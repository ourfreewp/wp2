<?php
// Path: wp-content/plugins/wp2/src/Templates/init-template-areas.php
namespace WP2\Templates;

use \WP2\Templates\TemplatePartController;

/**
 * Handles modifications to the main query for template part areas.
 */
class TemplateAreaController
{

    /**
     * Holds all template area definitions.
     *
     * @var array
     */
    private $template_areas = [];

    public function __construct()
    {
        // Initialize template areas using data from TemplatePartController.
        $this->initialize_template_areas();
    }
    /**
     * Initializes the template areas with default values.
     */
    private function initialize_template_areas()
    {
        $template_part_controller = new TemplatePartController();

        // Get all template part area names as a flat list.
        $template_part_area_names = $template_part_controller->get_template_part_areas();

        // Generate template areas from the flat list of area names.
        foreach ($template_part_area_names as $area_name) {
            $area_tag = $this->get_default_tag_for_area($area_name); // Dynamically determine the tag.
            $label = ucfirst(str_replace('-', ' ', $area_name));     // Generate label from area name.
            $description = 'The ' . $label . ' area of the template.'; // Generate description.

            $args = [
                'area_name' => $area_name,
                'area_tag' => $area_tag,
                'label' => $label,
                'description' => $description,
                'icon' => 'layout',
            ];

            $this->template_areas[] = $this->create_area($args);
        }
    }
    /**
     * Dynamically determines the default HTML tag for an area.
     *
     * @param string $area_name The area name.
     * @return string The default HTML tag.
     */
    private function get_default_tag_for_area($area_name)
    {
        // Define area-to-tag mappings, fallback to 'div'.
        $tag_mappings = [
            'header' => 'div',
            'footer' => 'div',
            'content' => 'div',
            'aside' => 'div',
        ];

        foreach ($tag_mappings as $key => $tag) {
            if (strpos($area_name, $key) !== false) {
                return $tag;
            }
        }

        return 'div'; // Default tag.
    }

    /**
     * Helper function to create a template area definition.
     *
     * @param string $area_name Unique area identifier.
     * @param string $area_tag  HTML tag for the area.
     * @param string $label     Label for the area.
     * @param string $description Description for the area.
     * @return array Template area definition.
     */
    private function create_area($args)
    {
        $text_domain = 'wp2';
        $area_name = $args['area_name'];
        $area_tag = $args['area_tag'];
        $label = $args['label'] ? $args['label'] : $area_name ?? '';
        $description = $args['description'] ?? '';
        $icon = $args['icon'];

        return [
            'area'        => $area_name,
            'area_tag'    => $area_tag,
            'label'       => __($label, $text_domain),
            'description' => __($description, $text_domain),
            'icon'        => $icon,
        ];
    }

    /**
     * Registers the template areas with WordPress.
     *
     * @param array $areas Existing template part areas.
     * @return array Updated template part areas.
     */
    public function register_template_areas($areas)
    {
        return array_merge($areas, $this->template_areas);
    }
}

/**
 * Initializes the TemplateAreaController and hooks it to WordPress.
 */
function wp2_template_part_areas()
{
    $template_area_controller = new TemplateAreaController();
    add_filter('default_wp_template_part_areas', [$template_area_controller, 'register_template_areas'], 20, 1);
}

add_action('init', function () {
    wp2_template_part_areas();
}, 22);
