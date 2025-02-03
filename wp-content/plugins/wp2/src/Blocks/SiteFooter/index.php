<?php
// Path: wp-content/plugins/wp2/src/Blocks/SiteFooter/index.php

namespace WP2\Blocks\SiteFooter;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'footer',
    'wp2-site-footer'
);
echo $inner_blocks;
?>
