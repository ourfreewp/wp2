<?php
// Path: wp-content/plugins/wp2-blueprint/src/Wiki/Types/init-term-enum.php

namespace WP2\Blueprint\Wiki\Types\Taxonomies;


class EnumController
{

    /**
     * Textdomain
     * 
     * @var string
     */
    private $text_domain = 'wp2-blueprint';

    /**
     * Prefix
     *
     * @var string
     */
    private $prefix = 'wp2_blueprint';

    /**
     * Taxonomy
     *
     * @var string
     */
    private $taxonomy = 'enum';

    /**
     * Initial Post Type
     *
     * @var array
     */
    private $post_type = 'thing';
    /**
     * Constructor
     */
    public function __construct()
    {
        add_action('init', [$this, 'register_type'], 12);
    }

    public function register_type()
    {

        $args   = $this->set_args();
        $prefix = $this->prefix;
        $taxonomy = $this->prefix . '_' . $this->taxonomy;
        $post_type = $this->prefix . '_' . $this->post_type;

        register_taxonomy($taxonomy, [$post_type], $args);
    }

    // Set labels
    private function set_labels()
    {

        $text_domain = $this->text_domain;
        $plural      = 'Enums';
        $singular    = 'Enum';

        $labels = [
            'name'              => _x($plural, 'taxonomy general name', $text_domain),
            'singular_name'     => _x($singular, 'taxonomy singular name', $text_domain),
            'search_items'      => __('Search ' . $plural, $text_domain),
            'all_items'         => __('All ' . $plural, $text_domain),
            'parent_item'       => __('Parent ' . $singular, $text_domain),
            'parent_item_colon' => __('Parent ' . $singular . ':', $text_domain),
        ];

        return $labels;
    }

    private function set_args()
    {

        $labels = $this->set_labels();

        $args = [
            'labels'            => $labels,
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud'     => true,
            'default_term'      => [
                'name' => 'Undefined',
                'slug' => 'undefined',
                'description' => 'No enumeration defined.',
            ],
            'sort'              => true,
        ];

        return $args;
    }
}

new EnumController();
