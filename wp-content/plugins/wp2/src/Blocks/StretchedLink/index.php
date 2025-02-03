<?php
// Path: wp-content/plugins/wp2/src/Blocks/StretchedLink/index.php

namespace WP2\Blocks\StretchedLink;

$href  = $isEditor ? '#' : ($attributes['link']['url'] ?? '');
$title = $attributes['link']['title'] ?? '';
?>

<a useBlockProps href="<?php echo esc_url($href); ?>" class="wp2-stretched-link">
    <span class="screen-reader-text"><?php echo esc_html($title); ?></span>
</a>