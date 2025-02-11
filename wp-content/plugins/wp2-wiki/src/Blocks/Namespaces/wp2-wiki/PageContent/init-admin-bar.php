<?php

namespace WP2_Wiki\Blocks\README;

use WP2\Helpers\Event\ActionScheduler\Controller as ActionScheduler;
use WP_Admin_Bar;

class AdminBarController
{

    /**
     * Textdomain for translations.
     *
     * @var string
     */
    private $textdomain = 'wp2-wiki';

    /**
     * String prefix used for hooks, callbacks, IDs, etc.
     *
     * @var string
     */
    private $prefix = 'wp2_wiki_';

    /**
     * Post type for READMEs.
     *
     * @var string
     */
    private $post_type = 'wp2_wiki_readme';

    /**
     * Hook used for "options" sync tasks.
     *
     * @var string
     */
    private $options_hook = 'wp2_wiki_readme_options_sync';

    /**
     * Callback used for "options" sync tasks.
     * 
     * @var string
     */
    private $options_callback = 'wp2_wiki_readme_options_response';

    /**
     * Hook used for "posts" sync tasks.
     *
     * @var string
     */
    private $posts_hook = 'wp2_wiki_readme_posts_sync';

    /**
     * Callback used for "posts" sync tasks.
     * 
     * @var string
     */
    private $posts_callback = 'wp2_wiki_readme_posts_response';

    /**
     * Constructor.
     * Sets up admin_bar_menu hook.
     */
    public function __construct()
    {
        // Add WP Admin Bar menu items
        add_action('admin_bar_menu', [$this, 'admin_bar_menu'], 100);
    }

    /**
     * Hook callback to add items to the Admin Bar only on the correct page.
     */
    public function admin_bar_menu(WP_Admin_Bar $admin_bar): void
    {
        if ($this->admin_bar_context()) {
            $this->handle_admin_bar_menu($admin_bar);
        }
    }

    /**
     * Adds READMEs parent and children nodes to the admin bar.
     */
    public function handle_admin_bar_menu(WP_Admin_Bar $admin_bar): void
    {
        $textdomain   = $this->textdomain;
        $parent_id    = $this->post_type . '_menu';

        $options_hook = $this->options_hook;
        $options_id   = $options_hook . '_submenu';

        $posts_hook   = $this->posts_hook;
        $posts_id     = $posts_hook . '_submenu';

        // Parent node
        $admin_bar->add_node([
            'id'    => $parent_id,
            'title' => 'README',
            'href'  => false,
            'meta'  => [
                'title' => __('READMEs', $this->textdomain),
            ],
        ]);

        // Refresh "options" sync node
        $admin_bar->add_node([
            'id'     => $options_id,
            'parent' => $parent_id,
            'title'  => __('Refresh Options', $textdomain),
            'href'   => $this->build_sync_url($options_hook),
            'meta'   => [
                'title' => __('Refresh README Options', $textdomain),
            ],
        ]);

        // Sync "posts" node
        $admin_bar->add_node([
            'id'     => $posts_id,
            'parent' => $parent_id,
            'title'  => __('Sync Posts', $textdomain),
            'href'   => $this->build_sync_url($posts_hook),
            'meta'   => [
                'title' => __('Sync README Posts', $textdomain),
            ],
        ]);
    }

    /**
     * Checks whether we're on the README post type listing screen.
     */
    private function admin_bar_context(): bool
    {
        return (
            isset($_GET['post_type'])
            && $_GET['post_type'] === $this->post_type
        );
    }

    /**
     * Builds the admin URL for triggering a sync,
     * e.g.: edit.php?post_type=wp2_wiki_readme&wp2_wiki_readme_options_sync=1
     */
    private function build_sync_url(string $hook): string
    {
        return admin_url(
            sprintf(
                'edit.php?post_type=%s&%s=1',
                urlencode($this->post_type),
                urlencode($hook)
            )
        );
    }
}
new AdminBarController();
