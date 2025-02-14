<?php
// Path: wp-content/plugins/wp2-directory/src/Types/Listing/init.php

namespace WP2_Directory\Types\Listing;

class Controller
{
    /**
     * Textdomain.
     *
     * @var string
     */
    private string $text_domain = 'wp2-directory';

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
    private string $single_key     = 'wp2_catalog_listing';
    private string $single_slug    = 'wp2-directory';
    private string $single_archive = 'wp2-directory';
    private array $single_labels  = [
        'archive_name' => 'Directory',
        'singular'     => 'Listing',
        'plural'       => 'Listings',
    ];

    private $show_in_menu = true;

    /**
     * Constructor.
     *
     * @param string $singular Singular label.
     * @param string $plural   Plural label.
     */
    public function __construct()
    {
        add_action('init', [$this, 'register_post_type'], 101);
    }

    /**
     * Registers the custom post type.
     */
    public function register_post_type(): void
    {

        $type = $this->single_key;

        $type_args = [
            'slug'    => $this->single_slug,
            'archive' => $this->single_archive,
            'show_in_menu' => $this->show_in_menu,
        ];

        $type_args['labels'] = $this->set_labels([
            'plural'      => $this->single_labels['plural'],
            'singular'    => $this->single_labels['singular'],
            'menu_name'   => $this->single_labels['archive_name'],
            'text_domain' => $this->text_domain,
        ]);

        $type_args = $this->set_args($type_args);

        register_post_type($type, $type_args);
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
        $menu_name   = $label_args['menu_name'];
        $text_domain = $label_args['text_domain'];

        return [
            'name'               => _x($plural, 'post type general name', $text_domain),
            'singular_name'      => _x($singular, 'post type singular name', $text_domain),
            'menu_name'          => _x($menu_name, 'admin menu', $text_domain),
            'name_admin_bar'     => _x($singular, 'add new on admin bar', $text_domain),
            'add_new'            => _x('New', $singular, $text_domain),
            'add_new_item'       => __('New ' . $singular, $text_domain),
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
            'rewrite'            => [
                'slug' => $slug,
                'with_front' => false,
            ],
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
