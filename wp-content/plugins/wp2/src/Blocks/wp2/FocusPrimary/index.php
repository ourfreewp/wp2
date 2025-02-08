<?php
// Path: wp-content/plugins/wp2/src/Blocks/wp2/ContentPrimary/index.php

namespace WP2\Blocks\WP2\ContentPrimary;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'div',
    'wp2-content-primary'
);
echo $inner_blocks;
