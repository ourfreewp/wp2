<?php
// Path: wp-content/plugins/wp2/src/Blocks/wp2/ItemTitle/index.php

namespace WP2\Blocks\WP2\ItemTitle;

use \WP2\Helpers\ContextController as ContextController;

$context_controller = new ContextController();

$context_args = [
    'block' => $block ?? [],
    'attributes' => $a ?? [],
];

$context = $context_controller->determine_context($context_args);

$title_controller = new TitleController();

$post_id = $block['postId'] ?? null;
$post_type = $block['postType'] ?? null;

$title = $title_controller->get_title(
    $context,
    [
        'type' => 'single_post',
        'post_id' => $post_id,
        'post_type' => $post_type,
    ]
);

$tag = isset($a['tag']) ? $a['tag'] : 'div';

$classes = [
    'wp2-title__content'
];

$class_string = esc_attr(implode(' ', $classes));

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