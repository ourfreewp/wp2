<?php
// File: wp-content/plugins/wp2-directory/src/Helpers/Sync/init.php

namespace WP2_Directory\Helpers\Sync;

class Controller
{
    /**
     * Triggers the sync process for a given directory kind.
     *
     * Iterates over each listing directory found in the catalog
     * and processes its sync.
     *
     * @param string $kind The kind of directory (e.g., 'plugins').
     * @param string $dir    The full path to the catalog directory.
     * @return void
     */
    public function trigger_catalog_listings_sync($kind, $dir)
    {
        error_log("Triggering sync for directory kind: {$kind} in catalog: {$dir}");
        $dirs = $this->get_catalog_listing_directories($dir);
        if (empty($dirs)) {
            error_log("No listing directories found in catalog: {$dir}");
            return;
        }
        foreach ($dirs as $dir) {
            $this->handle_catalog_listing_sync_request($kind, $dir);
        }
    }

    /**
     * Retrieves all subdirectories (listings) within a catalog directory.
     *
     * @param string $catalog_dir The catalog directory path.
     * @return array List of listing directory paths.
     */
    protected function get_catalog_listing_directories($dir)
    {
        $dir = $this->trailingslashit($dir);
        $dirs = glob($dir . '*', GLOB_ONLYDIR);
        return $dirs ? $dirs : [];
    }

    /**
     * Processes the common sync steps for a single listing.
     *
     * Parses the listing’s block.json, assembles the common payload,
     * and then calls the kind‑specific sync method (e.g. sync_plugins_listing).
     *
     * @param string $dir    The full path to the listing directory.
     * @param string $kind The kind of directory (e.g., 'plugins').
     * @return void
     */
    protected function handle_catalog_listing_sync_request($kind, $dir)
    {
        // Parse the block.json to get the block data.
        $block_data = $this->parse_block_json($dir);

        if (is_wp_error($block_data)) {
            error_log("Error parsing block.json in {$dir}: " . $block_data->get_error_message());
            return;
        }

        $method = 'handle_' . $kind . '_sync';

        $sync_args = [
            'method' => $method,
            'dir' => $dir,
            'kind' => $kind,
            'block'  => $block_data,
        ];

        // Assemble the common payload.
        $payload = $this->prepare_catalog_listing_sync_payload($sync_args);

        if (method_exists($this, $method)) {
            $this->$method($payload);
        } else {
            error_log("Sync method for directory kind '{$kind}' does not exist.");
        }
    }

    /**
     * Prepares a common payload from block.json data.
     *
     * @param array $block_data The decoded block.json data.
     * @return array The common payload for the upsert.
     */
    protected function prepare_catalog_listing_sync_payload($sync_args)
    {
        $meta_prefix = 'wp2_catalog_' . $sync_args['kind'];

        $block_data   = $sync_args['block'];
        $post_title   = $block_data['title'];
        $post_excerpt = $block_data['description'] ?? '';
        $attributes   = $block_data['attributes'] ?? [];
        $blockstudio  = $block_data['blockstudio'] ?? [];
        $block_name   = $block_data['name'];
        $post_name    = sanitize_title($block_name);

        $block_attributes = wp_json_encode([
            'lock' => [
                'remove' => true,
                'move'   => true,
            ],
        ]);

        $post_content = sprintf(
            '<!-- wp:%s %s -->',
            esc_attr($block_name),
            esc_attr($block_attributes)
        );

        $meta_args = [
            'path'              => $sync_args['dir'],
            'block_name'        => $block_name,
            'block_data'        => $block_data,
            'block_attributes'  => $attributes,
            'block_blockstudio' => $blockstudio,
        ];

        $meta = $this->construct_meta_keys($meta_prefix, $meta_args);

        $sync_payload =  [
            'post_title'   => $post_title,
            'post_content' => $post_content,
            'post_excerpt' => $post_excerpt,
            'post_name'    => $post_name,
            'meta'         => $meta,
        ];

        return $sync_payload;
    }

    /**
     * Syncs a single listing for the 'plugins' kind.
     *
     * @param array $sync_payload The payload for the upsert.
     * @return void
     */
    protected function handle_plugins_sync($sync_payload)
    {
        $process_payload = $sync_payload;
        // if needed add custom logic for plugins
        $this->upsert_catalog_listing($process_payload);
    }

    /**
     * Upserts a catalog listing post.
     *
     * @param array $payload The payload for the upsert.
     * @return void
     */
    protected function upsert_catalog_listing($process_payload)
    {
        $post_id = null;
        $post_type = 'wp2_catalog_listing';
        $post_name = $process_payload['post_name'];

        $post_data = [
            'post_name'   => $post_name,
            'post_type'   => $post_type,
            'post_status' => 'publish',
            'post_title'   => $process_payload['post_title'],
            'post_content' => $process_payload['post_content'],
            'post_excerpt' => $process_payload['post_excerpt'],
        ];

        $post_meta = $process_payload['meta'];

        $existing_post = $this->get_existing_post_by_name($post_name, $post_type);

        if ($existing_post) {
            $post_id = $existing_post->ID;
            $post_data['ID'] = $post_id;
            wp_update_post($post_data);
        } else {
            $post_id = wp_insert_post($post_data);
        }

        if (is_wp_error($post_id)) {
            error_log("Error upserting catalog listing: " . $post_id->get_error_message());
            return;
        }

        $this->update_post_meta($post_id, $post_meta);
    }

    /**
     * Look for existing post by post_name
     * 
     * @param string $post_name The post name to search for.
     */
    protected function get_existing_post_by_name($post_name, $post_type = 'wp2_catalog_listing')
    {
        $args = [
            'name'        => $post_name,
            'post_type'   => $post_type,
            'post_status' => 'any',
            'numberposts' => 1,
        ];

        $posts = get_posts($args);

        if ($posts) {
            return $posts[0];
        }

        return null;
    }

    /**
     * Parses the block.json file from a listing directory.
     *
     * @param string $dir The listing directory.
     * @return array|\WP_Error The decoded JSON data or WP_Error on failure.
     */
    protected function parse_block_json($dir)
    {
        $block_json_path = $this->trailingslashit($dir) . 'block.json';
        if (!file_exists($block_json_path)) {
            return new \WP_Error('block_json_missing', "block.json not found in directory: {$dir}");
        }

        $json_contents = file_get_contents($block_json_path);
        $data = json_decode($json_contents, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new \WP_Error('json_decode_error', "Error decoding block.json: " . json_last_error_msg());
        }

        return $data;
    }

    /**
     * Ensures a string ends with a trailing slash.
     *
     * @param string $string The input string.
     * @return string The string with a trailing slash.
     */
    protected function trailingslashit($string)
    {
        return rtrim($string, '/\\') . '/';
    }

    /**
     * Construct arguments for the upsert operation.
     * @param string $prefix The prefix for the arguments.
     * @param array  $args   The input arguments.
     * @return array The constructed arguments.
     */
    protected function construct_meta_keys($prefix, $args)
    {
        $meta = [];
        foreach ($args as $key => $value) {
            $meta[$prefix . '_' . $key] = $value;
        }
        return $meta;
    }

    /**
     * Update meta for a post.
     * 
     * @param int   $post_id The post ID.
     * @param array $meta    The meta data to update.
     */
    protected function update_post_meta($post_id, $meta)
    {
        foreach ($meta as $key => $value) {
            update_post_meta($post_id, $key, $value);
        }
    }
}


new Controller();
