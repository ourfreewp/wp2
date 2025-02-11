<?php
// Path: wp-content/plugins/wp2/src/Blocks/Settings/Permissions/init.php
namespace WP2\Blocks\Settings\Permissions;

/**
 * Manages block types and related functionalities.
 */
class Controller
{
    /**
     * Block bundles for reuse across methods.
     */
    private const BLOCK_BUNDLES = [
        'core_blocks' => [
            'core/paragraph',
            'core/image',
            'core/group',
        ],
        'core_editor_blocks' => [
            'core/heading',
            'core/list',
            'core/list-item',
            'core/quote',
        ],
        'core_embed_blocks' => [
            'core/embed',
        ],
        'core_form_blocks' => [
            'core/search',
        ],
        'core_navigation_blocks' => [
            'core/navigation',
            'core/navigation-link',
            'core/navigation-submenu',
        ],
        'core_button_blocks' => [
            'core/button',
            'core/buttons',
        ],
        'core_site_blocks' => [
            'core/template-part',
        ],
        'wp2_article_blocks' => [
            'wp2/article-header',
            'wp2/article-content',
            'wp2/article-footer',
        ],
        'wp2_broadcast_blocks' => [
            'wp2/broadcast-header',
            'wp2/broadcast-content',
            'wp2/broadcast-footer',
        ],
        'wp2_focus_blocks' => [
            'wp2/primary',
            'wp2/secondary',
        ],
        'wp2_navigation_blocks' => [
            'wp2/nav-primary',
            'wp2/nav-secondary',
        ],
        'wp2_item_blocks' => [
            'wp2/item-byline',
            'wp2/item-cover',
            'wp2/item-content',
            'wp2/item-dateline',
            'wp2/item-media',
            'wp2/item-meta',
            'wp2/item-photo',
            'wp2/item-share',
            'wp2/item-subtitle',
            'wp2/item-term',
            'wp2/item-title',
        ],
        'wp2_main_blocks' => [
            'wp2/main-content',
            'wp2/main-footer',
            'wp2/main-header',
        ],
        'wp2_query_blocks' => [
            'wp2/query-content',
            'wp2/query-footer',
            'wp2/query-header',
        ],
        'wp2_root_blocks' => [
            'wp2/root-header',
            'wp2/root-content',
            'wp2/root-footer',
            'wp2/root',
        ],
        'wp2_site_blocks' => [
            'wp2/site-alert',
            'wp2/site-brand',
            'wp2/site-item',
            'wp2/site-menu',
            'wp2/site-placement',
            'wp2/site-search',
        ],
        'wp2_misc_blocks' => [
            'wp2/stretched-link',
        ],
        'wp2_wiki_blocks' => [
            'wp2-wiki/readme',
            'wp2-wiki/nav-primary',
            'wp2-wiki/nav-secondary',
        ],
    ];

    /**
     * Filters the allowed block types based on context, editor, and post type.
     *
     * @param array|null $allowed_block_types Allowed block types.
     * @param object     $editor_context      Editor context.
     * @return array|null Modified allowed block types.
     */
    public function filter_allowed_block_types($allowed_block_types, $editor_context)
    {
        $allowed_blocks = [];

        // Incrementally merge blocks from modular methods.
        $allowed_blocks = array_merge(
            $allowed_blocks,
            $this->allow_blocks_by_role(),
            $this->allow_blocks_by_editor($editor_context->name ?? null),
            $this->allow_blocks_by_post_type($editor_context->post->post_type ?? null)
        );

        // Clean up the array (remove duplicates and reindex).
        return array_values(array_unique($allowed_blocks));
    }

    /**
     * Allows blocks based on user roles.
     *
     * @return array Blocks allowed for specific roles.
     */
    private function allow_blocks_by_role()
    {
        $user = wp_get_current_user();

        if (in_array('subscriber', $user->roles)) {
            return [];
        }

        if (in_array('administrator', $user->roles)) {
            return array_merge(
                self::BLOCK_BUNDLES['wp2_wiki_blocks']
            );
        }

        return self::BLOCK_BUNDLES['core_blocks'];
    }

    /**
     * Allows blocks based on the editor context.
     *
     * @param string|null $editor_name The editor name (e.g., 'core/edit-site').
     * @return array Blocks allowed in the given editor context.
     */
    private function allow_blocks_by_editor($editor_name)
    {
        if ($editor_name === 'core/edit-site') {
            return array_merge(
                self::BLOCK_BUNDLES['wp2_root_blocks'],
            );
        }

        if ($editor_name === 'core/edit-post') {
            return array_merge(
                self::BLOCK_BUNDLES['core_editor_blocks'],
            );
        }

        return [];
    }

    /**
     * Allows blocks based on the post type.
     *
     * @param string|null $post_type The post type being edited.
     * @return array Blocks allowed for the given post type.
     */
    private function allow_blocks_by_post_type($post_type)
    {
        if ($post_type === 'page') {
            return array_merge(
                self::BLOCK_BUNDLES['editor_core_blocks']
            );
        }

        if (in_array($post_type, ['post'])) {
            return array_merge(
                self::BLOCK_BUNDLES['core_blocks'],
                self::BLOCK_BUNDLES['embed_blocks']
            );
        }

        return self::BLOCK_BUNDLES['core_blocks'];
    }
}

/**
 * Initializes the BlockTypeController and hooks it to WordPress.
 */
function wp2_init_block_rules()
{
    $controller = new Controller();

    // Filter allowed block types.
    add_filter('allowed_block_types_all', [$controller, 'filter_allowed_block_types'], 10, 2);
}

// Hook initialization.
add_action('init', __NAMESPACE__ . '\\wp2_init_block_rules', 20);
