<?php
// Path: wp-content/plugins/wp2/src/Blocks/wp2/SiteHeader/index.php

namespace WP2\Blocks\WP2\SiteHeader;

$allowed_blocks = ['core/group', 'wp2/navbar-primary', 'wp2/navbar-secondary'];

$tag = 'div';

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'header',
    'wp2-site-header'
);

echo $inner_blocks;
