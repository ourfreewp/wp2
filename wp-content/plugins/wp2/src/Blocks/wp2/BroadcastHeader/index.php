<?php
// Path: wp-content/plugins/wp2/src/Blocks/wp2/BroadcastHeader/index.php

namespace WP2\Blocks\WP2\BroadcastHeader;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'div',
    'wp2-broadcast-header'
);

echo $inner_blocks;
