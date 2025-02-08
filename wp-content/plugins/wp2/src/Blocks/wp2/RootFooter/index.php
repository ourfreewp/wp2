<?php
// Path: wp-content/plugins/wp2/src/Blocks/wp2/SiteFooter/index.php

namespace WP2\Blocks\WP2\SiteFooter;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'footer',
    'wp2-site-footer'
);
echo $inner_blocks;
