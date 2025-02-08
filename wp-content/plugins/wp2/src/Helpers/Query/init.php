<?php
// Path: wp-content/plugins/wp2/src/Helpers/Query/init.php
namespace WP2\Helpers\Query;

use WP_Query;

/**
 * Class QueryController
 *
 * A helper class for managing and caching custom WordPress queries.
 */
class Controller
{

    /**
     * Transient expiration time in seconds (24 hours).
     */
    private const EXPIRATION = 24 * HOUR_IN_SECONDS;

    /**
     * Constructor to hook into the `options.php` page.
     */
    public function __construct()
    {
        add_action('load-options.php', [$this, 'preload_queries']);
    }

    /**
     * Preloads all necessary queries to ensure their transients are set.
     *
     * This helps optimize database queries by setting transients in advance.
     *
     * @return void
     */
    public function preload_queries(): void
    {
        /**
         * Action Hook: `wp2_preload_queries`
         *
         * Allows developers to preload custom queries.
         */
        do_action('wp2_preload_queries');
    }

    /**
     * Retrieves a query result by name.
     *
     * Looks up a method dynamically based on the provided query name.
     *
     * @param string $name        The name of the query method.
     * @param array  $overrides   Optional overrides for query arguments.
     * @param string $return_type Type of result: 'ids' for post IDs or 'objects' for full objects.
     *
     * @return array|null Array of results or null if the query method doesn't exist.
     */
    public function get_query(string $name, array $overrides = [], string $return_type = 'objects'): ?array
    {
        $method = "get_{$name}_query";

        if (!method_exists($this, $method)) {
            return null; // Method doesn't exist.
        }

        $posts = $this->$method($overrides);

        return ($return_type === 'ids')
            ? array_map(static fn($post) => $post->ID, $posts)
            : $posts;
    }

    /**
     * Deletes all transients for registered queries.
     *
     * This should be triggered after content updates to ensure cached queries are refreshed.
     *
     * @return void
     */
    public function delete_all_transients(): void
    {
        /**
         * Action Hook: `wp2_delete_all_query_transients`
         *
         * Allows developers to delete query transients.
         */
        do_action('wp2_delete_all_query_transients');
    }

    /**
     * Gets or sets a transient. If the transient doesn't exist, it runs the callback to generate the data.
     *
     * @param string   $transient_key The transient key.
     * @param callable $callback      The callback to generate the transient data.
     *
     * @return mixed The transient data.
     */
    private function get_or_set_transient(string $transient_key, callable $callback)
    {
        $data = get_transient($transient_key);

        if ($data === false) {
            $data = call_user_func($callback);
            set_transient($transient_key, $data, self::EXPIRATION);
        }

        return $data;
    }
}

/**
 * Initializes the query controller.
 *
 * This ensures query modifications and caching optimizations are applied.
 *
 * @return void
 */
function wp2_init_query_controller(): void
{
    new Controller();
}

// Hook initialization.
add_action('init', __NAMESPACE__ . '\wp2_init_query_controller', 30);
