<?php
// Path: wp-content/plugins/wp2/src/Blocks/wp2/ItemTerm/init.php
namespace WP2\Blocks\WP2\ItemTerm;

use WP2\Helpers\Context\Controller as ContextController;

/**
 * TermController
 *
 * Dynamically retrieves and formats a featured term for queried items.
 * Supports highlighting categories, tags, or custom taxonomies.
 *
 * @package WP2
 */
class TermController
{
    /**
     * Text domain for localization.
     */
    private const TEXT_DOMAIN = 'wp2';

    /**
     * Retrieves a featured term for a queried item based on context.
     *
     * @param array $args Contextual arguments for determining the term.
     *                    - `id` (int): The ID of the post or queried item.
     *                    - `taxonomy` (string): The taxonomy to retrieve terms from. Default 'category'.
     *                    - `prefix` (string): Text to prepend to the term. Default ''.
     *                    - `suffix` (string): Text to append to the term. Default ''.
     *                    - `link` (bool): Whether to link the term to its archive. Default true.
     * @return string The constructed term HTML.
     */
    public static function get_featured_term(array $args = []): string
    {
        // Define default arguments.
        $defaults = [
            'id'       => null,
            'taxonomy' => 'category',
            'prefix'   => '',
            'suffix'   => '',
            'link'     => true,
        ];
        $args = wp_parse_args($args, $defaults);

        // Determine the context using ContextController.
        $context = ContextController::determine_context($args);

        // Allow customization of the context via filter.
        $context = apply_filters('wp2_term_context', $context, $args);

        // Generate the term based on the determined context.
        return self::generate_term($context, $args);
    }

    /**
     * Generates the term HTML based on the context and arguments.
     *
     * @param string $context The determined context.
     * @param array  $args    The contextual arguments.
     * @return string The constructed term HTML.
     */
    private static function generate_term(string $context, array $args): string
    {
        // Handle different contexts for retrieving terms.
        switch ($context) {
            case ContextController::CONTEXT_SINGLE:
            case ContextController::CONTEXT_SINGLE_NEWS:
                return self::get_post_featured_term($args['id'], $args['taxonomy'], $args['prefix'], $args['suffix'], $args['link']);

            case ContextController::CONTEXT_TAXONOMY:
                return self::get_taxonomy_term($args['id'], $args['prefix'], $args['suffix'], $args['link']);

            default:
                return '';
        }
    }

    /**
     * Retrieves a featured term for a single post.
     *
     * @param int    $post_id  The ID of the post.
     * @param string $taxonomy The taxonomy to retrieve terms from.
     * @param string $prefix   Text to prepend to the term.
     * @param string $suffix   Text to append to the term.
     * @param bool   $link     Whether to link the term to its archive.
     * @return string The constructed term HTML.
     */
    private static function get_post_featured_term(int $post_id, string $taxonomy, string $prefix, string $suffix, bool $link): string
    {
        // Retrieve the terms for the post.
        $terms = get_the_terms($post_id, $taxonomy);

        if (is_wp_error($terms) || empty($terms)) {
            return '';
        }

        // Use the first term as the featured term.
        $term = $terms[0];
        $term_name = esc_html($term->name);

        // Optionally link the term.
        if ($link) {
            $term_name = sprintf(
                '<a href="%s" class="Featured-term">%s</a>',
                esc_url(get_term_link($term)),
                $term_name
            );
        }

        // Construct the term HTML with prefix and suffix.
        return sprintf(
            '<span class="Featured-term-wrapper">%s%s%s</span>',
            esc_html($prefix),
            $term_name,
            esc_html($suffix)
        );
    }

    /**
     * Retrieves a taxonomy term directly (e.g., for taxonomy archives).
     *
     * @param int    $term_id The ID of the taxonomy term.
     * @param string $prefix  Text to prepend to the term.
     * @param string $suffix  Text to append to the term.
     * @param bool   $link    Whether to link the term to its archive.
     * @return string The constructed term HTML.
     */
    private static function get_taxonomy_term(int $term_id, string $prefix, string $suffix, bool $link): string
    {
        $term = get_term($term_id);

        if (is_wp_error($term) || !$term) {
            return '';
        }

        $term_name = esc_html($term->name);

        // Optionally link the term.
        if ($link) {
            $term_name = sprintf(
                '<a href="%s" class="Featured-term">%s</a>',
                esc_url(get_term_link($term)),
                $term_name
            );
        }

        // Construct the term HTML with prefix and suffix.
        return sprintf(
            '<span class="Featured-term-wrapper">%s%s%s</span>',
            esc_html($prefix),
            $term_name,
            esc_html($suffix)
        );
    }
}

function wp22_post_label($post_id, $label_class = '')
{

    // Check for current post's post label.
    if (isset(get_post_meta($post_id, '_wp2_post_label', false)[0])) {
        $current_label = get_post_meta($post_id, '_wp2_post_label', false)[0];
    } else {
        $current_label = false;
    }
    // If this current post meta is a valid post label, lets use it.
    if ($current_label && ! is_wp_error($current_label['url'])) {

        $post_label = $current_label;
        // If we're missing a valid saved post label meta field, let's build one.
    } else {

        $cats = get_the_terms($post_id, 'category');
        // Check if the current post has categories attached to it.
        if ($cats) {

            $top_cats = [];

            foreach ($cats as $cat) {
                if (0 === $cat->parent && 'uncategorized' !== $cat->slug) {
                    $top_cats[] = $cat;
                }
            }
        }
        // If the post has top level categories, we'll use those to set default label.
        if (! empty($top_cats)) {

            $set_cat = $top_cats[0];

            $cat_url = get_term_link($set_cat->slug, 'category');
            if (is_wp_error($cat_url)) {
                $cat_url = '#';
            }

            $post_label = [
                'slug' => $set_cat->slug,
                'name' => $set_cat->name,
                'url'  => esc_url($cat_url),
            ];
        } else {
            // If the post doesn't have top level categories, we'll use the post type.
            $post_type = get_post_type_object(get_post_type($post_id));
            $post_label = [
                'slug' => $post_type->name,
                'name' => $post_type->labels->singular_name,
                'url'  => get_post_type_archive_link($post_type->name),
            ];
        }

        update_post_meta($post_id, '_wp2_post_label', $post_label);
    }
?>
    <a href="<?php echo esc_url($post_label['url']); ?>" class="PostLabel <?php echo esc_attr($label_class); ?>"><?php echo esc_html($post_label['name']); ?></a>
<?php
}
