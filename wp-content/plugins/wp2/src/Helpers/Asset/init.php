<?php
// Path: wp-content/themes/wp2/src/Helpers/Asset/init.php
namespace WP2\Helpers\Asset;

/**
 * Class AssetController
 *
 * Manages local theme assets, providing utility methods for fetching and enriching asset metadata.
 */
class Controller
{

    /**
     * Base directory for theme assets.
     *
     * @var string
     */
    private static string $base_dir;

    /**
     * Base URL for theme assets.
     *
     * @var string
     */
    private static string $base_url;

    /**
     * Initializes the base directory and URL for assets.
     *
     * Ensures paths are set once and prevents redundant calls.
     *
     * @return void
     */
    private static function initialize(): void
    {
        if (!isset(self::$base_dir) || !isset(self::$base_url)) {
            self::$base_dir = get_template_directory() . '/assets/';
            self::$base_url = get_template_directory_uri() . '/assets/';
        }
    }

    /**
     * Retrieves asset information.
     *
     * @param string $folder_name Folder name inside the assets directory (e.g., 'images').
     * @param string $file_name   File name without extension (e.g., 'background-pattern').
     * @param string $file_type   File extension (e.g., 'png').
     *
     * @return array|null Asset metadata or null if the file doesn't exist.
     */
    public static function get_asset_payload(string $folder_name, string $file_name, string $file_type): ?array
    {
        self::initialize();

        // Sanitize input values.
        $folder_name = sanitize_text_field($folder_name);
        $file_name   = sanitize_text_field($file_name);
        $file_type   = sanitize_text_field($file_type);

        // Construct file path and URL.
        $file_path = trailingslashit(self::$base_dir . $folder_name) . $file_name . '.' . $file_type;
        $file_url  = trailingslashit(self::$base_url . $folder_name) . $file_name . '.' . $file_type;

        // Validate file existence.
        if (!file_exists($file_path)) {
            return null;
        }

        return [
            'type'      => $folder_name, // Folder name used as type.
            'name'      => $file_name,   // File name without extension.
            'extension' => $file_type,   // File extension.
            'url'       => $file_url,    // Public URL.
            'path'      => $file_path,   // Absolute file path.
        ];
    }

    /**
     * Enriches asset metadata with additional properties.
     *
     * @param array $asset_payload  The basic asset payload.
     * @param array $additional_data Optional array of additional metadata.
     *
     * @return array|null Enriched asset payload or null if the input is invalid.
     */
    public static function enrich_asset_payload(array $asset_payload, array $additional_data = []): ?array
    {
        if (empty($asset_payload)) {
            return null;
        }

        // Default metadata enrichment.
        $defaults = [
            'alt'        => ucfirst(str_replace('-', ' ', $asset_payload['name'])), // Generate default alt text.
            'title'      => ucfirst(str_replace('-', ' ', $asset_payload['name'])), // Generate default title.
            'created_at' => file_exists($asset_payload['path']) ? date('Y-m-d H:i:s', filemtime($asset_payload['path'])) : null, // File modification timestamp.
        ];

        return array_merge($asset_payload, $defaults, $additional_data);
    }
}
