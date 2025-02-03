<?php
// Path: wp-content/plugins/wp2/src/Blocks/QueriedItemTitle/init.php
namespace WP2\Blocks\QueriedItemTitle;

use WP2\Helpers\ContextController;

/**
 * TitleController
 *
 * Dynamically generates titles for queried items based on context.
 * Supports all WordPress templates including archives, single posts, pages,
 * taxonomy terms, authors, search results, and 404 pages.
 *
 * @package OnTheWater
 */
class TitleController
{
    /**
     * Text domain for localization.
     */
    private const TEXT_DOMAIN = 'wp2';

    /**
     * Post type suffixes for titles.
     */
    private static $post_type_suffixes = [
        'post'       => ' Articles',
        'video'      => ' Videos',
        'forecasts'  => ' Reports',
        'news'       => ' News',
    ];

    /**
     * Default titles for specific contexts.
     */
    private static $default_titles = [
        ContextController::CONTEXT_404       => 'Page Not Found',
        ContextController::CONTEXT_SEARCH    => 'Search Results for: %s',
        ContextController::CONTEXT_GENERIC   => 'Explore Our Content',
    ];

    /**
     * Retrieves the title dynamically based on the context.
     *
     * @param array $args Contextual arguments for determining the title.
     * @return string The generated title.
     */
    public static function get_title(array $args = []): string
    {
        // Determine context using ContextController.
        $context = ContextController::determine_context($args);

        // Allow customization of context via filter.
        $context = apply_filters('wp2_title_context', $context, $args);

        // Generate the title based on the determined context.
        return self::generate_title($context, $args);
    }

    /**
     * Generates the title based on the context and arguments.
     *
     * @param string $context The determined context.
     * @param array  $args    The contextual arguments.
     * @return string The generated title.
     */
    private static function generate_title(string $context, array $args): string
    {
        switch ($context) {
            case ContextController::CONTEXT_404:
                return __(self::$default_titles[ContextController::CONTEXT_404], self::TEXT_DOMAIN);

            case ContextController::CONTEXT_SEARCH:
                $search_query = get_search_query();
                return !empty($search_query)
                    ? sprintf(
                        __(self::$default_titles[ContextController::CONTEXT_SEARCH], self::TEXT_DOMAIN),
                        esc_html($search_query)
                    )
                    : __(self::$default_titles[ContextController::CONTEXT_GENERIC], self::TEXT_DOMAIN);

            case ContextController::CONTEXT_TAXONOMY:
                return single_term_title('', false);

            case ContextController::CONTEXT_AUTHOR:
                return get_the_author();

            case ContextController::CONTEXT_POST_TYPE_ARCHIVE:
                return post_type_archive_title('', false);

            case ContextController::CONTEXT_SINGLE:
                return get_the_title($args['id']);

            case ContextController::CONTEXT_GENERIC:
                return __(self::$default_titles[ContextController::CONTEXT_GENERIC], self::TEXT_DOMAIN);

            default:
                return get_the_archive_title();
        }
    }

    /**
     * Prints a formatted title, including optional suffixes for post types.
     *
     * @param array $args Optional. Contextual arguments for formatting the title.
     * @return void
     */
    public static function print_title(array $args = []): void
    {
        // Get the title and post type suffix if applicable.
        $title = self::get_title($args);
        $post_type = get_query_var('post_type');
        $post_type_suffix = self::$post_type_suffixes[$post_type] ?? '';

        // Format and output the title.
        $format = '<h1 class="Page-title">%1$s%2$s</h1>';
        $formatted_title = sprintf($format, esc_html($title), esc_html($post_type_suffix));

        echo wp_kses($formatted_title, wp_kses_allowed_html('post'));
    }
}