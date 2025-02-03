<?php
// Path: wp-content/plugins/wp2/src/Templates/init-template-types.php
namespace WP2\Templates;

/**
 * Handles modifications to template types.
 */
class TemplateTypeController
{

    /**
     * Holds all template type definitions.
     *
     * @var array
     */
    private $template_types = [];

    /**
     * Constructor: Initializes template types by collecting all definitions.
     */
    public function __construct()
    {
        $this->template_types = array_merge(
            $this->archive_templates(),
            $this->single_templates(),
            $this->page_templates(),
            $this->site_templates()
        );
    }

    /**
     * Registers template types with WordPress.
     *
     * @param array $templates Existing template types.
     * @return array Modified template types.
     */
    public function register_template_types($templates)
    {
    
        $updated_types = $this->template_types;
    
        foreach ($updated_types as $template_group) {
            foreach ($template_group as $key => $value) {
                $key = (string) $key; // Ensure the key is treated as a string.
    
                // Ensure `title` and `description` are defined.
                $value = $this->ensure_template_keys($value);
    
                $templates[$key] = $value;
            }
        }
        
        return $templates;
    }
    
    /**
     * Ensures that the `title` and `description` keys exist in a template definition.
     *
     * @param array $template The template definition.
     * @return array The template with ensured keys.
     */
    private function ensure_template_keys($template)
    {
        return [
            'title' => $template['title'] ?? '', // Provide a default title.
            'description' => $template['description'] ?? '', // Provide a default description.
        ];
    }
    /**
     * Defines Archive templates.
     */
    private function archive_templates()
    {
        return [
            $this->create_template(
                'archive',
                'Archive: Fallback',
                'The fallback template type for archive views.'
            ),
            $this->create_template(
                'author',
                'Single Item: Author',
                'The template type for individual author archives.'
            ),
        ];
    }

    /**
     * Defines Single templates.
     */
    private function single_templates()
    {
        return [
            $this->create_template(
                'single',
                'Single Item: Fallback',
                'The fallback template type for single views.'
            ),
        ];
    }

    /**
     * Defines Page templates.
     */
    private function page_templates()
    {
        return [
            $this->create_template(
                'page',
                'Single Item: Page',
                'The fallback template type for page views.'
            ),
            $this->create_template(
                'front-page',
                'Page: Front Page',
                'The template type for the front page of the site.'
            ),
            $this->create_template(
                'search',
                'Page: Search',
                'The template type for the search results page.'
            ),
        ];
    }

    /**
     * Defines Site templates (index and 404).
     */
    private function site_templates()
    {
        return [
            $this->create_template(
                'index',
                'Site: Fallback',
                'The fallback template type for the site.'
            ),
            $this->create_template(
                '404',
                'Page: 404',
                'The template type for the 404 page.'
            ),
        ];
    }

    /**
     * Helper function to create a template type definition.
     *
     * @param string $template_key Unique template identifier.
     * @param string $title        Title for the template.
     * @param string $description  Description for the template.
     * @return array Template type definition.
     */
    private function create_template($template_key, $title, $description)
    {
        return [
            $template_key => [
                'title'       => $title,
                'description' => $description,
            ],
        ];
    }
}

/**
 * Initializes the Template_Type_Controller and hooks it to WordPress.
 */
function wp2_template_types()
{
    $controller = new TemplateTypeController();
    add_filter('default_template_types', [$controller, 'register_template_types']);
}

add_action('init', function () {
    wp2_template_types();
}, 21);
