<?php
// Path: wp-content/plugins/wp2-wiki/src/Types/Thing/init.php

namespace WP2_Wiki\Types\Thing;

class Controller
{
    /**
     * Textdomain.
     *
     * @var string
     */
    private string $text_domain = 'wp2-wiki';

    /**
     * Prefixes.
     *
     * @var string
     */

    /**
     * Post type identifier.
     *
     * @var string
     */
    private string $single_key     = 'wp2_wiki_thing';
    private string $single_slug    = 'wp2-thing';
    private string $single_archive = 'wp2-things';
    private array $single_labels  = [
        'singular' => 'Thing',
        'plural'   => 'Things',
    ];
    private string $show_in_menu = 'edit.php?post_type=' . 'wp2_wiki_doc';

    /**
     * Constructor.
     *
     * @param string $singular Singular label.
     * @param string $plural   Plural label.
     */
    public function __construct()
    {
        add_action('init', [$this, 'register_post_type'], 110);
    }

    /**
     * Registers the custom post type.
     */
    public function register_post_type(): void
    {
        $post_type    = $this->single_key;
        $slug         = $this->single_slug;
        $archive      = $this->single_archive;
        $show_in_menu = $this->show_in_menu;
        $labels       = $this->single_labels;

        $singular  = $labels['singular'];
        $plural    = $labels['plural'];

        $label_args = [
            'singular'    => $singular,
            'plural'      => $plural,
            'text_domain' => $this->text_domain,
        ];

        $labels = $this->set_labels($label_args);

        $type_args = [
            'slug'    => $slug,
            'archive' => $archive,
            'show_in_menu' => $this->show_in_menu,
            'labels'  => $labels,
        ];

        $args   = $this->set_args($type_args);

        register_post_type($post_type, $args);
    }

    /**
     * Generates labels for the custom post type.
     *
     * @param array $label_args
     * @return array
     */
    private function set_labels(array $label_args): array
    {
        $plural      = $label_args['plural'];
        $singular    = $label_args['singular'];
        $text_domain = $label_args['text_domain'];

        return [
            'name'               => _x($plural, 'post type general name', $text_domain),
            'singular_name'      => _x($singular, 'post type singular name', $text_domain),
            'menu_name'          => _x($plural, 'admin menu', $text_domain),
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
            'not_found_in_trash' => __('No ' . $plural . ' found in Trash.', $text_domain),
        ];
    }

    /**
     * Generates arguments for the custom post type.
     *
     * @param array  $type_args
     */
    private function set_args(array $type_args): array
    {
        $labels       = $type_args['labels'];
        $slug         = $type_args['slug'];
        $archive      = $type_args['archive'];
        $show_in_menu = $type_args['show_in_menu'];
        return [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => $show_in_menu,
            'query_var'          => true,
            'rewrite'            => ['slug' => $slug],
            'capability_type'    => 'post',
            'has_archive'        => $archive,
            'hierarchical'       => true,
            'supports'           => [
                'title',
                'editor',
                'thumbnail',
                'excerpt',
                'comments',
                'page-attributes',
                'custom-fields',
                'revisions',
            ],
            'show_in_rest'       => true,
        ];
    }
}

new Controller();
