<?php
// Path: wp-content/plugins/wp2/src/Themes/Elements/init-body.php
namespace WP2\Themes\Elements\Body;

use WP2\Helpers\Context\Controller as ContextController;

/**
 * Manages body  and related functionalities.
 */
class Controller
{
    /**
     * Text domain for localization.
     */
    private const TEXT_DOMAIN = 'wp2';

    /**
     * Initialize the body class controller.
     */
    public function __construct()
    {
        add_filter('body_class', [$this, 'add_context_body_class']);
    }

    /**
     * Add formatted, escaped, and context-specific classes to the body tag.
     *
     * @param array $classes Existing body classes.
     * @return array Modified body classes.
     */
    public function add_context_body_class(array $classes): array
    {
        // Retrieve context values dynamically.
        $contexts = $this->get_context_values();

        // Format and append context strings to the classes array.
        foreach ($contexts as $context) {
            $formatted_context = $this->format_context_string($context);
            $classes[] = esc_attr("wp2-context-{$formatted_context}");
        }

        return $classes;
    }

    /**
     * Formats a context string into a safe and CSS-compatible slug.
     *
     * @param string $context The context string to format.
     * @return string Formatted context string.
     */
    private function format_context_string(string $context): string
    {
        // Sanitize the string using WordPress slugify method.
        $sanitized_context = sanitize_title_with_dashes($context);

        // Convert snake_case to kebab-case.
        return str_replace('_', '-', $sanitized_context);
    }

    /**
     * Retrieve dynamic context values.
     * Placeholder method for getting context.
     *
     * @return array Context values for class generation.
     */
    private function get_context_values(): array
    {
        $context = ContextController::determine_context($args = []);

        // Allow customization of the context via filter.
        $context = apply_filters('wp2_body_class_context', $context, $args);

        return [$context];
    }
}

/**
 * Initialize the body class controller.
 */
function wp2_init_themes_elements_body_controller()
{
    new Controller();
}

add_action('init', function () {
    wp2_init_themes_elements_body_controller();
}, 20);
