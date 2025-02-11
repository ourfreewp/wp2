<?php
// Path: wp-content/plugins/wp2/src/Blocks/wp2/BroadcastContent/index.php

namespace WP2\Blocks\WP2\BroadcastContent;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'div',
    'wp2-broadcast-content'
);

echo $inner_blocks;
