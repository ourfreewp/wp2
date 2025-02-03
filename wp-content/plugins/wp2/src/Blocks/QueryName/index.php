<?php
// Path: wp-content/plugins/wp2/src/Blocks/QueryName/index.php

namespace WP2\Blocks\QueryName;

$tag = isset($a['tag']) ? $a['tag'] : 'div';

$context = 'fetched-context' ?? 'default-context';

$classes = [
    'wp2-query-name__content'
];

$class_string = esc_attr(implode(' ', $classes));

$title = 'Context Aware Query Name';

$content = sprintf(
    '<%1$s class="%2$s">%3$s</%1$s>',
    esc_attr($tag),
    $class_string,
    esc_html($title)
);
?>

<div class="wp2-query-name wp2-query-name--<?php echo esc_attr($context); ?>" useBlockProps>
    <?php echo $content; ?>
</div>