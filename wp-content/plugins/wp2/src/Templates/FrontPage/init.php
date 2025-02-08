<?php

/**
 * Front Page Controller.
 *
 * Manages modifications and enhancements for the front page, including:
 * - Query customization
 * - Hooks and filters for additional features
 *
 */

namespace WP2\Themes\Templates\FrontPage;

use WP_Query;

/**
 * Class FrontPageController
 *
 * A controller for handling front-page modifications.
 */
class Controller
{

    /**
     * Post types to include in front page queries.
     *
     * @var array
     */
    private array $post_types;

    /**
     * Constructor.
     *
     * Initializes the class and registers hooks.
     */
    public function __construct()
    {
        $this->post_types = apply_filters('wp2_front_page_post_types', ['post']);

        add_action('pre_get_posts', [$this, 'modify_query']);
    }

    /**
     * Modifies the main query for the front page.
     *
     * Ensures only non-admin, main queries are modified.
     *
     * @param WP_Query $query The current query object.
     *
     * @return void
     */
    public function modify_query(WP_Query $query): void
    {
        if (is_admin() || ! $query->is_main_query() || ! is_front_page()) {
            return;
        }

        /**
         * Filter: `wp2_modify_front_page_query`
         *
         * Allows developers to modify the WP_Query instance before applying changes.
         *
         * @param WP_Query $query      The current WP_Query object.
         * @param array    $post_types Allowed post types for front page queries.
         */
        $query = apply_filters('wp2_modify_front_page_query', $query, $this->post_types);

        $query->set('post_type', $this->post_types);
    }
}

/**
 * Initialize the front page controller.
 *
 * This function instantiates the `FrontPageController` class
 * to ensure modifications are applied during runtime.
 *
 * @return void
 */
function wp2_init_front_page_controller(): void
{
    new Controller();
}

/**
 * Hooks into WordPress to initialize the front-page controller at the right moment.
 */
add_action('init', __NAMESPACE__ . '\wp2_init_front_page_controller', 30);
