<?php
// Path: wp-content/plugins/wp2/src/Blocks/wp2/BroadcastFooter/index.php

namespace WP2\Blocks\WP2\BroadcastFooter;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'div',
    'wp2-broadcast-footer'
);

echo $inner_blocks;
