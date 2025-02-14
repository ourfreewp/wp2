<?php
// File: wp-content/plugins/wp2-directory/src/Syncs/Listings/Integrations/init.php

namespace WP2_Directory\Syncs\Listings\Integrations;

use WP2_Directory\Helpers\Sync\Controller as SyncController;
use WP2\Helpers\Event\ActionScheduler\Controller as ActionScheduler;

class Controller
{
    /**
     * Sync Hook.
     *
     * @var string
     */
    private $hook = 'wp2_directory_trigger_integrations_sync';

    /**
     * Sync Callback.
     *
     * @var string
     */
    private $callback = 'wp2_directory_execute_integrations_sync';

    /**
     * Post type for integration listings.
     *
     * @var string
     */
    private $post_type = 'wp2_catalog_listing';

    /**
     * Sync Key
     * 
     * @var string
     */
    private $kind = 'integrations';

    /**
     * Sync Controller
     * 
     * @var SyncController
     */
    private $sync_helper;

    /**
     * Location
     * 
     * @var string
     */
    private $catalog_dir = WP_CONTENT_DIR . '/plugins/wp2-directory/src/Catalogs/Integrations';

    /**
     * Constructor.
     *
     * Hooks the sync handler to WordPress’ admin_init action.
     *
     * @return void
     */
    public function __construct()
    {
        add_action('admin_init', [$this, 'handle_sync']);
    }

    /**
     * Handles sync requests based on the current request context.
     *
     * Checks if the current request context matches the expected hook parameters
     * and, if so, schedules the async sync action.
     *
     * @return void
     */
    public function handle_sync(): void
    {
        if ($this->check_context()) {

            $as_controller = new ActionScheduler();

            $admin_notice = [
                'type'            => 'info',
                'message'         => 'Syncing Integrations Listings...',
                'is_dismissible'  => true,
                'sync_param'      => $this->hook,
            ];

            $as_controller::register_async_action(
                $this->hook,
                __NAMESPACE__ . '\\' . $this->callback,
                [],
                null,
                true,
                $admin_notice
            );
        }
    }

    /**
     * Checks if the current request context matches the expected hook parameters.
     *
     * Determines if the proper admin page is being viewed (based on the post type)
     * and if the specific sync hook query parameter is set.
     *
     * @return bool True if the context matches, false otherwise.
     */
    private function check_context(): bool
    {
        $sync_param = 'wp2_sync_' . $this->kind;
        return (
            isset($_GET['post_type'])
            && $_GET['post_type'] === $this->post_type
            && isset($_GET[$sync_param])
            && $_GET[$sync_param] === '1'
        );
    }

    /**
     * Executes the actual sync process.
     *
     * This method should be called by the scheduled action callback.
     *
     * @return void
     */
    public function execute_sync(): void
    {
        // Use the object's properties directly.
        $catalog = $this->catalog_dir;
        $kind = $this->kind;

        // Instantiate the sync controller and trigger the sync process.
        (new SyncController())->trigger_catalog_listings_sync($kind, $catalog);
    }
}

// Instantiate the integration sync controller.
new Controller();

/**
 * Scheduled callback function for integration sync.
 *
 * This function is hooked to the async action.
 */
function wp2_directory_execute_integrations_sync(): void
{
    $controller = new Controller();
    $controller->execute_sync();
}

add_action('wp2_directory_trigger_integrations_sync', __NAMESPACE__ . '\\wp2_directory_execute_integrations_sync');
