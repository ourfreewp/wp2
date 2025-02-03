<?php
// Path: wp-content/plugins/wp2/src/Templates/init-template-blocks.php
namespace WP2\Templates;

/**
 * Handles modifications to block categories for template blocks.
 */
class TemplateBlockController
{

    /**
     * Holds all block category definitions.
     *
     * @var array
     */
    private $block_categories = [];

    public function __construct()
    {
        // Initialize block categories with default values.
        $this->initialize_block_categories();
    }

    /**
     * Initializes block categories with default values.
     */
    private function initialize_block_categories()
    {

        $default_categories = [
            [
                'slug'  => 'article',
                'title' => 'Article',
            ],
            [
                'slug'  => 'item',
                'title' => 'Item',
            ],
            [
                'slug'  => 'link',
                'title' => 'Link',
            ],
            [
                'slug'  => 'promotion',
                'title' => 'Promotion',
            ],
            [
                'slug'  => 'navigation',
                'title' => 'Navigation',
            ],
            [
                'slug'  => 'player',
                'title' => 'Player',
            ],
            [
                'slug'  => 'query',
                'title' => 'Query',
            ],
            [
                'slug'  => 'shortcode',
                'title' => 'Shortcode',
            ],
            [
                'slug'  => 'single',
                'title' => 'Single',
            ],
            [
                'slug'  => 'site',
                'title' => 'Site',
            ],
            [
                'slug'  => 'content',
                'title' => 'Content',
            ],
        ];

        foreach ($default_categories as $category) {
            $this->block_categories[] = $this->create_category($category);
        }
    }

    /**
     * Helper function to create a block category definition.
     *
     * @param array $args Arguments for creating a block category.
     * @return array Block category definition.
     */
    private function create_category($args)
    {

        $prefix = 'wp2-';
        $slug = $args['slug'] ?? '';
        $title = $args['title'] ?? '';

        return [
            'slug'  => $prefix . $slug,
            'title' => $title,
        ];
    }

    /**
     * Registers the block categories with WordPress.
     *
     * @param array $categories Existing block categories.
     * @return array Updated block categories.
     */
    public function register_block_categories($categories)
    {
        return array_merge($categories, $this->block_categories);
    }
}

/**
 * Initializes the TemplateBlockController and hooks it to WordPress.
 */
function wp2_template_block_categories()
{
    $template_block_controller = new TemplateBlockController();
    add_filter('block_categories_all', [$template_block_controller, 'register_block_categories'], 20, 1);
}

add_action('init', function () {
    wp2_template_block_categories();
}, 22);