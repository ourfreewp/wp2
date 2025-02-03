<?php
// Path: wp-content/plugins/wp2/src/Blocks/QueriedItemPhoto/index.php

namespace WP2\Blocks\QueriedItemPhoto;

$context = 'fetched-context' ?? 'default-context';

$response = [
    'url' => 'https://example.com/wp-content/uploads/profile-avatar.jpg', // Avatar URL
    'alt' => 'User Avatar', // Alt text for the avatar
    'type' => 'photo' // Static type for avatar
];

// Construct classes dynamically
$classes = [
    'wp2-photo__content',
    'wp2-photo--' . $response['type'],
    'wp2-photo--' . $context
];

$class_string = esc_attr(implode(' ', $classes));

// Build the photo element
$photo_element = sprintf(
    '<img class="wp2-photo__image" src="%1$s" alt="%2$s" />',
    esc_url($response['url']),
    esc_attr($response['alt'])
);

$content = sprintf(
    '<div class="%1$s">%2$s</div>',
    $class_string,
    $photo_element
);
?>

<div class="wp2-photo wp2-photo--<?php echo esc_attr($response['type']); ?> wp2-photo--<?php echo esc_attr($context); ?>" useBlockProps>
    <?php echo $content; ?>
</div>