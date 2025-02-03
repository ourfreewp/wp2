<?php
// Path: wp-content/plugins/wp2/src/Blocks/QueriedItemTitle/index.php

namespace WP2\Blocks\QueriedItemTitle;

$tag = isset($a['tag']) ? $a['tag'] : 'div';

$context = 'fetched-context' ?? 'default-context';

$classes = [
    'wp2-title__content'
];

$class_string = esc_attr(implode(' ', $classes));

$title = 'Context Aware Title';

$content = sprintf(
    '<%1$s class="%2$s">%3$s</%1$s>',
    esc_html($tag),
    $class_string,
    esc_html($title)
);
?>

<div class="wp2-title wp2-title--<?php echo esc_attr($context); ?>" useBlockProps>
    <?php echo $content; ?>
</div>