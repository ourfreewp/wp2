<?php
// Path: wp-content/plugins/wp2/src/Helpers/Context/init.php
namespace WP2\Helpers\Context;

/**
 * ContextController
 *
 * Dynamically determines the context for queried items based on the WordPress
 * environment and optional input arguments.
 *
 * @package WP2\Helpers
 */
class Controller
{

    /**
     * Context keys for determination.
     */
    public const CONTEXT_404 = '404';
    public const CONTEXT_FRONT_PAGE = 'front_page';
    public const CONTEXT_SEARCH = 'search';
    public const CONTEXT_GENERIC = 'generic';
    public const CONTEXT_TAXONOMY = 'taxonomy';
    public const CONTEXT_AUTHOR = 'author';
    public const CONTEXT_POST_TYPE_ARCHIVE = 'post_type_archive';
    public const CONTEXT_SINGLE_NEWS = 'single_news';
    public const CONTEXT_SINGLE = 'single';
    public const CONTEXT_PAGE_MAGAZINE = 'page_magazine';

    /**
     * Default arguments for determining context.
     *
     * @var array
     */
    private static array $default_args = [
        'template'       => '',
        'object_type'    => '',
        'object_subtype' => '',
        'id'             => null,
        'parent_id'      => null,
        'is_editor'      => false,
        'current_user'   => null,
        'is_preview'     => false,
        'slug'           => null,
        'status'         => null,
        'is_paged'       => false,
        'custom'         => null,
    ];

    /**
     * Constructor.
     *
     * Hooks into WordPress admin bar to display the context.
     */
    public function __construct()
    {
        add_action('admin_bar_menu', [$this, 'add_context_to_admin_bar'], 999);
    }

    /**
     * Determines the context for the queried item.
     *
     * This method uses WordPress conditional functions and optional arguments
     * to determine the most appropriate context for a given query.
     *
     * @param array $args Optional. Additional context arguments.
     * @return string The determined context.
     */
    public static function determine_context(array $args = []): string
    {
        $args = wp_parse_args($args, self::$default_args);

        if (is_404()) {
            return self::CONTEXT_404;
        }

        if (is_front_page()) {
            return self::CONTEXT_FRONT_PAGE;
        }

        if (is_search()) {
            return self::CONTEXT_SEARCH;
        }

        if (is_tax() || $args['template'] === 'taxonomy') {
            return self::CONTEXT_TAXONOMY;
        }

        if (is_author() || $args['object_type'] === 'user') {
            return self::CONTEXT_AUTHOR;
        }

        if (is_post_type_archive()) {
            return self::CONTEXT_POST_TYPE_ARCHIVE;
        }

        if (is_singular('news') || ($args['template'] === 'single' && $args['object_subtype'] === 'news')) {
            return self::CONTEXT_SINGLE_NEWS;
        }

        if (is_singular() || $args['template'] === 'single') {
            return self::CONTEXT_SINGLE;
        }

        if ($args['template'] === 'page' && $args['object_subtype'] === 'magazine') {
            return self::CONTEXT_PAGE_MAGAZINE;
        }

        if ($args['is_preview']) {
            return 'preview';
        }

        if ($args['is_paged']) {
            return 'paged';
        }

        if (!empty($args['custom'])) {
            return $args['custom'];
        }

        if ($args['is_editor']) {
            return self::CONTEXT_GENERIC;
        }

        return self::CONTEXT_GENERIC;
    }

    /**
     * Adds the current context to the WordPress admin bar.
     *
     * @param \WP_Admin_Bar $wp_admin_bar The WordPress admin bar object.
     */
    public function add_context_to_admin_bar(\WP_Admin_Bar $wp_admin_bar): void
    {
        if (!is_admin_bar_showing()) {
            return;
        }

        $context = self::determine_context();
        $parent_id = 'wp2-context';

        // Add the main context menu
        $wp_admin_bar->add_menu([
            'id'    => $parent_id,
            'title' => 'Context (' . $context . ')',
            'href'  => false,
        ]);

        // System pages
        $system_pages = [
            'front_page'     => get_option('page_on_front') ? get_permalink(get_option('page_on_front')) : home_url('/'),
            '404'            => 'error-404',
            'search_results' => home_url('/?s=example'),
        ];

        foreach ($system_pages as $key => $page) {
            $wp_admin_bar->add_menu([
                'parent' => $parent_id,
                'id'     => "{$parent_id}-system-{$key}",
                'title'  => 'System: ' . ucfirst(str_replace('_', ' ', $key)),
                'href'   => $page,
            ]);
        }

        // Post type archives
        foreach (get_post_types(['public' => true], 'objects') as $post_type) {
            if ($archive_link = get_post_type_archive_link($post_type->name)) {
                $wp_admin_bar->add_menu([
                    'parent' => $parent_id,
                    'id'     => "{$parent_id}-archive-{$post_type->name}",
                    'title'  => 'Archive: ' . $post_type->label,
                    'href'   => $archive_link,
                ]);
            }
        }

        // Single post examples
        foreach (get_post_types(['public' => true], 'objects') as $post_type) {
            $posts = get_posts([
                'post_type'      => $post_type->name,
                'posts_per_page' => 1,
            ]);

            if (!empty($posts)) {
                $wp_admin_bar->add_menu([
                    'parent' => $parent_id,
                    'id'     => "{$parent_id}-single-{$post_type->name}",
                    'title'  => 'Single: ' . $post_type->label,
                    'href'   => get_permalink($posts[0]->ID),
                ]);
            }
        }

        // Taxonomy terms
        foreach (get_taxonomies(['public' => true], 'objects') as $taxonomy) {
            $terms = get_terms([
                'taxonomy'   => $taxonomy->name,
                'hide_empty' => false,
                'number'     => 1,
            ]);

            if (!empty($terms) && !is_wp_error($terms)) {
                $term = reset($terms);
                $wp_admin_bar->add_menu([
                    'parent' => $parent_id,
                    'id'     => "{$parent_id}-taxonomy-{$taxonomy->name}",
                    'title'  => 'Taxonomy: ' . ucfirst($taxonomy->name),
                    'href'   => get_term_link($term),
                ]);
            }
        }
    }
}

/**
 * Initialize the queried item context.
 *
 * @return void
 */
function wp2_init_queried_item_context(): void
{
    new Controller();
}

/**
 * Hook initialization.
 */
add_action('init', __NAMESPACE__ . '\wp2_init_queried_item_context', 21);
