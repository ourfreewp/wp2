<?php
// Path: wp-content/plugins/wp2/src/Blocks/Settings/Categories/init.php
namespace WP2\Blocks\Settings\Categories;

/**
 * Handles modifications to block categories for template blocks.
 */
class Controller
{

    /**
     * Prefix for block categories.
     *
     * @var string
     */
    private $prefix = 'wp2-';

    /**
     * Default block categories.
     * 
     * @var array
     */
    private $default_categories = [
        [
            'slug'  => 'article',
            'title' => 'Article',
        ],
        [
            'slug'  => 'broadcast',
            'title' => 'Broadcast',
        ],
        [
            'slug'  => 'focus',
            'title' => 'Focus',
        ],
        [
            'slug'  => 'item',
            'title' => 'Item',
        ],
        [
            'slug'  => 'main',
            'title' => 'Main',
        ],
        [
            'slug'  => 'navigation',
            'title' => 'Navigation',
        ],
        [
            'slug'  => 'query',
            'title' => 'Query',
        ],
        [
            'slug'  => 'root',
            'title' => 'Root',
        ],
        [
            'slug'  => 'site',
            'title' => 'Site',
        ],
        [
            'slug'  => 'utility',
            'title' => 'Utility',
        ],
        [
            'slug'  => 'new',
            'title' => 'New',
        ],
        [
            'slug'  => 'wiki',
            'title' => 'Wiki',
        ],
    ];

    /**
     * Holds all block category definitions.
     *
     * @var array
     */
    private $block_categories = [];


    /**
     * Constructor: Initializes block categories by collecting all definitions.
     */
    public function __construct()
    {
        $this->initialize_categories();
    }

    /**
     * Initializes block categories with default values.
     */
    private function initialize_categories()
    {

        $default_categories = apply_filters('wp2_block_categories', $this->default_categories);

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

        $prefix = $this->prefix;
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
    public function register_categories($categories)
    {
        return array_merge($categories, $this->block_categories);
    }
}

/**
 * Initializes the TemplateBlockController and hooks it to WordPress.
 */
function wp2_init_block_categories()
{
    $controller = new Controller();
    add_filter('block_categories_all', [$controller, 'register_categories'], 20, 1);
}

add_action('init', __NAMESPACE__ . '\\wp2_init_block_categories', 20);
