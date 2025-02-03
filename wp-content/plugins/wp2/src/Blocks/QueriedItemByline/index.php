<?php
// Path: wp-content/plugins/wp2/src/Blocks/QueriedItemByline/index.php

namespace WP2\Blocks\QueriedItemByline;

$context = 'fetched-context' ?? 'default-context';

$classes = [
    'wp2-byline__content'
];

$class_string = esc_attr(implode(' ', $classes));

$item_content = 'Context Aware Byline';

$content = sprintf(
    '<div class="%1$s">%2$s</div>',
    $class_string,
    esc_html($item_content)
);
?>

<div class="wp2-byline wp2-byline--<?php echo esc_attr($context); ?>" useBlockProps>
    <?php echo $content; ?>
</div>