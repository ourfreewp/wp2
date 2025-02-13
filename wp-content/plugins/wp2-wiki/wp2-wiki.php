<?php

/**
 * Plugin Name: WP2 Wiki
 * Description: 
 * Version: 1.0
 * Author: WP2S
 *
 * @package WP2_Wiki
 */

namespace WP2_Wiki;

use WP2_Daemon\WP2_Studio\Handlers\Instance\Controller as StudioController;

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Class Plugin
 *
 * Main class for the WP2 Wiki plugin. This class defines required constants,
 * registers directories for block types, and initializes the integration with Blockstudio.
 *
 * @package WP2_Wiki
 */
class Module
{

    /**
     * Associative array of constants to define.
     *
     * @var array
     */
    private $constants = [];

    /**
     * Directories to be registered with the Studio Controller.
     *
     * @var array
     */
    private $directories = [
        WP2_WIKI_DIR . '/src/Assets',
        WP2_WIKI_DIR . '/src/Blocks/Namespaces/wp2-wiki',
        WP2_WIKI_DIR . '/src/Helpers',
        WP2_WIKI_DIR . '/src/Templates',
        WP2_WIKI_DIR . '/src/Types',
    ];

    /**
     * Studio controller instance.
     *
     * @var StudioController|null
     */
    private $studio_controller;

    /**
     * Plugin constructor.
     */
    public function __construct()
    {
        // Set constant values using plugin_dir_path and get_template_directory functions.
        $this->constants['WP2_WIKI_DIR'] = plugin_dir_path(__FILE__);
        $this->constants['WP2_WIKI_DIR'] = plugin_dir_url(__FILE__);
        $this->constants['WP2_THEME_DIR'] = get_template_directory();
        $this->constants['WP2_THEME_URL'] = get_template_directory_uri();

        // Define the constants.
        $this->define_constants();

        // Hook into WordPress init action.
        add_action('init', [$this, 'init_module']);
    }


    //

    /**
     * Defines required constants.
     *
     * @return void
     */
    private function define_constants(): void
    {
        foreach ($this->constants as $name => $value) {
            if (! defined($name)) {
                define($name, $value);
            }
        }
    }

    /**
     * Initializes the plugin functionality.
     *
     * This method instantiates the Studio Controller and registers directories
     * containing block definitions. It is called on the 'init' hook.
     *
     * @return void
     */
    public function init_module(): void
    {
        // Instantiate the Studio Controller if it exists.
        if (class_exists('\WP2_Daemon\WP2_Studio\Handlers\Instance\Controller')) {
            $this->studio_controller = new StudioController();

            // Register each directory.
            foreach ($this->directories as $directory) {
                $this->studio_controller->register_directory($directory);
            }
        } else {
            error_log('[WP2 Wiki] StudioController class not found. Please ensure WP2 Studio is loaded.');
        }
    }
}

new Module();
