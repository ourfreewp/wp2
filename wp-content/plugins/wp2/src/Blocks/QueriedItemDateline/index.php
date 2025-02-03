<?php
// Path: wp-content/plugins/wp2/src/Blocks/QueriedItemDateline/index.php

namespace WP2\Blocks\QueriedItemDateline;

$context = 'fetched-context' ?? 'default-context';

$classes = [
    'wp2-dateline__content'
];

$class_string = esc_attr(implode(' ', $classes));

$response = [
    'published' => [
        'prefix'    => 'Published',
        'formatted' => 'January 1, 2021',
        'datetime'  => '2021-01-01',
        'visible'   => true
    ],
    'modified'  => [
        'prefix'    => 'Updated',
        'formatted' => 'January 1, 2021',
        'datetime'  => '2021-01-01',
        'visible'   => false
    ]
];

$dateline = '';
$published = '';
$modified  = '';

if ($response['published']['visible']) {
    $published = sprintf(
        '<span class="wp2-dateline__published"><time datetime="%3$s">%1$s %2$s</time></span>',
        esc_html($response['published']['prefix']),
        esc_html($response['published']['formatted']),
        esc_attr($response['published']['datetime'])
    );
}

if ($response['modified']['visible']) {
    $modified = sprintf(
        '<span class="wp2-dateline__modified"><time datetime="%3$s">%1$s %2$s</time></span>',
        esc_html($response['modified']['prefix']),
        esc_html($response['modified']['formatted']),
        esc_attr($response['modified']['datetime'])
    );
}

if ($published && $modified) {
    $dateline = sprintf(
        '<div class="wp2-dateline">%1$s %2$s</div>',
        $published,
        $modified
    );
} else {
    $dateline = sprintf(
        '<div class="wp2-dateline">%1$s</div>',
        $published ?: $modified
    );
}

$content = sprintf(
    '<div class="%1$s">%2$s</div>',
    $class_string,
    $dateline
);
?>

<div class="wp2-dateline wp2-dateline--<?php echo esc_attr($context); ?>" useBlockProps>
    <?php echo $content; ?>
</div>