<?php
// Path: wp-content/plugins/wp2/src/Blocks/wp2/Root/index.php

namespace WP2\Blocks\WP2\SiteRoot;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'div',
    'wp2-root'
);
echo $inner_blocks;
