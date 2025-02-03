<?php
// Path: wp-content/plugins/wp2/src/Blocks/ContentPrimary/index.php

namespace WP2\Blocks\ContentPrimary;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'div',
    'wp2-content-primary'
);
echo $inner_blocks;
?>
