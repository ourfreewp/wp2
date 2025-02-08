<?php
// Path: wp-content/plugins/wp2/src/Templates/init-template-parts.php
namespace WP2\Templates;

class TemplatePartController
{
    private $template_parts = [];
    private $template_part_areas = [];
    private $template_parts_dir;

    public function __construct()
    {
        $this->template_parts_dir = WP2_THEME_DIR . '/parts';

        $this->template_parts = array_merge(
            $this->generate_templates('system'),
            $this->generate_templates('archive'),
            $this->generate_templates('front_page'),
            $this->generate_templates('singular')
        );

        add_action('admin_init', [$this, 'validate_template_parts_on_site_editor']);
    }

    public function register_template_parts($theme_json)
    {
        $new_data = [
            'version'       => 3,
            'templateParts' => $this->template_parts,
        ];

        return $theme_json->update_with($new_data);
    }

    public function validate_template_parts_on_site_editor()
    {
        if (!isset($_GET['postType']) || $_GET['postType'] !== 'wp_template_part') {
            return;
        }

        if (!is_dir($this->template_parts_dir)) {
            return;
        }

        foreach ($this->template_parts as $template_part) {
            $template_name = $template_part['name'];
            $expected_file = $this->template_parts_dir . '/' . $template_name . '.html';

            if (!file_exists($expected_file)) {
                $this->create_part_file($expected_file, $template_name);
            } else {

                $file_contents = file_get_contents($expected_file);
                if (empty($file_contents)) {
                    $this->create_part_file($expected_file, $template_name);
                }
            }
        }
    }

    private function create_part_file($file_path, $template_name)
    {

        $template = explode('-part-', $template_name)[1];
        $template_zone = explode('-part-', $template_name)[0];

        $class_name = 'wp2-wrapper wp2-wrapper--' . $template_zone . ' wp2-wrapper--' . $template;

        $template_name = ucwords(str_replace('-', ' ', $template));

        $zone_name = ucwords(str_replace('-', ' ', $template_zone));

        $wrapper_name = $template_name . ': ' . $zone_name . ' Wrapper';

        $group_attributes = json_encode([
            'lock' => [
                'move' => true,
                'remove' => true,
            ],
            'className' => $class_name,
            'metadata' => [
                'name' => $wrapper_name,
            ],
            'layout' => [
                'type' => 'constrained',
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        $inner_content = $this->generate_part_content($template, $template_zone);

        $wrapper = '<!-- wp:group %s --><div class="wp-block-group">%s</div> <!-- /wp:group -->';

        $wrapper = sprintf($wrapper, $group_attributes, $inner_content);

        file_put_contents($file_path, $wrapper);
    }

    private function generate_part_content($template, $template_zone)
    {
        $lock = [
            'move' => true,
            'remove' => true,
        ];

        $option = [
            'value' => '0',
            'label' => 'Default',
        ];

        $attributes = [
            'lock' => $lock,
            'option' => $option,
            'blockstudio' => [
                'attributes' => [
                    "0" => "o",
                    'lock' => $lock,
                    'blockstudio' => [
                        'data' => [
                            'option' => $option,
                        ]
                    ],
                    'option' => $option,
                ]
            ]
        ];

        // Mapping template zones to their respective templates
        $template_map = [
            'root-header'      => '<!-- wp:wp2/root-header %s --><!-- /wp:wp2/root-header -->',
            'root-content'     => '<!-- wp:wp2/root-content %s --><!-- /wp:wp2/root-content -->',
            'root-footer'      => '<!-- wp:wp2/root-footer %s --><!-- /wp:wp2/root-footer -->',
            'article-header'   => '<!-- wp:wp2/article-header %s --><!-- /wp:wp2/article-header -->',
            'article-content'  => '<!-- wp:wp2/article-content %s --><!-- /wp:wp2/article-content -->',
            'article-footer'   => '<!-- wp:wp2/article-footer %s --><!-- /wp:wp2/article-footer -->',
            'query-header'     => '<!-- wp:wp2/query-header %s --><!-- /wp:wp2/query-header -->',
            'query-content'    => '<!-- wp:wp2/query-content %s --><!-- /wp:wp2/query-content -->',
            'query-footer'     => '<!-- wp:wp2/query-footer %s --><!-- /wp:wp2/query-footer -->',
            'main-header'      => '<!-- wp:wp2/main-header %s --><!-- /wp:wp2/main-header -->',
            'main-content'     => '<!-- wp:wp2/main-content %s --><!-- /wp:wp2/main-content -->',
            'main-footer'      => '<!-- wp:wp2/main-footer %s --><!-- /wp:wp2/main-footer -->',
            'primary-content'  => '<!-- wp:wp2/primary %s --><!-- /wp:wp2/primary -->',
            'secondary-content' => '<!-- wp:wp2/secondary %s --><!-- /wp:wp2/secondary -->'
        ];

        // Select the appropriate template or fallback to an empty string
        $template = $template_map[$template_zone] ?? '';

        // Format the template with JSON-encoded attributes
        return sprintf($template, json_encode($attributes, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    public function generate_templates($type)
    {
        $templates_map = [
            'system' => [
                '404' => '404',
                'index' => 'Index',
            ],
            'archive' => [
                'archive' => 'Archive',
                'author' => 'Author',
                'search' => 'Search',
            ],
            'front_page' => [
                'front-page' => 'Front Page',
            ],
            'singular' => [
                'page' => 'Page',
                'single' => 'Single',
            ],
        ];

        $zones_map = [
            'system' => [
                'Header'  => 'header',
                'Footer'  => 'footer',
                'Content' => 'div',
            ],
            'archive' => [
                'Header'  => 'header',
                'Footer'  => 'footer',
                'Content' => 'section',
                'Query'   => 'div',
            ],
            'front_page' => [
                'Header'  => 'header',
                'Footer'  => 'footer',
                'Content' => 'section',
                'Query'   => 'div',
            ],
            'singular' => [
                'Header'  => 'header',
                'Footer'  => 'footer',
                'Content' => 'div',
                'Article' => 'article',
            ],
        ];

        // Validate input type
        if (!isset($templates_map[$type]) || !isset($zones_map[$type])) {
            return []; // Return empty if type is not found
        }

        $templates = $templates_map[$type];

        $zones = $this->generate_template_part_zones($zones_map[$type]);

        return $this->generate_template_parts($templates, $zones);
    }

    private function generate_template_part_zones($zones)
    {
        $prefixes = ['Root', 'Main', 'Primary', 'Secondary', 'Article', 'Query'];

        $prefixed_zones = [];

        // Define exclusion rules
        $exclusions = [
            'Article'  => ['Query'],
            'Query'    => ['Article'],
            'Root'     => ['Query', 'Article'],
            'Main'     => ['Query', 'Article'],
            'Primary'  => ['Query', 'Article', 'Header', 'Footer'],
            'Secondary' => ['Query', 'Article', 'Header', 'Footer'],
        ];

        foreach ($prefixes as $prefix) {
            foreach ($zones as $zone_key => $zone_value) {

                // Skip if the prefix should not apply to this zone
                if (isset($exclusions[$prefix]) && in_array($zone_key, $exclusions[$prefix])) {
                    continue;
                }

                // If the prefix matches the zone, use it directly
                if ($prefix === $zone_key) {
                    $prefixed_zones[$zone_key] = $zone_key;
                    continue;
                }

                // Otherwise, append the prefix
                $prefixed_zones["{$prefix} {$zone_key}"] = $zone_key;
            }
        }

        return $prefixed_zones;
    }

    private function generate_template_parts($templates, $zones)
    {
        $template_parts = [];

        foreach ($templates as $template => $title) {
            $template_parts = array_merge($template_parts, $this->generate_template_parts_for_template($template, $title, $zones));
        }

        return $template_parts;
    }

    private function generate_template_parts_for_template($template, $title, $zones)
    {
        $excluded_zones = [
            'archive'     => ['Article'],
            'author'      => ['Article'],
            'search'      => ['Article'],
            'front-page'  => ['Article'],
            '404'         => ['Article', 'Query'],
            'index'       => ['Article', 'Query'],
            'page'        => ['Query'],
            'single'      => ['Query'],
        ];

        $template_parts = [];

        foreach ($zones as $zone => $area) {
            if ($this->should_exclude_zone($template, $zone)) {
                continue;
            }
            $template_parts[] = $this->build_template_part($template, $title, $zone, $area);
        }

        return $template_parts;
    }

    private function should_exclude_zone(string $template, string $zone): bool
    {
        $exclusions = [
            'archive'     => ['Article'],
            'author'      => ['Article'],
            'search'      => ['Article'],
            'front-page'  => ['Article'],
            '404'         => ['Article', 'Query'],
            'index'       => ['Article', 'Query'],
            'page'        => ['Query'],
            'single'      => ['Query'],
        ];

        if (!isset($exclusions[$template])) {
            return false;
        }

        foreach ($exclusions[$template] as $excluded) {
            if (strpos($zone, $excluded) === 0) {
                return true;
            }
        }

        return false;
    }

    private function build_template_part($template, $title, $zone, $area)
    {
        $zone_slug = $this->slugify($zone);
        $name = "{$zone_slug}-part-{$template}";

        return $this->create_template_part("{$title}: {$zone}", $name, $zone);
    }

    private function slugify($value)
    {
        return str_replace(' ', '-', strtolower(trim($value)));
    }

    private function create_template_part($title, $name, $area)
    {
        return [
            'title' => $title,
            'name'  => $name,
            'area'  => $area,
        ];
    }

    public function get_template_part_areas()
    {
        $this->template_part_areas = array_unique(
            array_map(function ($part) {
                return $part['area'];
            }, $this->template_parts)
        );


        return $this->template_part_areas;
    }
}

function wp2_template_parts()
{
    $controller = new TemplatePartController();
    add_filter('wp_theme_json_data_theme', [$controller, 'register_template_parts'], 21, 1);
}

// add with priority 20 to ensure it runs after the default template parts are registered
add_action('init', function () {
    wp2_template_parts();
}, 22);
