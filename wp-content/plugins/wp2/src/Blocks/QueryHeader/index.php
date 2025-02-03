<?php
// Path: wp-content/plugins/wp2/src/Blocks/QueryHeader/index.php

namespace WP2\Blocks\QueryHeader;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'header',
    'wp2-query-header'
);
echo $inner_blocks;
?>
