<?php
// Path: wp-content/plugins/wp2/src/Blocks/QueriedItemMedia/index.php

namespace WP2\Blocks\QueriedItemMedia;

$context = 'fetched-context' ?? 'default-context';

// Example $response payload
$response = [
    'url' => 'https://placehold.co/1200x630',
    'alt' => 'Sample Alt Text',
    'type' => 'placeholder' // Options: 'image', 'video', 'placeholder'
];

// Construct classes dynamically
$classes = [
    'wp2-media__content',
    'wp2-media--' . $response['type'],
    'wp2-media--' . $context
];

$class_string = esc_attr(implode(' ', $classes));

// Build media element
$media_element = '';

if ($response['type'] === 'image') {
    $media_element = sprintf(
        '<img class="wp2-media__image" src="%1$s" alt="%2$s" />',
        esc_url($response['url']),
        esc_attr($response['alt'])
    );
} elseif ($response['type'] === 'video') {
    $media_element = sprintf(
        '<video class="wp2-media__video" controls>
            <source src="%1$s" type="video/mp4">
            Your browser does not support the video tag.
        </video>',
        esc_url($response['url'])
    );
} elseif ($response['type'] === 'placeholder') {
    $media_element = sprintf(
        '<img class="wp2-media__placeholder" src="%1$s" alt="%2$s" />',
        esc_url($response['url']),
        esc_attr($response['alt'])
    );
}

// Construct the content block
$content = sprintf(
    '<div class="%1$s">%2$s</div>',
    $class_string,
    $media_element
);
?>

<div class="wp2-media wp2-media--<?php echo esc_attr($response['type']); ?> wp2-media--<?php echo esc_attr($context); ?>" useBlockProps>
    <?php echo $content; ?>
</div>