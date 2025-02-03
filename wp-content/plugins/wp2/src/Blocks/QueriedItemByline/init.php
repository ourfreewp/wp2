<?php
// Path: wp-content/plugins/wp2/src/Blocks/QueriedItemByline/init.php
namespace WP2\Blocks\QueriedItemByline;

use WP2\Helpers\ContextController;

/**
 * BylineController
 *
 * Dynamically generates an author byline for queried items based on context.
 * Supports options for prefix, suffix, and author link.
 *
 * @package OnTheWater
 */
class BylineController
{
    /**
     * Text domain for localization.
     */
    private const TEXT_DOMAIN = 'wp2';

    /**
     * Retrieves the author byline dynamically based on the context.
     *
     * @param array $args Contextual arguments for determining the byline.
     *                    - `id` (int): The ID of the post or queried item.
     *                    - `link` (bool): Whether to link the author name. Default true.
     *                    - `prefix` (string): Text to prepend to the author name. Default 'By '.
     *                    - `suffix` (string): Text to append to the author name. Default ''.
     * @return string The constructed byline HTML.
     */
    public static function get_byline(array $args = []): string
    {
        // Define default arguments.
        $defaults = [
            'id'     => null,
            'link'   => true,
            'prefix' => __('By ', self::TEXT_DOMAIN),
            'suffix' => '',
        ];
        $args = wp_parse_args($args, $defaults);

        // Determine the author ID.
        $author_id = get_post_field('post_author', $args['id']);
        if (!$author_id) {
            return '';
        }

        // Get the author's display name with a fallback to "Guest Author".
        $author_name = get_the_author_meta('display_name', $author_id);
        if (empty($author_name)) {
            $author_name = __('Guest Author', self::TEXT_DOMAIN);
        }

        // Construct the author name, optionally linking it.
        if ($args['link']) {
            $author_url = esc_url(get_author_posts_url($author_id));
            $author_name = sprintf(
                '<a href="%s" class="Byline-author">%s</a>',
                $author_url,
                esc_html($author_name)
            );
        } else {
            $author_name = sprintf(
                '<span class="Byline-author">%s</span>',
                esc_html($author_name)
            );
        }

        // Assemble the full byline with prefix and suffix.
        return sprintf(
            '<p class="Byline">%s%s%s</p>',
            esc_html($args['prefix']),
            $author_name,
            esc_html($args['suffix'])
        );
    }
}