<?php
// Path: wp-content/plugins/wp2-wiki/src/Blocks/wp2-wiki/PageContent/init.php
namespace WP2_Wiki\Blocks\PageContent;

use WP_Filesystem;

use WP2\Helpers\Event\ActionScheduler\Controller as ActionScheduler;

use WP2_Wiki\Helpers\Markdown\Controller as Parser;

/**
 * Handles scanning for README files, stashing them in options,
 * and creating/updating "wp2_wiki_readme" posts from those files.
 */
class Controller
{
    /**
     * Paths (relative to ABSPATH) of README files found in scanning.
     *
     * @var string[]
     */
    private $readme_paths = [];

    /**
     * Our custom post type for README objects.
     *
     * @var string
     */
    private $post_type = 'wp2_wiki_readme';

    /**
     * Prefix used when storing README data in options.
     *
     * @var string
     */
    private $option_prefix = 'wp2_wiki_readme_';

    /**
     * Plugin textdomain for translations.
     *
     * @var string
     */
    private $textdomain = 'wp2-wiki';

    /**
     * The Action Scheduler hook for "options" sync tasks.
     *
     * @var string
     */
    private $options_hook = 'wp2_wiki_readme_options_sync';

    /**
     * The Action Scheduler hook for "posts" sync tasks.
     *
     * @var string
     */
    private $posts_hook = 'wp2_wiki_readme_posts_sync';

    /**
     * Parser instance for converting Markdown to HTML.
     * 
     * @var Parser
     */
    private $parser;


    /**
     * Constructor.
     *
     * Initializes the Parser instance, defines README file paths,
     * and hooks the sync handler to WordPress’ admin_init action.
     *
     * @return void
     */
    public function __construct()
    {
        add_action('admin_init', [$this, 'handle_syncs']);
    }


    /**
     * Prepares the option data payload by processing the raw README content.
     *
     * Uses the parser instance to convert the raw file content into an array of processed data.
     *
     * @param string      $path             The file path.
     * @param string|null $raw_file_content The raw content of the README file.
     * @return array An associative array containing keys: post_name, post_title, html, raw.
     */
    private function prepare_option_data(string $path, ?string $raw_file_content): array
    {
        $this->parser = new Parser();
        $results = $this->parser->process_readme($raw_file_content, $path) ?: [];

        return $results;
    }
    /**
     * Prepares the post data array used for creating or updating a README post.
     *
     * Expects the option data payload to contain 'post_title' and 'post_name'.
     *
     * @param string $path        The file path.
     * @param mixed  $option_data The option data payload (expected to be an array).
     * @return array The post data array.
     */
    private function prepare_post_data(string $path)
    {

        $post_type = $this->post_type;

        $data = $this->get_option_data($path);

        $post_title = $data['post_title'];
        $html       = $data['html'];
        $raw        = $data['raw'];
        $toc        = $data['toc'];
        $post_name  = $data['post_name'];
        $path       = $data['path'];

        $attributes    = wp_json_encode([
            'lock' => [
                'move'   => true,
                'remove' => true,
            ],
            'align' => 'full',
        ]);
        $post_content = sprintf(
            "<!-- wp:wp2-wiki/readme %s /-->",
            $attributes
        );

        $args = [
            'post_title'   => $post_title,
            'post_status'  => 'publish',
            'post_type'    => $post_type,
            'post_name'    => $post_name,
            'post_content' => $post_content,
        ];

        $path = '/' . ltrim(str_replace('README.md', '', $path), '/');

        $meta = [
            '_wp2_wiki_readme_path' => $path,
            '_wp2_wiki_readme_html' => $html,
            '_wp2_wiki_readme_toc'  => $toc,
            '_wp2_wiki_readme_raw'  => $raw,
        ];

        $post_data = array_merge($args, ['meta' => $meta]);


        return $post_data;
    }


    /**
     * Synchronizes a single README post.
     *
     * Retrieves the post data via update_post(), then updates its meta data.
     *
     * @param string $filepath The file path.
     * @return void
     */
    private function update_post(string $filepath): void
    {
        $post_data = $this->prepare_post_data($filepath);

        $meta_data = $post_data['meta'];
        $post_name = $post_data['post_name'];

        $post_id = $this->insert_post($post_data);

        foreach ($meta_data as $key => $value) {
            update_post_meta($post_id, $key, $value);
        }
    }

    /**
     * Inserts a new post or updates an existing one if a matching slug is found.
     *
     * @param array $post_data The post data array.
     * @return int The ID of the inserted or updated post.
     */
    private function insert_post(array $post_data): int
    {
        $post_type = $this->post_type;

        $post_name = !empty($post_data['post_name']) ? 'wp2-wiki-' . $post_data['post_name'] : 'wp2-wiki';

        $post_args = [
            'post_title'   => $post_data['post_title'],
            'post_type'    => $post_type,
            'post_status'  => $post_data['post_status'],
            'post_name'    => $post_name,
            'post_content' => $post_data['post_content'],
        ];

        $existing_posts = get_posts([
            'post_type'   => $post_type,
            'name'        => $post_name,
            'numberposts' => 1,
        ]);

        if (!empty($existing_posts)) {
            $post_id = $existing_posts[0]->ID;
            $post_args['ID'] = $post_id;
            wp_update_post($post_args);
        } else {
            $post_id = wp_insert_post($post_args);
        }

        return $post_id;
    }

    /**
     * Synchronizes a single README file's content to its corresponding option.
     *
     * If the file content is empty, the option is deleted.
     *
     * @param string      $path              The file path.
     * @param string|null $raw_file_content  The raw file content.
     * @return void
     */
    private function update_option(string $path, ?string $raw_file_content): void
    {

        $payload = $this->prepare_option_data($path, $raw_file_content);

        $option_name = $this->get_option_name($path);

        delete_option($option_name);

        update_option($option_name, $payload);
    }

    /**
     * Retrieves stored option data for a given README file path.
     *
     * @param string $path The file path.
     * @return array An array of option data if found, or an empty array otherwise.
     */
    private function get_option_data(string $path): array
    {
        $option_name = $this->get_option_name($path);
        $option      = get_option($option_name);

        $option = maybe_unserialize($option);

        if (!is_array($option)) {
            $option = [];
        }

        return $option;
    }

    // get_option_name
    private function get_option_name(string $path): string
    {
        return $this->option_prefix . md5($path);
    }

    /**
     * Scans defined directories for README files and populates the readme_paths property.
     *
     * Combines manual paths with those found via glob scanning.
     *
     * @return void
     */
    private function define_paths(): array
    {
        $base_paths = [
            ABSPATH => [
                'README.md',
                'wp-content/README.md',
                'wp-content/mu-plugins/README.md',
                'wp-content/plugins/README.md',
                'wp-content/themes/README.md',
                'wp-content/themes/wp2/README.md',
                'wp-content/uploads/README.md',
            ],
            WP2_CORE_DIR => [
                'README.md',
                'src/README.md',
                'src/Assets/README.md',
                'src/Blocks/README.md',
                'src/Blocks/README.md',
                'src/Elements/README.md',
                'src/Helpers/README.md',
                'src/Templates/README.md',
            ],
            WP2_NEW_DIR => [
                'README.md',
                'src/README.md',
            ],
            WP2_WIKI_DIR => [
                'README.md',
                'src/README.md',
                'src/Assets/README.md',
                'src/Blocks/README.md',
                'src/Helpers/README.md',
                'src/Templates/README.md',
                'src/Types/README.md',
            ],
        ];

        $dynamic_paths = [
            ABSPATH => [
                'wp-content/themes/wp2/*/README.md',
                'wp-content/themes/wp2/*/assets/*/README.md',
            ],
            WP2_CORE_DIR => [
                'src/Assets/*/README.md',
                'src/Blocks/*/README.md',
                'src/Blocks/Namespaces/*/README.md',
                'src/Blocks/Namespaces/core/*/README.md',
                'src/Blocks/Namespaces/wp2/*/README.md',
                'src/Blocks/Settings/*/README.md',
                'src/Elements/*/README.md',
                'src/Helpers/*/README.md',
                'src/Templates/*/README.md',
            ],
            WP2_WIKI_DIR => [
                'src/Assets/*/README.md',
                'src/Blocks/*/README.md',
                'src/Blocks/Namespaces/wp2-wiki/*/README.md',
                'src/Helpers/*/README.md',
                'src/Templates/*/README.md',
                'src/Types/*/README.md',
            ],
        ];

        $paths = [];

        // Add static paths
        foreach ($base_paths as $base => $subpaths) {
            foreach ($subpaths as $subpath) {
                $paths[] = rtrim($base, '/') . '/' . ltrim($subpath, '/');
            }
        }

        // Add dynamic paths from scanned directories
        foreach ($dynamic_paths as $base => $patterns) {
            foreach ($patterns as $pattern) {
                $scanned_paths = $this->scan_dir(rtrim($base, '/') . '/' . ltrim($pattern, '/'));
                $paths = array_merge($paths, $scanned_paths);
            }
        }

        // Validate and clean up paths
        return $this->validate_paths($paths);
    }
    /**
     * Validates the readme_paths property, ensuring all paths are valid and exist.
     *
     * This function performs the following steps for each path:
     * 1. Normalize double slashes.
     * 2. Remove the ABSPATH prefix.
     * 3. Remove any trailing "README.md" from the path.
     * 4. If the resulting path is empty, set it to '/'.
     * 5. Ensure the path ends with a single slash.
     * 6. Append "README.md" to the path.
     * 7. Finally, only return paths where the file exists.
     *
     * @param string[] $paths The raw paths.
     * @return string[] The validated paths.
     */
    public function validate_paths(array $paths): array
    {
        $paths = array_map(function ($path) {
            // Normalize any double slashes.
            $path = str_replace('//', '/', $path);
            // Remove ABSPATH from the beginning.
            $path = str_replace(ABSPATH, '', $path);
            // Remove a trailing "README.md" if it exists.
            $path = preg_replace('/README\.md$/i', '', $path);
            // If the path is empty, default to the root.
            $path = trim($path) === '' ? '/' : $path;
            // Ensure the path ends with a single slash.
            $path = rtrim($path, '/') . '/';
            // Append "README.md".
            return $path . 'README.md';
        }, $paths);

        // Filter out any paths that do not exist.
        $paths = array_values(array_filter($paths, function ($path) {
            return file_exists(ABSPATH . $path);
        }));

        return $paths;
    }
    /**
     * Scans a directory matching a given pattern for README files.
     *     *
     * @param string $pattern The glob pattern.
     * @return string[] Array of relative file paths.
     */
    private function scan_dir(string $pattern): array
    {
        $files = glob($pattern) ?: [];

        return $files;
    }

    /**
     * Retrieves the content of a file from disk.
     *
     * Initializes WP_Filesystem if necessary.
     *
     * @param string $file The relative file path.
     * @return string|null The file content, or null if the file doesn't exist.
     */
    private function get_file(string $file): ?string
    {
        global $wp_filesystem;
        if (empty($wp_filesystem)) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            WP_Filesystem();
        }
        $full_path = ABSPATH . $file;
        if (!is_file($full_path)) {
            return null;
        }
        $contents = $wp_filesystem->get_contents($full_path);
        return $contents;
    }

    /**
     * Synchronizes README posts by updating/creating posts based on stored option data.
     *
     * @return void
     */
    public function sync_posts(): void
    {
        $this->readme_paths = $this->define_paths();
        foreach ($this->readme_paths as $filepath) {
            $this->update_post($filepath);
        }
    }
    /**
     * Reads each README file from disk and synchronizes its content to a WordPress option.
     *
     * @return void
     */
    public function sync_options(): void
    {
        $this->readme_paths = $this->define_paths();
        foreach ($this->readme_paths as $file) {
            $raw_file_content = $this->get_file($file);
            if (empty($raw_file_content)) {
                $raw_file_content = '';
            }
            $this->update_option($file, $raw_file_content);
        }
    }

    /**
     * Handles the scheduling of sync tasks based on the current request context.
     *
     * Checks for both options and posts sync hooks.
     *
     * @return void
     */
    public function handle_syncs(): void
    {
        // set define_paths to readme_paths
        $this->readme_paths = $this->define_paths();
        $paths = $this->readme_paths;
        do_action('qm/debug', $paths);

        if ($this->check_context($this->options_hook)) {
            $this->handle_options_sync();
        }
        if ($this->check_context($this->posts_hook)) {
            $this->handle_posts_sync();
        }
    }
    /**
     * Schedules an asynchronous task for syncing README options.
     *
     * @return void
     */
    public function handle_options_sync(): void
    {
        // Example message
        $message = 'README Options Sync Scheduled';
        $admin_notice = [
            'type'           => 'info',
            'message'        => $message,
            'is_dismissible' => true,
        ];
        $controller = new ActionScheduler();
        // The callback references the function below in the same namespace.
        $controller::register_async_action(
            $this->options_hook,
            __NAMESPACE__ . '\\wp2_wiki_readme_options_response',
            [],
            null,
            true,
            $admin_notice
        );
    }
    /**
     * Schedules an asynchronous task for syncing README posts.
     *
     * @return void
     */
    public function handle_posts_sync(): void
    {
        $message = 'README Posts Sync Scheduled';
        $admin_notice = [
            'type'           => 'info',
            'message'        => $message,
            'is_dismissible' => true,
        ];
        $controller = new ActionScheduler();
        $controller::register_async_action(
            $this->posts_hook,
            __NAMESPACE__ . '\\wp2_wiki_readme_posts_response',
            [],
            null,
            true,
            $admin_notice
        );
    }
    /**
     * Checks if the current request context matches the expected hook parameters.
     *
     * Determines if the page is the README post listing and if the specific sync hook is set.
     *
     * @param string $hook The hook query parameter to check.
     * @return bool True if the context matches, false otherwise.
     */
    private function check_context(string $hook): bool
    {
        return (
            isset($_GET['post_type'])
            && $_GET['post_type'] === $this->post_type
            && isset($_GET[$hook])
            && $_GET[$hook] === '1'
        );
    }
}

new Controller();

add_action('wp2_wiki_readme_options_sync', __NAMESPACE__ . '\\wp2_wiki_readme_options_response');
add_action('wp2_wiki_readme_posts_sync', __NAMESPACE__ . '\\wp2_wiki_readme_posts_response');

/**
 * Method for "options" sync (scheduled by Action Scheduler).
 */
function wp2_wiki_readme_options_response(): void
{
    $controller = new Controller();
    $controller->sync_options();
}

/**
 * Method for "posts" sync (scheduled by Action Scheduler).
 */
function wp2_wiki_readme_posts_response(): void
{
    $controller = new Controller();
    wp2_wiki_readme_options_response();
    $controller->sync_posts();
}
