<?php
// Path: wp-content/plugins/wp2/src/Blocks/wp2/ItemSubtitle/index.php

namespace WP2\Blocks\WP2\ItemSubtitle;

$tag = isset($a['tag']) ? $a['tag'] : 'div';

$context = 'fetched-context' ?? 'default-context';

$classes = [
    'wp2-subtitle__content'
];

$class_string = esc_attr(implode(' ', $classes));

$title = 'Context Aware Subtitle';

$content = sprintf(
    '<%1$s class="%2$s">%3$s</%1$s>',
    esc_html($tag),
    $class_string,
    esc_html($title)
);
?>

<div class="wp2-subtitle wp2-subtitle--<?php echo esc_attr($context); ?>" useBlockProps>
    <?php echo $content; ?>
</div>