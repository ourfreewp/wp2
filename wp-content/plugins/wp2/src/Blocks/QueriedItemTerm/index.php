<?php
// Path: wp-content/plugins/wp2/src/Blocks/QueriedItemTerm/index.php

namespace WP2\Blocks\QueriedItemTerm;

$context = 'fetched-context' ?? 'default-context';

$classes = [
    'wp2-term__content'
];

$class_string = esc_attr(implode(' ', $classes));

$term_content = 'Context Aware Term';
$term_link    = $isEditor ? '#' : 'https://example.com';

$content = sprintf(
    '<a href="%3$s" class="%1$s">%2$s</a>',
    $class_string,
    esc_html($term_content),
    esc_url($term_link)
);
?>

<div class="wp2-term wp2-term--<?php echo esc_attr($context); ?>" useBlockProps>
    <?php echo $content; ?>
</div>