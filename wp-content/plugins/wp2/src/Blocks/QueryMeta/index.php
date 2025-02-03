<?php
// Path: wp-content/plugins/wp2/src/Blocks/QueryMeta/index.php

namespace WP2\Blocks\QueryMeta;

$tag = isset($a['tag']) ? $a['tag'] : 'div';

// Define dynamic type and context
$type = isset($a['type']) ? esc_attr($a['type']) : 'default-type';
$context = isset($a['context']) ? esc_attr($a['context']) : 'default-context';

// Base classes
$classes = [
    'wp2-query-meta__content'
];

// Add type and context as additional classes
$classes[] = 'wp2-meta--' . $type;
$classes[] = 'wp2-meta--' . $context;

// Convert class array to string
$class_string = esc_attr(implode(' ', $classes));

// Example metadata values. Replace with dynamic data in production.
$meta_data = [
    'author'    => [
        'label'    => 'Author',
        'value'    => 'John Doe',
        'visible'  => true,
    ],
    'category'  => [
        'label'    => 'Category',
        'value'    => 'News',
        'visible'  => true,
    ],
    'comments'  => [
        'label'    => 'Comments',
        'value'    => '15',
        'visible'  => false,
    ]
];

// Construct meta items
$meta_items = '';

foreach ($meta_data as $key => $data) {
    if ($data['visible']) {
        $meta_items .= sprintf(
            '<span class="wp2-meta__%1$s">%2$s: %3$s</span>',
            esc_attr($key),
            esc_html($data['label']),
            esc_html($data['value'])
        );
    }
}

// Construct content
$content = sprintf(
    '<%1$s class="%2$s">%3$s</%1$s>',
    esc_html($tag),
    $class_string,
    $meta_items
);
?>

<div class="wp2-query-meta wp2-meta--<?php echo esc_attr($type); ?> wp2-meta--<?php echo esc_attr($context); ?>" useBlockProps>
    <?php echo $content; ?>
</div>