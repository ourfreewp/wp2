<?php
// Path: wp-content/plugins/wp2/src/Blocks/QueryDescription/index.php

namespace WP2\Blocks\QueryDescription;

$tag = isset($a['tag']) ? $a['tag'] : 'div';

$context = 'fetched-context' ?? 'default-context';

$classes = [
    'wp2-query-description__content'
];

$class_string = esc_attr(implode(' ', $classes));

$title = 'Context Aware Query Description';

$content = sprintf(
    '<%1$s class="%2$s">%3$s</%1$s>',
    esc_attr($tag),
    $class_string,
    esc_html($title)
);
?>

<div class="wp2-query-description wp2-query-description--<?php echo esc_attr($context); ?>" useBlockProps>
    <?php echo $content; ?>
</div>