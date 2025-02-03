<?php
// Path: wp-content/plugins/wp2/src/Blocks/ContentSecondary/index.php

namespace WP2\Blocks\ContentSecondary;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'div',
    'wp2-content-secondary'
);
echo $inner_blocks;
?>
