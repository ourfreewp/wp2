<?php
// Path: wp-content/plugins/wp2-wiki/src/Wiki/Types/Doc/init.php

namespace WP2\Wiki\Types\Doc;


class SingleController
{

    /**
     * Textdomain
     * 
     * @var string
     */
    private $text_domain = 'wp2-wiki';

    /**
     * prefix
     *
     * @var string
     */
    private $prefix = 'wp2_wiki';

    /**
     * Type
     *
     * @var string
     */
    private $type = 'wp2_wiki_doc';
    private $single_slug = 'wp2-doc';
    private $archive_slug = 'wp2-wiki';

    /**
     * Labels
     * @var array
     */
    private $labels = [
        'singular' => 'Doc',
        'plural'   => 'Docs',
        'menu_name' => 'Wiki',
    ];

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
        $type   = $this->type;
        register_post_type($type, $args);
    }

    // Set labels
    private function set_labels()
    {

        $text_domain = $this->text_domain;
        $plural      = $this->labels['plural'];
        $singular    = $this->labels['singular'];
        $menu_name   = $this->labels['menu_name'];

        $labels = [
            'name'               => _x($plural, 'post type general name', $text_domain),
            'singular_name'      => _x($singular, 'post type singular name', $text_domain),
            'menu_name'          => _x($menu_name, 'admin menu', $text_domain),
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

        $slug = $this->single_slug;
        $archive_slug = $this->archive_slug;

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => [
                'slug' => $slug,
            ],
            'capability_type'    => 'post',
            'has_archive'        => $archive_slug,
            'hierarchical'       => true,
            'supports'           => ['title', 'editor', 'thumbnail', 'excerpt', 'comments', 'page-attributes', 'custom-fields', 'revisions'],
            'show_in_rest'       => true,
        ];

        return $args;
    }
}

new SingleController();
