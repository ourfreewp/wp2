<?php
// Path: wp-content/plugins/wp2-wiki/src/Wiki/Types/init-single-readme.php

namespace WP2_Wiki\Types\Singles;


class READMEController
{

    /**
     * Textdomain
     * 
     * @var string
     */
    private $text_domain = 'wp2-wiki';

    /**
     * Type Prefix
     *
     * @var string
     */
    private $prefix = 'wp2_wiki';

    /**
     * Type
     *
     * @var string
     */
    private $type = 'readme';

    /**
     * Constructor
     */
    public function __construct()
    {
        add_action('init', [$this, 'register_type'], 30);
    }

    public function register_type()
    {

        $args   = $this->set_args();
        $prefix = $this->prefix;
        $type   = $this->prefix . '_' . $this->type;

        register_post_type($type, $args);
    }

    // Set labels
    private function set_labels()
    {

        $text_domain = $this->text_domain;
        $plural      = 'READMEs';
        $singular    = 'README';

        $labels = [
            'name'               => _x($plural, 'post type general name', $text_domain),
            'singular_name'      => _x($singular, 'post type singular name', $text_domain),
            'menu_name'          => _x($plural, 'admin menu', $text_domain),
            'name_admin_bar'     => _x($singular, 'add new on admin bar', $text_domain),
            'add_new'            => _x('Add New', $singular, $text_domain),
            'add_new_item'       => __('Add New ' . $singular, $text_domain),
            'new_item'           => __('New ' . $singular, $text_domain),
            'edit_item'          => __('Edit ' . $singular, $text_domain),
            'view_item'          => __('View ' . $singular, $text_domain),
            'all_items'          => __('All ' . $plural, $text_domain),
            'search_items'       => __('Search ' . $plural, $text_domain),
            'parent_item_colon'  => __('Parent ' . $plural . ':', $text_domain),
            'not_found'          => __('No ' . $plural . ' found.', $text_domain),
            'not_found_in_trash' => __('No ' . $plural . ' found in Trash.', $text_domain)
        ];

        return $labels;
    }

    private function set_args()
    {

        $labels = $this->set_labels();

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => [
                'slug' => 'wp2-readme',
            ],
            'capability_type'    => 'post',
            'has_archive'        => 'wp2-readmes',
            'hierarchical'       => true,
            'supports'           => ['title', 'editor', 'thumbnail', 'excerpt', 'comments', 'page-attributes', 'custom-fields', 'revisions'],
            'show_in_rest'       => true,
            'template'           => [
                ['wp2-wiki/readme', []],
            ],
            'template_lock'      => 'all',
        ];

        return $args;
    }
}

new READMEController();
