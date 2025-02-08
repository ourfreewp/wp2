<?php
// Path: wp-content/plugins/wp2/src/Blocks/wp2/QueryContent/index.php

namespace WP2\Blocks\WP2\QueryContent;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'section',
    'wp2-query-content'
);
echo $inner_blocks;
