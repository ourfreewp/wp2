<?php
// Path: wp-content/plugins/wp2-wiki/src/Helpers/Markdown/init.php
namespace WP2_Wiki\Helpers\Markdown;

include_once WP2_WIKI_DIR . '/src/vendor/autoload.php';

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\DescriptionList\DescriptionListExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\TableOfContents\TableOfContentsExtension;
use League\CommonMark\Extension\TaskList\TaskListExtension;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\DefaultAttributes\DefaultAttributesExtension;
use League\CommonMark\Extension\DisallowedRawHtml\DisallowedRawHtmlExtension;
use League\CommonMark\Extension\Embed\EmbedExtension;
use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension;
use League\CommonMark\Extension\Footnote\FootnoteExtension;
use League\CommonMark\Extension\Mention\MentionExtension;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\SmartPunct\SmartPunctExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Parser\MarkdownParser;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Node\Block\Heading;


use WP_HTML_Tag_Processor;

class Controller
{

    /**
     * Returns the Markdown configuration.
     *
     * @return array
     */
    public static function config(): array
    {
        return [
            'renderer' => [
                'block_separator' => "\n",
                'inner_separator' => "\n",
                'soft_break'      => "\n",
            ],
            'commonmark' => [
                'enable_em'              => true,
                'enable_strong'          => true,
                'use_asterisk'           => true,
                'use_underscore'         => true,
                'unordered_list_markers' => ['-', '*', '+'],
            ],
            'html_input'         => 'escape',
            'allow_unsafe_links' => false,
            'max_nesting_level'  => PHP_INT_MAX,
            'slug_normalizer'    => [
                'max_length' => 255,
            ],
        ];
    }

    /**
     * Sets up the Markdown environment with the required extensions.
     *
     * @param array $config
     * @return Environment
     */
    public static function environment(array $config = []): Environment
    {
        $environment = new Environment($config);
        $environment->addExtension(self::commonmark_core_extension());
        $environment->addExtension(self::github_flavored_markdown_extension());
        $environment->addExtension(self::heading_permalink_extension());
        $environment->addExtension(self::table_of_contents_extension());
        $environment->addExtension(self::task_list_extension());
        return $environment;
    }

    /**
     * Processes the raw README Markdown and returns parsed data.
     *
     * Expected keys in the returned array:
     * - path: The original file path.
     * - post_title: The level‑1 header extracted from the Markdown (or a fallback if missing).
     * - raw: The original raw Markdown content.
     * - html: The converted HTML.
     *
     * @param string $raw_file_content The raw Markdown content.
     * @param string $path       The file path.
     * @return array Associative array with keys 'path', 'post_title', 'raw', 'html'
     */
    public static function process_readme(string $raw_file_content, string $path): array
    {
        $config      = self::config();
        $environment = self::environment($config);
        $converter   = self::markdown_converter($environment);
        $html        = self::get_html($converter, $raw_file_content);
        $name        = self::get_name($path);
        $title_value = self::get_title_value($raw_file_content, $path);
        $title       = self::get_title($title_value, $name);
        $toc         = self::get_toc($html);
        $raw         = trim($raw_file_content);

        return [
            'option_name' => 'wp2_wiki_readme_' . md5($path),
            'path'        => $path,
            'post_title'  => $title,
            'post_name'   => $name,
            'toc'         => $toc,
            'raw'         => $raw,
            'html'        => $html,
        ];
    }




    /**
     * Converts Markdown content using the given MarkdownConverter.
     *
     * @param MarkdownConverter $converter The Markdown converter.
     * @param string $content The raw Markdown content.
     * @return string The parsed HTML output.
     */
    public static function get_html(MarkdownConverter $converter, string $content): string
    {
        return $converter->convert($content)->getContent();
    }

    /**
     * Returns the title if found; otherwise, returns a fallback title derived from the file path.
     *
     * @param string|null $title The extracted title (or null if not found).
     * @param string $path The file path.
     * @return string The title or a fallback.
     */
    public static function get_title(?string $title, string $path): string
    {
        return $title ? $title : self::convert_name_to_title($path);
    }


    /**
     * Converts a file path into a human‐readable title.
     *
     * If the file path ends with "README.md" (case-insensitive), the parent directory’s basename is used.
     * Otherwise, the basename of the file is used.
     * Dashes and underscores are replaced with spaces, and the string is converted to title case.
     *
     * @param string $path The file path.
     * @return string The fallback title.
     */
    public static function convert_name_to_title(string $path): string
    {
        if (strcasecmp(basename($path), 'README.md') === 0) {
            $fallback = basename(dirname($path));
        } else {
            $fallback = basename($path);
        }
        // Replace dashes and underscores with spaces.
        $fallback = str_replace(['-', '_'], ' ', $fallback);
        return ucwords($fallback);
    }

    /**
     * Generates a slug-like name for the file from its path.
     *
     * @param string $path The file path.
     * @return string The generated name.
     */
    public static function get_name(string $path): string
    {
        // Remove "README.md" (case-insensitive) from the path.
        $path = str_ireplace('README.md', '', $path);
        // Remove any trailing slash.
        $path = rtrim($path, '/');
        // Normalize double slashes.
        $path = str_replace('//', '/', $path);
        // Replace slashes with double-dashes.
        $slug = str_replace('/', '--', $path);
        $slug = strtolower($slug);
        return $slug === '' ? 'wiki' : $slug;
    }



    /**
     * Extracts the table of contents (TOC) from the HTML using a custom tag processor.
     *
     * @param string $html The HTML content.
     * @return string The inner HTML of the TOC element, or an empty string if not found.
     */
    public static function get_toc(string $html): string
    {
        if (! class_exists('WP_HTML_Tag_Processor')) {
            return '';
        }
        $tag_processor = new WP_HTML_Tag_Processor($html);
        if ($tag_processor->next_tag(['tag' => 'ul', 'class_name' => 'table-of-contents'])) {
            return $tag_processor->get_updated_html();
        }
        return '';
    }
    /**
     * Extracts the level‑1 heading (title) from the Markdown content.
     *
     * This method scans the Markdown content for an ATX‑style heading that starts with a single “#”
     * and returns the trimmed heading text.
     *
     * @param string $raw_file_content The raw Markdown content.
     * @param string $path The file path.
     * @return string|null The level‑1 heading text if found, or null.
     */
    public static function get_title_value(string $raw_file_content, string $path): ?string
    {
        $cursor = new Cursor($raw_file_content);
        self::skip_newlines($cursor);
        while (!$cursor->isAtEnd()) {
            $state = $cursor->saveState();
            if ($cursor->getCurrentCharacter() === '#') {
                $hash_count = 0;
                while (!$cursor->isAtEnd() && $cursor->getCurrentCharacter() === '#') {
                    $hash_count++;
                    $cursor->advance();
                }
                if ($hash_count === 1) {
                    self::skip_whitespace($cursor);
                    $title = '';
                    while (!$cursor->isAtEnd() && $cursor->getCurrentCharacter() !== "\n") {
                        $title .= $cursor->getCurrentCharacter();
                        $cursor->advance();
                    }
                    return trim($title);
                }
            }
            $cursor->restoreState($state);
            self::skip_line($cursor);
        }
        return null;
    }

    /**
     * Creates a Markdown Parser.
     *
     * @param Environment $environment The Markdown environment.
     * @return MarkdownParser
     */
    public static function markdown_parser(Environment $environment): MarkdownParser
    {
        return new MarkdownParser($environment);
    }

    /**
     * Creates a Markdown Converter.
     *
     * @param Environment $environment The Markdown environment.
     * @return MarkdownConverter
     */
    public static function markdown_converter(Environment $environment): MarkdownConverter
    {
        return new MarkdownConverter($environment);
    }

    /**
     * CommonMark Core Extension.
     *
     * @return CommonMarkCoreExtension
     */
    public static function commonmark_core_extension(): CommonMarkCoreExtension
    {
        return new CommonMarkCoreExtension();
    }

    /**
     * Github Flavored Markdown Extension.
     *
     * @return GithubFlavoredMarkdownExtension
     */
    public static function github_flavored_markdown_extension(): GithubFlavoredMarkdownExtension
    {
        return new GithubFlavoredMarkdownExtension();
    }

    /**
     * Heading Permalink Extension.
     *
     * @return HeadingPermalinkExtension
     */
    public static function heading_permalink_extension(): HeadingPermalinkExtension
    {
        return new HeadingPermalinkExtension();
    }

    /**
     * Table of Contents Extension.
     *
     * @return TableOfContentsExtension
     */
    public static function table_of_contents_extension(): TableOfContentsExtension
    {
        return new TableOfContentsExtension();
    }

    /**
     * Task List Extension.
     *
     * @return TaskListExtension
     */
    public static function task_list_extension(): TaskListExtension
    {
        return new TaskListExtension();
    }

    /**
     * Description List Extension.
     *
     * @return DescriptionListExtension
     */
    public static function description_list_extension(): DescriptionListExtension
    {
        return new DescriptionListExtension();
    }

    /**
     * Attributes Extension.
     *
     * @return AttributesExtension
     */
    public static function attributes_extension(): AttributesExtension
    {
        return new AttributesExtension();
    }

    /**
     * Autolink Extension.
     *
     * @return AutolinkExtension
     */
    public static function autolink_extension(): AutolinkExtension
    {
        return new AutolinkExtension();
    }

    /**
     * Default Attributes Extension.
     *
     * @return DefaultAttributesExtension
     */
    public static function default_attributes_extension(): DefaultAttributesExtension
    {
        return new DefaultAttributesExtension();
    }

    /**
     * Disallowed Raw HTML Extension.
     *
     * @return DisallowedRawHtmlExtension
     */
    public static function disallowed_raw_html_extension(): DisallowedRawHtmlExtension
    {
        return new DisallowedRawHtmlExtension();
    }

    /**
     * Embed Extension.
     *
     * @return EmbedExtension
     */
    public static function embed_extension(): EmbedExtension
    {
        return new EmbedExtension();
    }

    /**
     * External Link Extension.
     *
     * @return ExternalLinkExtension
     */
    public static function external_link_extension(): ExternalLinkExtension
    {
        return new ExternalLinkExtension();
    }

    /**
     * Footnote Extension.
     *
     * @return FootnoteExtension
     */
    public static function footnote_extension(): FootnoteExtension
    {
        return new FootnoteExtension();
    }

    /**
     * Mention Extension.
     *
     * @return MentionExtension
     */
    public static function mention_extension(): MentionExtension
    {
        return new MentionExtension();
    }

    /**
     * Strikethrough Extension.
     *
     * @return StrikethroughExtension
     */
    public static function strikethrough_extension(): StrikethroughExtension
    {
        return new StrikethroughExtension();
    }

    /**
     * Table Extension.
     *
     * @return TableExtension
     */
    public static function table_extension(): TableExtension
    {
        return new TableExtension();
    }

    /**
     * Smart Punct Extension.
     *
     * @return SmartPunctExtension
     */
    public static function smart_punct_extension(): SmartPunctExtension
    {
        return new SmartPunctExtension();
    }


    /**
     * Skips all consecutive newline characters.
     *
     * @param Cursor $cursor
     * @return void
     */
    public static function skip_newlines(Cursor $cursor): void
    {
        while (!$cursor->isAtEnd() && $cursor->getCurrentCharacter() === "\n") {
            $cursor->advance();
        }
    }

    /**
     * Skips all whitespace (spaces and tabs) from the current position.
     *
     * @param Cursor $cursor
     * @return void
     */
    public static function skip_whitespace(Cursor $cursor): void
    {
        while (!$cursor->isAtEnd() && ctype_space($cursor->getCurrentCharacter())) {
            $cursor->advance();
        }
    }

    /**
     * Advances the cursor to the end of the current line.
     *
     * @param Cursor $cursor
     * @return void
     */
    public static function skip_to_end_of_line(Cursor $cursor): void
    {
        while (!$cursor->isAtEnd() && $cursor->getCurrentCharacter() !== "\n") {
            $cursor->advance();
        }
    }

    /**
     * Skips to the beginning of the next line.
     *
     * @param Cursor $cursor
     * @return void
     */
    public static function skip_line(Cursor $cursor): void
    {
        self::skip_to_end_of_line($cursor);
        if (!$cursor->isAtEnd() && $cursor->getCurrentCharacter() === "\n") {
            $cursor->advance();
        }
    }
}
