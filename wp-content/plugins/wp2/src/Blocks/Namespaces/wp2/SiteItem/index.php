<?php
// Path: wp-content/plugins/wp2/src/Blocks/wp2/SiteItem/index.php

namespace WP2\Blocks\WP2\SiteItem;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'div',
    'wp2-site-item'
);

echo $inner_blocks;
