<?php
// Path: wp-content/plugins/wp2/src/Filters/init-block-types.php
namespace WP2\Filters;

/**
 * Manages block types and related functionalities.
 */
class BlockTypeController
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
            'wp2/broadcast-content',
        ],
        'wp2_navigation_blocks' => [
            'wp2/navbar-primary',
            'wp2/navbar-secondary',
        ],
        'wp2_player_blocks' => [
            'wp2/player-video',
        ],
        'wp2_item_blocks' => [
            'wp2/item-byline',
            'wp2/item-cover',
            'wp2/item-dateline',
            'wp2/item-media',
            'wp2/item-meta',
            'wp2/item-photo',
            'wp2/item-share',
            'wp2/item-subtitle',
            'wp2/item-term',
            'wp2/item-title',
        ],
        'wp2_query_blocks' => [
            'wp2/query-content',
            'wp2/query-description',
            'wp2/query-footer',
            'wp2/query-header',
            'wp2/query-meta',
            'wp2/query-name',
        ],
        'wp2_singular_blocks' => [
            'wp2/single-action',
            'wp2/single-alert',
            'wp2/single-article',
            'wp2/single-card',
            'wp2/single-form',
            'wp2/single-icon',
            'wp2/single-item',
            'wp2/single-placement',
            'wp2/single-section',
            'wp2/single-shortcode',
        ],
        'wp2_site_blocks' => [
            'wp2/site-alert',
            'wp2/site-action',
            'wp2/site-brand',
            'wp2/site-footer',
            'wp2/site-header',
            'wp2/site-search',
            'wp2/site-menu',
            'wp2/site-root',
        ],
        'wp2_misc_blocks' => [
            'wp2/stretched-link',
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
                self::BLOCK_BUNDLES['core_blocks'],
                self::BLOCK_BUNDLES['core_button_blocks'],
                self::BLOCK_BUNDLES['core_editor_blocks'],
                self::BLOCK_BUNDLES['core_embed_blocks'],
                self::BLOCK_BUNDLES['core_form_blocks'],
                self::BLOCK_BUNDLES['core_navigation_blocks'],
                self::BLOCK_BUNDLES['core_site_blocks'],
                self::BLOCK_BUNDLES['wp2_article_blocks'],
                self::BLOCK_BUNDLES['wp2_broadcast_blocks'],
                self::BLOCK_BUNDLES['wp2_navigation_blocks'],
                self::BLOCK_BUNDLES['wp2_player_blocks'],
                self::BLOCK_BUNDLES['wp2_item_blocks'],
                self::BLOCK_BUNDLES['wp2_query_blocks'],
                self::BLOCK_BUNDLES['wp2_singular_blocks'],
                self::BLOCK_BUNDLES['wp2_site_blocks'],
                self::BLOCK_BUNDLES['wp2_misc_blocks']
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

        if (in_array($post_type, ['article', 'forecasts'])) {
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
function wp2_block_type_settings()
{
    $block_type_controller = new BlockTypeController();

    // Filter allowed block types.
    add_filter('allowed_block_types_all', [$block_type_controller, 'filter_allowed_block_types'], 10, 2);
}

// Hook initialization.
add_action('init', function () {
    wp2_block_type_settings();
}, 22);
