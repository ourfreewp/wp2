<?php
// Path: wp-content/plugins/wp2/src/Blocks/QueryFooter/index.php

namespace WP2\Blocks\QueryFooter;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'footer',
    'wp2-query-footer'
);
echo $inner_blocks;
?>
