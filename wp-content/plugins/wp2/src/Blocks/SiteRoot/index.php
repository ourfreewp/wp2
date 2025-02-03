<?php
// Path: wp-content/plugins/wp2/src/Blocks/SiteRoot/index.php

namespace WP2\Blocks\SiteRoot;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'div',
    'wp2-site-root'
);
echo $inner_blocks;
?>
