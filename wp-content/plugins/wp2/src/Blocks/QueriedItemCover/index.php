<?php
// Path: wp-content/plugins/wp2/src/Blocks/QueriedItemCover/index.php

namespace WP2\Blocks\QueriedItemCover;

$context = 'fetched-context' ?? 'default-context';

$response = [
    'url' => 'https://example.com/wp-content/uploads/banner-image.jpg', // Banner image URL
    'alt' => 'Cover Image', // Alt text for the banner
    'type' => 'cover' // Static type for cover
];

// Construct classes dynamically
$classes = [
    'wp2-cover__content',
    'wp2-cover--' . $response['type'],
    'wp2-cover--' . $context
];

$class_string = esc_attr(implode(' ', $classes));

// Build the cover element
$cover_element = sprintf(
    '<img class="wp2-cover__image" src="%1$s" alt="%2$s" />',
    esc_url($response['url']),
    esc_attr($response['alt'])
);

$content = sprintf(
    '<div class="%1$s">%2$s</div>',
    $class_string,
    $cover_element
);
?>

<div class="wp2-cover wp2-cover--<?php echo esc_attr($response['type']); ?> wp2-cover--<?php echo esc_attr($context); ?>" useBlockProps>
    <?php echo $content; ?>
</div>