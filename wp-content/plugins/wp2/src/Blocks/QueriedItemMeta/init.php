<?php
// Path: wp-content/plugins/wp2/src/Blocks/QueriedItemMeta/init.php
namespace WP2\Blocks\QueriedItemMeta;

use WP2\Helpers\ContextController;

/**
 * MetaController
 *
 * Dynamically retrieves and formats metadata for queried items based on the provided meta key.
 *
 * @package OnTheWater
 */
class MetaController
{
    /**
     * Text domain for localization.
     */
    private const TEXT_DOMAIN = 'wp2';

    /**
     * Retrieves metadata for a queried item based on the provided meta key.
     *
     * @param array $args Contextual arguments for retrieving metadata.
     *                    - `id` (int): The ID of the post or queried item.
     *                    - `meta_key` (string): The meta key to retrieve.
     *                    - `default` (mixed): Default value if the meta key is not found. Default ''.
     *                    - `prefix` (string): Text to prepend to the metadata. Default ''.
     *                    - `suffix` (string): Text to append to the metadata. Default ''.
     *                    - `format_callback` (callable|null): A callback to format the metadata. Default null.
     * @return string The constructed metadata HTML or the default value if not found.
     */
    public static function get_meta(array $args = []): string
    {
        // Define default arguments.
        $defaults = [
            'id'             => null,
            'meta_key'       => '',
            'default'        => '',
            'prefix'         => '',
            'suffix'         => '',
            'format_callback' => null,
        ];
        $args = wp_parse_args($args, $defaults);

        // Ensure a valid meta key is provided.
        if (empty($args['meta_key'])) {
            return '';
        }

        // Retrieve the metadata.
        $meta_value = get_post_meta($args['id'], $args['meta_key'], true);

        // If no metadata is found, return the default value.
        if (empty($meta_value)) {
            $meta_value = $args['default'];
        }

        // Optionally format the metadata using a callback.
        if (is_callable($args['format_callback'])) {
            $meta_value = call_user_func($args['format_callback'], $meta_value, $args);
        }

        // Construct the metadata HTML with prefix and suffix.
        return sprintf(
            '<span class="Meta-item">%s%s%s</span>',
            esc_html($args['prefix']),
            esc_html($meta_value),
            esc_html($args['suffix'])
        );
    }
}