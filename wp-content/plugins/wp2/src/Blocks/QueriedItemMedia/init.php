<?php
// Path: wp-content/plugins/wp2/src/Blocks/QueriedItemMedia/init.php
namespace WP2\Blocks\QueriedItemMedia;

use WP2\Helpers\ContextController;

/**
 * MediaController
 *
 * Dynamically retrieves and formats the media for queried items based on context.
 * Currently focuses on featured images, with support for customization and extensibility.
 *
 * @package OnTheWater
 */
class MediaController
{
    /**
     * Text domain for localization.
     */
    private const TEXT_DOMAIN = 'wp2';

    /**
     * Retrieves the media dynamically based on the context.
     *
     * @param array $args Contextual arguments for determining the media.
     *                    - `id` (int): The ID of the post or queried item.
     *                    - `size` (string): The image size to retrieve. Default 'large'.
     *                    - `attributes` (array): Additional attributes for the <img> tag.
     *                    - `fallback` (bool): Whether to use a default fallback image. Default true.
     * @return string The constructed media HTML.
     */
    public static function get_media(array $args = []): string
    {
        // Define default arguments.
        $defaults = [
            'id'         => null,
            'size'       => 'large',
            'attributes' => ['class' => 'object-fit-cover'],
            'fallback'   => true,
        ];
        $args = wp_parse_args($args, $defaults);

        // Determine the context using ContextController.
        $context = ContextController::determine_context($args);

        // Allow customization of the context via filter.
        $context = apply_filters('wp2_media_context', $context, $args);

        // Generate the media based on the determined context.
        return self::generate_media($context, $args);
    }

    /**
     * Generates the media HTML based on the context and arguments.
     *
     * @param string $context The determined context.
     * @param array  $args    The contextual arguments.
     * @return string The constructed media HTML.
     */
    private static function generate_media(string $context, array $args): string
    {
        // Handle different contexts for media generation.
        switch ($context) {
            case ContextController::CONTEXT_SINGLE:
            case ContextController::CONTEXT_SINGLE_NEWS:
                return self::get_featured_image($args['id'], $args['size'], $args['attributes'], $args['fallback']);

            case ContextController::CONTEXT_POST_TYPE_ARCHIVE:
            case ContextController::CONTEXT_GENERIC:
                return self::get_archive_featured_image($args['size'], $args['attributes'], $args['fallback']);

            case ContextController::CONTEXT_TAXONOMY:
                return self::get_taxonomy_media($args['id'], $args['size'], $args['attributes'], $args['fallback']);

            default:
                return self::get_fallback_media($args['size'], $args['attributes']);
        }
    }

    /**
     * Retrieves the featured image for a single post.
     *
     * @param int    $post_id    The ID of the post.
     * @param string $size       The image size to retrieve.
     * @param array  $attributes Additional attributes for the <img> tag.
     * @param bool   $fallback   Whether to use a fallback if no image is available.
     * @return string The constructed image HTML.
     */
    private static function get_featured_image(int $post_id, string $size, array $attributes, bool $fallback): string
    {
        $thumbnail = get_the_post_thumbnail($post_id, $size, $attributes);

        if (!$thumbnail && $fallback) {
            return self::get_fallback_media($size, $attributes);
        }

        return $thumbnail ?: '';
    }

    /**
     * Retrieves a featured image for an archive page.
     *
     * @param string $size       The image size to retrieve.
     * @param array  $attributes Additional attributes for the <img> tag.
     * @param bool   $fallback   Whether to use a fallback if no image is available.
     * @return string The constructed image HTML.
     */
    private static function get_archive_featured_image(string $size, array $attributes, bool $fallback): string
    {
        // Example: Logic to retrieve a featured image for an archive.
        $archive_page_id = get_option('page_for_posts');
        if ($archive_page_id) {
            return self::get_featured_image($archive_page_id, $size, $attributes, $fallback);
        }

        return $fallback ? self::get_fallback_media($size, $attributes) : '';
    }

    /**
     * Retrieves media for a taxonomy term.
     *
     * @param int    $term_id    The ID of the taxonomy term.
     * @param string $size       The image size to retrieve.
     * @param array  $attributes Additional attributes for the <img> tag.
     * @param bool   $fallback   Whether to use a fallback if no image is available.
     * @return string The constructed media HTML.
     */
    private static function get_taxonomy_media(int $term_id, string $size, array $attributes, bool $fallback): string
    {
        $image_id = get_term_meta($term_id, '_thumbnail_id', true);

        if ($image_id) {
            return wp_get_attachment_image($image_id, $size, false, $attributes);
        }

        return $fallback ? self::get_fallback_media($size, $attributes) : '';
    }

    /**
     * Retrieves a default fallback image.
     *
     * @param string $size       The image size to retrieve.
     * @param array  $attributes Additional attributes for the <img> tag.
     * @return string The constructed fallback image HTML.
     */
    private static function get_fallback_media(string $size, array $attributes): string
    {
        // Example: Replace with a valid fallback image URL.
        $fallback_url = apply_filters('wp2_fallback_media_url', 'https://via.placeholder.com/800x600');

        $attributes_str = '';
        foreach ($attributes as $key => $value) {
            $attributes_str .= sprintf('%s="%s" ', esc_attr($key), esc_attr($value));
        }

        return sprintf(
            '<img src="%s" alt="%s" %s/>',
            esc_url($fallback_url),
            esc_attr__('Fallback Image', self::TEXT_DOMAIN),
            trim($attributes_str)
        );
    }
}