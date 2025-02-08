<?php
// Path: wp-content/plugins/wp2-blueprint/src/Blocks/wp2/Overview/init.php

namespace WP2\Blueprint\Blocks\Overview;

class Controller
{
    /**
     * Text Domain.
     *
     * @var string
     */
    private $text_domain = 'wp2-blueprint';

    /**
     * Type Prefix.
     *
     * @var string
     */
    private $prefix = 'wp2_blueprint';

    /**
     * The license key to be used in the localized script.
     *
     * @var string
     */
    private $license_key;

    /**
     * Constructor.
     *
     * Initializes the license key and registers all necessary hooks.
     */
    public function __construct()
    {
        $this->license_key = defined('WP2_FULLPAGEJS_LICENSE_KEY') ? WP2_FULLPAGEJS_LICENSE_KEY : '';
    }


    /**
     * Registers the order field for the section taxonomy.
     * 
     * @param array $meta_boxes
     * @return array
     */

    public function register_fields($meta_boxes)
    {
        $prefix = $this->prefix;

        $meta_boxes[] = [
            'title'      => __('Section Fields', $this->text_domain),
            'id'         => $prefix . '_section_fields',
            'taxonomies' => ['wp2_demo_slide_section'],
            'fields'     => [
                [
                    'name'              => __('Order', $this->text_domain),
                    'id'                => $prefix . 'order',
                    'type'              => 'number',
                    'required'          => false,
                    'disabled'          => false,
                    'readonly'          => false,
                    'clone'             => false,
                    'clone_empty_start' => false,
                    'hide_from_rest'    => false,
                    'hide_from_front'   => false,
                ],
                [
                    'name'          => 'Theme',
                    'id'            => $prefix . 'theme',
                    'type'          => 'select',
                    'options'       => [
                        'primary' => 'Primary',
                        'secondary' => 'Secondary',
                        'light' => 'Light',
                        'dark'  => 'Dark',
                        'white' => 'White',
                        'black' => 'Black',
                        'accent-1' => 'Accent 1',
                        'accent-2' => 'Accent 2',
                        'accent-3' => 'Accent 3',
                    ],
                ],
                [
                    'name'              => __('Content', $this->text_domain),
                    'id'                => $prefix . 'content',
                    'type'              => 'wysiwyg',
                    'raw'               => true,
                    'options'           => [
                        'editor_class'  => 'rwmb-wysiwyg',
                        'textarea_rows' => 4,
                        'teeny'         => true,
                        'media_buttons' => false,
                        'drag_drop_upload' => false,
                        'quicktags'     => false,
                    ],
                ],
            ],
        ];

        return $meta_boxes;
    }

    /**
     * Registers a private post type for slides and a hierarchical taxonomy for slide sections.
     */
    public function register_types()
    {
        $text_domain   = $this->text_domain;
        $prefix        = $this->prefix;
        $slide_singular = 'Slide';
        $slide_plural   = 'Slides';
        $slide_type     = $prefix . '_slide';
        $slide_slug     = 'slide';

        $section_singular = 'Section';
        $section_plural   = 'Sections';
        $section_type     = $prefix . '_slide_section';

        $post_type_labels = [
            'name'               => _x($slide_plural, 'post type general name', $text_domain),
        ];

        $post_type_args = [
            'labels'             => $post_type_labels,
            'description'        => 'Slides for the WP2 Demo Wizard.',
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => ['slug' => $slide_slug],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 20,
            'menu_icon'          => 'dashicons-slides',
            'supports'           => ['title', 'editor', 'thumbnail', 'page-attributes', 'custom-fields', 'revisions', 'author', 'excerpt'],
            'taxonomies'         => [$section_type],
            'show_in_rest'       => true,
            'template_lock'      => 'all',
            'template'           => [
                [
                    'wp2-demo/resource',
                    [
                        'lock' => [
                            'insert' => true,
                            'move'   => true,
                        ]
                    ],
                ],
            ],
        ];

        $taxonomy_labels = [
            'name'              => _x($section_plural, 'taxonomy general name', $text_domain),
        ];

        $taxonomy_args = [
            'labels'            => $taxonomy_labels,
            'show_ui'           => true,
            'default_term'      => [
                'name' => 'Missing Section',
                'slug' => 'missing-section',
                'description' => 'Slides without a section.',
            ],
            'sort'              => true,
        ];
    }

    /**
     * Enqueues and localizes an empty script with the fullPage.js license key.
     */
    public function enqueue_scripts()
    {

        wp_register_script('wp2-demo-wizard-scripts-local', null, [], null, true);

        wp_localize_script('wp2-demo-wizard-scripts-local', 'wp2FullpageConfig', [
            'licenseKey' => $this->license_key,
        ]);

        wp_enqueue_script('wp2-demo-wizard-scripts-local');

        wp_enqueue_style('wp-block-paragraph');
    }

    /**
     * Renders the block in the footer.
     */
    public function render_wizard()
    {
        echo bs_block([
            'id' => 'wp2-demo/wizard',
            'align' => 'full',
        ]);
    }
}

// Instantiate the controller.
function wp2_demo_init_wizard()
{
    $controller = new Controller();

    add_action('wp_enqueue_scripts', [$controller, 'enqueue_scripts'], 10);
    add_action('init', [$controller, 'register_types'], 40);
    add_filter('rwmb_meta_boxes', [$controller, 'register_fields'], 10);
}

wp2_demo_init_wizard();
