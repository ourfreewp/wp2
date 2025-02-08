<?php
// Path: wp-content/plugins/wp2/src/Blocks/wp2/SiteContent/index.php

namespace WP2\Blocks\WP2\SiteContent;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'div',
    'wp2-site-content'
);
echo $inner_blocks;
