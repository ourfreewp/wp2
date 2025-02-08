<?php
// Path: wp-content/plugins/wp2/src/Blocks/wp2/SiteMenu/index.php

namespace WP2\Blocks\WP2\SiteMenu;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'div',
    'wp2-site-menu'
);

echo $inner_blocks;
