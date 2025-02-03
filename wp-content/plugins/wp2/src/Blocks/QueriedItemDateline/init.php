<?php
// Path: wp-content/plugins/wp2/src/Blocks/QueriedItemDateline/init.php
namespace WP2\Blocks\QueriedItemDateline;

use WP2\Helpers\ContextController;

/**
 * DatelineController
 *
 * Dynamically generates and formats datelines for queried items based on context.
 * Supports single posts, archives, custom post types, and other WordPress contexts.
 *
 * @package OnTheWater
 */
class DatelineController
{
    /**
     * Text domain for localization.
     */
    private const TEXT_DOMAIN = 'wp2';

    /**
     * Retrieves the dateline dynamically based on the context.
     *
     * @param array $args Contextual arguments for determining the dateline.
     *                    - `id` (int): The ID of the post or queried item.
     *                    - `format` (string): Date format string. Default 'F j, Y'.
     *                    - `prefix` (string): Text to prepend to the date. Default ''.
     *                    - `suffix` (string): Text to append to the date. Default ''.
     * @return string The constructed dateline HTML.
     */
    public static function get_dateline(array $args = []): string
    {
        // Define default arguments.
        $defaults = [
            'id'     => null,
            'format' => 'F j, Y', // Default date format (e.g., "January 1, 2023").
            'prefix' => '',       // Default no prefix.
            'suffix' => '',       // Default no suffix.
        ];
        $args = wp_parse_args($args, $defaults);

        // Determine the context using ContextController.
        $context = ContextController::determine_context($args);

        // Allow customization of the context via filter.
        $context = apply_filters('wp2_dateline_context', $context, $args);

        // Generate the dateline based on the determined context.
        return self::generate_dateline($context, $args);
    }

    /**
     * Generates the dateline based on the context and arguments.
     *
     * @param string $context The determined context.
     * @param array  $args    The contextual arguments.
     * @return string The constructed dateline HTML.
     */
    private static function generate_dateline(string $context, array $args): string
    {
        $dateline = '';

        // Handle different contexts for the dateline.
        switch ($context) {
            case ContextController::CONTEXT_SINGLE:
            case ContextController::CONTEXT_SINGLE_NEWS:
                $dateline = self::get_post_dateline($args['id'], $args['format']);
                break;

            case ContextController::CONTEXT_POST_TYPE_ARCHIVE:
            case ContextController::CONTEXT_GENERIC:
                $dateline = self::get_archive_dateline($args['format']);
                break;

            case ContextController::CONTEXT_TAXONOMY:
                $dateline = self::get_taxonomy_dateline($args['id'], $args['format']);
                break;

            case ContextController::CONTEXT_AUTHOR:
                $dateline = self::get_author_dateline($args['id']);
                break;

            default:
                $dateline = self::get_generic_dateline($args['format']);
                break;
        }

        // Add prefix and suffix if provided.
        return sprintf(
            '<p class="Dateline">%s%s%s</p>',
            esc_html($args['prefix']),
            $dateline,
            esc_html($args['suffix'])
        );
    }

    /**
     * Constructs a dateline for a single post.
     *
     * @param int    $post_id The ID of the post.
     * @param string $format  The date format.
     * @return string The constructed dateline HTML.
     */
    private static function get_post_dateline(int $post_id, string $format): string
    {
        $date = get_the_date($format, $post_id);

        return sprintf(
            '<time datetime="%s" class="Dateline-date">%s</time>',
            esc_attr(get_the_date('c', $post_id)),
            esc_html($date)
        );
    }

    /**
     * Constructs a dateline for an archive.
     *
     * @param string $format The date format.
     * @return string The constructed dateline HTML.
     */
    private static function get_archive_dateline(string $format): string
    {
        // Example: "Updated January 2023"
        $current_time = current_time('timestamp');
        $date = date_i18n($format, $current_time);

        return sprintf(
            '<time datetime="%s" class="Dateline-date">%s</time>',
            esc_attr(date('c', $current_time)),
            esc_html($date)
        );
    }

    /**
     * Constructs a dateline for a taxonomy term.
     *
     * @param int    $term_id The ID of the taxonomy term.
     * @param string $format  The date format.
     * @return string The constructed dateline HTML.
     */
    private static function get_taxonomy_dateline(int $term_id, string $format): string
    {
        // Example implementation (modify based on taxonomy meta).
        $modified_date = get_term_meta($term_id, '_modified_date', true);

        if ($modified_date) {
            return sprintf(
                '<time datetime="%s" class="Dateline-date">%s</time>',
                esc_attr(date('c', strtotime($modified_date))),
                esc_html(date_i18n($format, strtotime($modified_date)))
            );
        }

        return '';
    }

    /**
     * Constructs a dateline for an author archive.
     *
     * @param int $author_id The ID of the author.
     * @return string The constructed dateline HTML.
     */
    private static function get_author_dateline(int $author_id): string
    {
        $last_post_date = get_user_meta($author_id, 'last_post_date', true);

        if ($last_post_date) {
            return sprintf(
                '<time datetime="%s" class="Dateline-date">%s</time>',
                esc_attr(date('c', strtotime($last_post_date))),
                esc_html(date_i18n('F j, Y', strtotime($last_post_date)))
            );
        }

        return '';
    }

    /**
     * Constructs a generic dateline when no specific context applies.
     *
     * @param string $format The date format.
     * @return string The constructed dateline HTML.
     */
    private static function get_generic_dateline(string $format): string
    {
        $current_time = current_time('timestamp');
        $date = date_i18n($format, $current_time);

        return sprintf(
            '<time datetime="%s" class="Dateline-date">%s</time>',
            esc_attr(date('c', $current_time)),
            esc_html($date)
        );
    }
}


function wp2_get_the_dates($post_id, $get_modified = false, $include_wrapper = true)
{
	$published_formatted = get_the_date('F j, Y', $post_id);
	$published_datetime  = get_the_date('c', $post_id);

	$published_dateline = sprintf(
		'<p class="wp-block-post-date">
				<span class="wp-block-post-date-prefix">Published</span><time datetime="%s">%s</time>
		</p>',
		esc_html($published_datetime),
		esc_html($published_formatted)
	);

	if ($get_modified) {
		$modified_formatted  = get_the_modified_date('F j, Y', $post_id);
		$modified_datetime   = get_the_modified_date('c', $post_id);


		$modified_dateline = sprintf(
			'<p class="wp-block-post-date %s">
				<span class="wp-block-post-date-prefix">Updated</span> <time datetime="%s">%s</time>
		</p>',
			$get_modified ? 'wp-block-post-date__modified-date' : 'wp-block-post-date__modified-date visually-hidden',
			esc_html($modified_datetime),
			esc_html($modified_formatted)
		);
	}

	$dateline = sprintf(
		'<div class="wp-block-wp2-post-dates">
			%s
			%s
		</div>',
		$published_dateline,
		$modified_dateline ?? ''
	);

	if (!$include_wrapper) {
		$dateline = '<div class="wp-block-wp2-dateline">' . $published_formatted . '</div>';
	}

	return $dateline;
}