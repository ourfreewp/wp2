<?php
// Path: wp-content/plugins/wp2/src/Blocks/wp2/QueryFooter/index.php

namespace WP2\Blocks\WP2\QueryFooter;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'footer',
    'wp2-query-footer'
);
echo $inner_blocks;
