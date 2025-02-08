<?php
// Path: wp-content/plugins/wp2/src/Filters/init-block-patterns.php
namespace WP2\Filters;

use WP_Block_Patterns_Registry;

/**
 * Manages block patterns and related functionalities.
 */
class BlockPatternController
{
    private $prefix = 'wp2';

    /**
     * Constructor to initialize hooks and theme support.
     */
    public function __construct()
    {
        // Restrict block patterns through the REST API.
        add_filter('rest_dispatch_request', [$this, 'restrict_block_patterns'], 12, 3);

        // Disable loading remote block patterns.
        add_filter('should_load_remote_block_patterns', '__return_false');

        // Manage theme support for block patterns.
        add_action('after_setup_theme', [$this, 'remove_core_block_pattern_support']);
    }

    /**
     * Restricts block patterns by unregistering patterns not matching specific criteria.
     *
     * @param mixed  $dispatch_result The result to send to the client.
     * @param object $request         The current request object.
     * @param string $route           The requested route.
     * @return mixed The modified or original dispatch result.
     */
    public function restrict_block_patterns($dispatch_result, $request, $route)
    {
        if (strpos($route, '/wp/v2/block-patterns/patterns') !== false) {
            $this->unregister_non_wp2_patterns();
        }

        return $dispatch_result;
    }

    /**
     * Unregisters block patterns not prefixed with "wp2".
     */
    private function unregister_non_wp2_patterns()
    {
        $patterns = WP_Block_Patterns_Registry::get_instance()->get_all_registered();

        if (!empty($patterns)) {
            foreach ($patterns as $pattern) {
                if (strpos($pattern['name'], $this->prefix) !== 0) {
                    unregister_block_pattern($pattern['name']);
                }
            }
        }
    }

    /**
     * Removes support for core block patterns.
     */
    public function remove_core_block_pattern_support()
    {
        remove_theme_support('core-block-patterns');
    }
}

/**
 * Initializes the BlockPatternController and hooks it to WordPress.
 */
function wp2_block_pattern_settings()
{
    new BlockPatternController();
}

// Hook initialization.
add_action('init', function () {
    wp2_block_pattern_settings();
}, 99);
