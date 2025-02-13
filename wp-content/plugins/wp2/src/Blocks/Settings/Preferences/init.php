<?php
// Path: wp-content/plugins/wp2/src/Blocks/Settings/Preferences/init.php
namespace WP2\Blocks\Settings\Preferences;

/**
 * Manages block editor settings and related functionalities.
 */
class Controller
{
    /**
     * Sets the post lock timeout window.
     *
     * @param int $limit The default time limit in seconds.
     * @return int Modified time limit in seconds.
     */
    public function set_post_lock_window($limit)
    {
        return 30; // Set the timeout to 30 seconds.
    }

    /**
     * Restrict code editor to administrators.
     *
     * @param array $settings The editor settings.
     * @return array Modified settings with code editing disabled for non-admins.
     */
    public function restrict_code_editor_for_non_admins($settings)
    {
        if (!current_user_can('activate_plugins')) {
            $settings['codeEditingEnabled'] = false;
        }
        return $settings;
    }

    /**
     * Disable Openverse media category in the block editor.
     *
     * @param array $settings The editor settings.
     * @return array Modified settings with Openverse disabled.
     */
    public function disable_openverse_media_category($settings)
    {
        $settings['enableOpenverseMediaCategory'] = false;
        return $settings;
    }

    /**
     * Disable the block directory.
     * 
     * @return void
     */
    public function disable_block_directory()
    {
        remove_action('enqueue_block_editor_assets', 'wp_enqueue_editor_block_directory_assets');
    }
}

/**
 * Initializes the BlockEditorController and hooks it to WordPress.
 */
function wp2_block_editor_settings()
{
    $controller = new Controller();

    // Set post lock timeout window.
    add_filter('wp_check_post_lock_window', [$controller, 'set_post_lock_window']);

    // Restrict code editor to administrators.
    add_filter('block_editor_settings_all', [$controller, 'restrict_code_editor_for_non_admins']);

    // Disable Openverse media category.
    add_filter('block_editor_settings_all', [$controller, 'disable_openverse_media_category']);

    // Disable block directory.
    add_action('init', [$controller, 'disable_block_directory']);
}

add_action('init', __NAMESPACE__ . '\\wp2_block_editor_settings', 20);
