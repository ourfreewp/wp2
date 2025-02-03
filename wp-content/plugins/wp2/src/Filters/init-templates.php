<?php
// Path: wp-content/plugins/wp2/src/Filters/init-templates.php

namespace WP2\Filters;

use WP_Filesystem;

class TemplateController
{
    private $template_dir;
    private $template_list;
    private $wp_filesystem;

    public function __construct()
    {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        WP_Filesystem();
        global $wp_filesystem;

        $this->wp_filesystem = $wp_filesystem;
        $this->template_dir = get_template_directory() . '/templates/';

        // Load template list from constant or fallback to default templates
        $default_templates = [
            '404',
            'archive',
            'author',
            'front-page',
            'index',
            'page',
            'search',
            'single',
        ];

        $this->template_list = defined('WP2_TEMPLATES') && is_array(WP2_TEMPLATES)
            ? WP2_TEMPLATES
            : $default_templates;

        $this->template_list = apply_filters('wp2/templates', $this->template_list);

        add_action('init', [$this, 'validate_templates']);
        add_action('admin_init', [$this, 'validate_template_on_site_editor']);
    }

    /**
     * Validate that all required templates exist in both files and database.
     */
    public function validate_templates()
    {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        WP_Filesystem();
        global $wp_filesystem;

        if (!$this->wp_filesystem->is_dir($this->template_dir)) {
            $this->wp_filesystem->mkdir($this->template_dir);
        }

        foreach ($this->template_list as $template_slug) {
            $file_path = $this->template_dir . $template_slug . '.html';

            // Check if the template exists in the database using WordPress functions
            $existing_template = get_posts([
                'post_type'   => 'wp_template',
                'name'        => $template_slug,
                'post_status' => 'publish',
                'numberposts' => 1
            ]);

            $template_content = $this->generate_template_content($template_slug);

            // Insert into the database if missing
            if (empty($existing_template)) {
                wp_insert_post([
                    'post_type'    => 'wp_template',
                    'post_name'    => $template_slug,
                    'post_title'   => ucfirst(str_replace('-', ' ', $template_slug)),
                    'post_content' => $template_content,
                    'post_status'  => 'publish'
                ]);
            }

            // Ensure the file exists
            if (!$this->wp_filesystem->exists($file_path)) {
                $this->wp_filesystem->put_contents($file_path, $template_content);
            }
        }
    }

    /**
     * Ensure templates exist for the Site Editor.
     */
    public function validate_template_on_site_editor()
    {
        if (!isset($_GET['postType']) || $_GET['postType'] !== 'wp_template') {
            return;
        }

        if (!$this->wp_filesystem->is_dir($this->template_dir)) {
            do_action('qm/debug', 'Template directory does not exist: ' . $this->template_dir);
            return;
        }

        foreach ($this->template_list as $template_slug) {
            $file_path = $this->template_dir . '/' . $template_slug . '.html';

            if (!$this->wp_filesystem->exists($file_path)) {
                do_action('qm/debug', "Missing template file: $file_path");
                $template_content = $this->generate_template_content($template_slug);
                $this->wp_filesystem->put_contents($file_path, $template_content);
            } else {
                $file_content = $this->wp_filesystem->get_contents($file_path);
                $template_content = $this->generate_template_content($template_slug);

                if ($file_content !== $template_content) {
                    do_action('qm/debug', "Template file content does not match: $file_path");
                    $this->wp_filesystem->put_contents($file_path, $template_content);
                }
            }
        }
    }
    /**
     * Generate the base template content.
     *
     * @param string $template_slug
     * @return string
     */
    private function generate_template_content($template_slug)
    {
        $template_name = ucfirst(str_replace('-', ' ', $template_slug)) . ': Root';
        $option_value = "{$template_slug}-0";
        $option_label = ucfirst(str_replace('-', ' ', $template_slug)) . ' — Default';

        // Construct the JSON metadata for the root block
        $attributes = [
            'metadata' => ['name' => $template_name],
            'blockstudio' => [
                'attributes' => [
                    'option' => [
                        'value' => $option_value,
                        'label' => $option_label
                    ]
                ]
            ]
        ];

        $template = sprintf(
            "<!-- wp:wp2/site-root %s -->\n",
            json_encode($attributes, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );

        $root_zones = ['header', 'content', 'footer'];

        foreach ($root_zones as $zone) {
            $template .= "    " . $this->generate_template_part($template_slug, $zone) . "\n";
        }

        $template .= "<!-- /wp:wp2/site-root -->";

        return $template;
    }

    /**
     * Generate a template part.
     *
     * @param string $template_slug
     * @param string $part
     * @return string
     */
    private function generate_template_part($template_slug, $zone)
    {

        $part = $zone;
        $slug = "root-{$part}-part-{$template_slug}";
        $className = "wp2-root-{$part}";

        $template = [
            'value' => $template_slug,
            'label' => ucfirst(str_replace('-', ' ', $template_slug))
        ];

        $template_zone = [
            'value' => $zone,
            'label' => ucfirst($zone)
        ];

        $template_area = [
            'value' => $part,
            'label' => ucfirst($part)
        ];

        $lock = ['move' => true, 'remove' => true];

        // Construct block attributes
        $attributes = [
            'slug' => $slug,
            'lock' => $lock,
            'className' => $className,
            'template' => $template,
            'template_zone' => $template_zone,
            'template_area' => $template_area,
            'blockstudio' => [
                'attributes' => [
                    "0" => "o",
                    'slug' => $slug,
                    'lock' => $lock,
                    'className' => $className,
                    'blockstudio' => [
                        'data' => [
                            'template' => $template,
                            'template_zone' => $template_zone,
                            'template_area' => $template_area
                        ]
                    ],
                    'template' => $template,
                    'template_zone' => $template_zone,
                    'template_area' => $template_area
                ]
            ]
        ];

        // Encode attributes into JSON // format the json
        return sprintf(
            "<!-- wp:template-part %s /-->",
            json_encode($attributes, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );
    }
}


/**
 * Initializes the TemplateController and hooks it to WordPress.
 */
function wp2_init_templates_controller()
{
    new TemplateController();
}

add_action('init', function () {
    wp2_init_templates_controller();
}, 30);


