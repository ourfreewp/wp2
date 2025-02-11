<?php
// Path: wp-content/plugins/wp2/src/Blocks/wp2/ItemContent/init.php
namespace WP2\Blocks\WP2\ItemContent;

use WP2\Helpers\Context\Controller as ContextController;

/**
 * ContentController
 *
 * Dynamically generates titles for queried items based on context.
 * Supports all WordPress templates including archives, single posts, pages,
 * taxonomy terms, authors, search results, and 404 pages.
 *
 * @package WP2
 */
class ContentController
{
    /**
     * Text domain for localization.
     */
    private const TEXT_DOMAIN = 'wp2';


    /**
     * Retrieves the title dynamically based on the context.
     *
     * @param array $args Contextual arguments for determining the title.
     * @return string The generated title.
     */
    public static function get_content(string $context, array $args): string
    {
        $type = $args['type'] ?? 'single_post';

        switch ($type) {
            case 'single_post':
                return self::get_single_post_content($context, $args);
            default:
                return '';
        }
    }

    /**
     * Retrieves the title for a single post.
     *
     * @param int $post_id The post ID to retrieve the title for.
     * @param string $post_type The post type to retrieve the title for.
     * @return string The title for the post.
     */
    private static function get_single_post_content(string $context, array $args): string
    {
        $post_id   = $args['post_id'] ?? null;
        $post_type = $args['post_type'] ?? null;

        if (!$post_id || !$post_type) {
            return '';
        }

        $post = get_post($post_id);

        if (!$post) {
            return '';
        }

        $content = $post->post_content ?? '';

        $content = apply_filters('the_content', $content);

        return apply_filters('wp2_title_single_post', $title, $post, $context);
    }
}
