<?php
// Path: wp-content/plugins/wp2/src/Blocks/SiteContent/index.php

namespace WP2\Blocks\SiteContent;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'div',
    'wp2-site-content'
);
echo $inner_blocks;
?>
