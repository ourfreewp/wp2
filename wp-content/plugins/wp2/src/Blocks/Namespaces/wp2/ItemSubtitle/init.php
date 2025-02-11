<?php
// Path: wp-content/plugins/wp2/src/Blocks/wp2/ItemSubtitle/init.php
namespace WP2\Blocks\WP2\ItemSubtitle;

/**
 * SubtitleController
 *
 * Dynamically generates titles for queried items based on context.
 * Supports all WordPress templates including archives, single posts, pages,
 * taxonomy terms, authors, search results, and 404 pages.
 *
 * @package WP2
 */
class SubtitleController
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
    public static function get_subtitle(string $context, array $args): string
    {
        $type = $args['type'] ?? 'single_post';

        switch ($type) {
            case 'single_post':
                return self::get_single_subtitle($context, $args);
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
    private static function get_single_subtitle(string $context, array $args): string
    {
        $post_id   = $args['post_id'] ?? null;
        $post_type = $args['post_type'] ?? null;

        $posts = get_posts([
            'post_type' => $post_type,
            'include'   => [$post_id]
        ]);

        $post = $posts[0] ?? null;

        if (!$post) {
            return '';
        }

        $excerpt = $post->post_excerpt ?? 'Test';

        return $excerpt;
    }
}
