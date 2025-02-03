<?php
// Path: wp-content/plugins/wp2/src/Blocks/SiteItem/index.php

namespace WP2\Blocks\SiteItem;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'div',
    'wp2-site-item'
);

echo $inner_blocks;
