<?php
// Path: wp-content/plugins/wp2/src/Blocks/QueryContent/index.php

namespace WP2\Blocks\QueryContent;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'section',
    'wp2-query-content'
);
echo $inner_blocks;
?>
