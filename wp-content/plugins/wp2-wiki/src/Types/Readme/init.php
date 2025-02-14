<?php
// Path: wp-content/plugins/wp2-wiki/src/Wiki/Types/Readme/init.php

namespace WP2_Wiki\Types\Readme;

class Controller
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
    private $type = 'wp2_wiki_readme';
    private $single_slug = 'wp2-readme';
    private $archive_slug = 'wp2-readme';
    private $show_in_menu = 'edit.php?post_type=' . 'wp2_wiki_doc';

    /**
     * Constructor
     */
    public function __construct()
    {
        add_action('init', [$this, 'register_type'], 110);
    }

    public function register_type()
    {

        $args   = $this->set_args();
        $type   = $this->type;

        register_post_type($type, $args);

        $this->register_meta('_wp2_wiki_readme_path');
        $this->register_meta('_wp2_wiki_readme_html');
        $this->register_meta('_wp2_wiki_readme_toc');
        $this->register_meta('_wp2_wiki_readme_raw');
    }

    // Set labels
    private function set_labels()
    {

        $text_domain = $this->text_domain;
        $plural      = 'README';
        $singular    = 'README';
        $menu_name   = 'README';

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
            'all_items'          => __($plural, $text_domain),
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
        $show_in_menu = $this->show_in_menu;

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => $show_in_menu,
            'query_var'          => true,
            'rewrite'            => [
                'slug' => $slug,
                'with_front' => false,
            ],
            'capability_type'    => 'post',
            'has_archive'        => $archive_slug,
            'hierarchical'       => true,
            'supports'           => ['title', 'editor', 'thumbnail', 'excerpt', 'comments', 'page-attributes', 'custom-fields', 'revisions'],
            'show_in_rest'       => true,
            'rest_namespace'     => 'wp2-wiki/v1',
            'rest_base'          => 'readmes',
            'template'           => [
                ['wp2-wiki/readme', []],
            ],
            'template_lock'      => 'all',
        ];

        return $args;
    }

    private function register_meta($meta_key)
    {

        register_post_meta($this->type, $meta_key, [
            'show_in_rest' => [
                'schema' => [
                    'type' => 'string',
                ],
            ],
            'single'       => true,
            'type'         => 'string',
            'auth_callback' => function () {
                return current_user_can('edit_posts');
            },
        ]);
    }
}

new Controller();
