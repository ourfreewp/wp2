<?php
/**
 * Single Placement Block
 *
 * Provides a generic wrapper for loading dynamic content.
 *
 * @package WP2\Blocks\SinglePlacement
 */

namespace WP2\Blocks\SinglePlacement;

$attributes = isset( $attributes ) ? $attributes : [];

/**
 * Extract attributes with defaults.
 */

// Wrapper ID: A unique ID for the wrapper element.
$id = ! empty( $attributes['id'] ) 
    ? esc_attr( $attributes['id'] ) 
    : '';

// Wrapper Classes: Custom classes for the wrapper.
$classes = isset( $attributes['classes'] ) 
    ? (array) $attributes['classes'] 
    : array();

// Additional Attributes: Custom data-* or other attributes.
$data_attributes = isset( $attributes['data'] ) 
    ? (array) $attributes['data'] 
    : array();

// Construct class attribute.
$class_attribute = esc_attr( implode( ' ', array_filter( $classes ) ) );

// Construct data attributes.
$data_string = '';
foreach ( $data_attributes as $key => $value ) {
    $data_string .= sprintf( ' data-%s="%s"', esc_attr( $key ), esc_attr( $value ) );
}

/**
 * Generate output.
 */
?>
<div useBlockProps
    <?php if ( $id ) : ?>id="<?php echo $id; ?>"<?php endif; ?> 
    class="wp2-placement<?php echo $class_attribute; ?>" 
    <?php echo $data_string; ?>
>
    <?php
    /**
     * Hook for adding dynamic content to the placement.
     * Usage:
     *   add_action( 'wp2_single_placement_content', function() {
     *       echo 'Dynamic content here.';
     *   } );
     */
    do_action( 'wp2_single_placement_content', $attributes );
    ?>
</div>