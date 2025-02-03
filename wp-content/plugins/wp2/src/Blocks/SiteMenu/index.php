<?php
// Path: wp-content/plugins/wp2/src/Blocks/SiteMenu/index.php

namespace WP2\Blocks\SiteMenu;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'div',
    'wp2-site-menu'
);

echo $inner_blocks;
