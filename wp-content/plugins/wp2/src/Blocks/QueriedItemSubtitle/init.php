<?php
// Path: wp-content/plugins/wp2/src/Blocks/QueriedItemSubtitle/init.php
namespace WP2\Blocks\QueriedItemSubtitle;

use WP_HTML_Tag_Processor; // For HTML tag processing.
use WP2\Helpers\ContextController; // For centralized context determination.

/**
 * SubtitleController
 *
 * Dynamically generates subtitles for queried items based on context.
 * Supports archives, taxonomies, authors, posts, pages, and custom post types.
 *
 * @package OnTheWater
 */
class SubtitleController
{
    /**
     * Text domain for localization.
     */
    private const TEXT_DOMAIN = 'wp2';

    /**
     * Default strings for subtitles by context.
     */
    private static $default_strings = [
        ContextController::CONTEXT_404           => 'Page not found. Please try searching for something else.',
        ContextController::CONTEXT_SEARCH        => 'Search Results for: %s',
        ContextController::CONTEXT_GENERIC       => 'Explore our latest content and updates.',
        'editor_fallback'                     => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit aliquam purus ac libero ultricies aliquam.',
    ];

    /**
     * Retrieves the subtitle (description or excerpt) based on the given context.
     *
     * @param array $args Contextual arguments for determining the subtitle.
     * @return string The processed subtitle or an empty string if none is applicable.
     */
    public static function get_subtitle(array $args = []): string
    {
        // Use ContextController to determine the context.
        $context = ContextController::determine_context($args);

        // Allow customization of context via filter.
        $context = apply_filters('wp2_determine_context', $context, $args);

        // Generate and return the subtitle based on context.
        return self::generate_subtitle($context, $args);
    }

    /**
     * Generates the subtitle based on the context and arguments.
     *
     * @param string $context The determined context.
     * @param array  $args    The contextual arguments.
     * @return string The generated subtitle.
     */
    private static function generate_subtitle(string $context, array $args): string
    {
        $subtitle = '';

        switch ($context) {
            case ContextController::CONTEXT_404:
                $subtitle = __(self::$default_strings[ContextController::CONTEXT_404], self::TEXT_DOMAIN);
                break;

            case ContextController::CONTEXT_FRONT_PAGE:
                $front_page_id = get_option('page_on_front');
                $subtitle = $front_page_id ? get_the_excerpt($front_page_id) : '';
                break;

            case ContextController::CONTEXT_SEARCH:
                $search_query = get_search_query();
                $subtitle = !empty($search_query)
                    ? sprintf(__(self::$default_strings[ContextController::CONTEXT_SEARCH], self::TEXT_DOMAIN), esc_html($search_query))
                    : '';
                break;

            case ContextController::CONTEXT_TAXONOMY:
                $subtitle = term_description($args['id']);
                break;

            case ContextController::CONTEXT_AUTHOR:
                $user = get_user_by('ID', $args['id']);
                $subtitle = $user->description ?? '';
                break;

            case ContextController::CONTEXT_POST_TYPE_ARCHIVE:
                $post_type = get_post_type_object($args['object_subtype']);
                if ($post_type) {
                    $archive_slug = $post_type->archive_slug ?? $post_type->name;
                    $archive_page = get_page_by_path($archive_slug);
                    $subtitle = $archive_page->post_excerpt ?? '';
                }
                break;

            case ContextController::CONTEXT_SINGLE_NEWS:
                $subtitle = get_post_meta($args['id'], '_custom_news_subtitle', true);
                break;

            case ContextController::CONTEXT_SINGLE:
                $subtitle = get_the_excerpt($args['id']);
                break;

            case ContextController::CONTEXT_PAGE_MAGAZINE:
                $subtitle = get_post_meta($args['id'], '_custom_magazine_subtitle', true);
                break;

            default:
                $subtitle = __(self::$default_strings[ContextController::CONTEXT_GENERIC], self::TEXT_DOMAIN);
                break;
        }

        return self::process_subtitle_html($subtitle, $args['is_editor']);
    }

    /**
     * Processes and sanitizes the subtitle HTML.
     *
     * @param string $subtitle The subtitle to process.
     * @param bool   $is_editor Whether the context is the editor view.
     * @return string The processed subtitle.
     */
    private static function process_subtitle_html(string $subtitle, bool $is_editor): string
    {
        if (empty($subtitle)) {
            return $is_editor
                ? __(self::$default_strings['editor_fallback'], self::TEXT_DOMAIN)
                : '';
        }

        $processor = new WP_HTML_Tag_Processor($subtitle);
        if ($processor->next_tag('p')) {
            $processor->add_class('m-0');
        }

        return $processor->get_updated_html();
    }
}