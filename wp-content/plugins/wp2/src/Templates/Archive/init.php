<?php
// Path: wp-content/plugins/wp2/src/Themes/Templates/Archive/init.php
namespace WP2\Themes\Templates\Archive;

use WP_Query;

/**
 * Handles modifications to the main query for archive pages.
 */
class Controller
{

    /**
     * Post types to include in archive queries.
     *
     * @var array
     */
    private array $post_types;

    /**
     * Constructor to initialize the class and register hooks.
     */
    public function __construct()
    {
        $this->post_types = apply_filters('wp2_archive_post_types', ['post']);

        add_action('pre_get_posts', [$this, 'modify_archive_query']);
    }

    /**
     * Modifies the main query for archive pages.
     *
     * Ensures only non-admin, main queries for archive pages are modified.
     *
     * @param WP_Query $query The current query object.
     *
     * @return void
     */
    public function modify_archive_query(WP_Query $query): void
    {
        if (is_admin() || ! $query->is_main_query() || ! is_archive()) {
            return;
        }

        /**
         * Filter: `wp2_modify_archive_query`
         *
         * Allows developers to modify the archive query before applying changes.
         *
         * @param WP_Query $query      The current WP_Query object.
         * @param array    $post_types Allowed post types for archive queries.
         */
        $query = apply_filters('wp2_modify_archive_query', $query, $this->post_types);

        $query->set('post_type', $this->post_types);
    }
}

/**
 * Initialize the archive controller.
 *
 * This function instantiates the `Controller` class
 * to ensure modifications are applied during runtime.
 *
 * @return void
 */
function wp2_init_archive_controller(): void
{
    new Controller();
}

/**
 * Hooks into WordPress to initialize the archive controller at the right moment.
 */
add_action('init', __NAMESPACE__ . '\wp2_init_archive_controller', 30);
