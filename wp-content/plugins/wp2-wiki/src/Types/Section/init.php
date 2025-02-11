<?php
// Path: wp-content/plugins/wp2-wiki/src/Types/Section/init.php

namespace WP2_Wiki\Types\Section;


class Controller
{

    /**
     * Textdomain
     * 
     * @var string
     */
    private $text_domain = 'wp2-wiki';

    /**
     * Prefix
     *
     * @var string
     */
    private $prefix = 'wp2_wiki';

    /**
     * Taxonomy
     *
     * @var string
     */
    private $taxonomy = 'wp2_wiki_section';

    /**
     * Initial Post Type
     *
     * @var array
     */
    private $post_types = [
        'wp2_wiki_thing',
        'wp2_wiki_readme',
    ];
    /**
     * Constructor
     */
    public function __construct()
    {
        add_action('init', [$this, 'register_type'], 20);
    }

    public function register_type()
    {

        $args   = $this->set_args();
        $labels = $this->set_labels();

        register_taxonomy($this->taxonomy, $this->post_types, $args);
    }

    // Set labels
    private function set_labels()
    {

        $text_domain = $this->text_domain;
        $plural      = 'Sections';
        $singular    = 'Section';

        $labels = [
            'name'              => _x($plural, 'taxonomy general name', $text_domain),
            'singular_name'     => _x($singular, 'taxonomy singular name', $text_domain),
            'search_items'      => __('Search ' . $plural, $text_domain),
            'all_items'         => __('All ' . $plural, $text_domain),
            'parent_item'       => __('Parent ' . $singular, $text_domain),
            'parent_item_colon' => __('Parent ' . $singular . ':', $text_domain),
            'edit_item'         => __('Edit ' . $singular, $text_domain),
            'update_item'       => __('Update ' . $singular, $text_domain),
            'add_new_item'     => __('Add New ' . $singular, $text_domain),
            'new_item_name'    => __('New ' . $singular . ' Name', $text_domain),
            'menu_name'         => __($plural, $text_domain),
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
                'name' => 'Miscellaneous',
                'slug' => 'miscellaneous',
            ],
            'sort'              => true,
        ];

        return $args;
    }
}

new Controller();
