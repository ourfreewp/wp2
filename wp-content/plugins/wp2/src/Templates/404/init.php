<?php
// Path: wp-content/plugins/wp2/src/Templates/404/init.php
/**
 * Error 404 Controller.
 *
 * Manages custom behaviors for 404 pages, including:
 * - Disabling permalink guessing.
 *
 */

namespace WP2\Themes\Templates\Error404;

/**
 * Class ErrorController
 *
 * Handles custom logic for WordPress 404 pages.
 */
class Controller
{

    /**
     * Constructor.
     *
     * Initializes the class and registers hooks.
     */
    public function __construct()
    {
        $this->disable_permalink_guessing();
    }

    /**
     * Disable permalink guessing for 404 pages.
     *
     * This prevents WordPress from attempting to guess and redirect
     * to similar permalinks when a page is not found.
     *
     * @return void
     */
    private function disable_permalink_guessing(): void
    {
        add_filter('do_redirect_guess_404_permalink', '__return_false');
    }
}

/**
 * Initialize the Error 404 controller.
 *
 * This function instantiates the `ErrorController` class
 * to ensure modifications are applied during runtime.
 *
 * @return void
 */
function wp2_init_themes_templates_error404_controller(): void
{
    new Controller();
}

add_action('init', function () {
    wp2_init_themes_templates_error404_controller();
}, 21);
