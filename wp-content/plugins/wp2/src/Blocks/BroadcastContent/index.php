<?php
// Path: wp-content/plugins/wp2/src/Blocks/BroadcastContent/index.php

namespace WP2\Blocks\BroadcastContent;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'div',
    'wp2-broadcast-content'
);

echo $inner_blocks;
