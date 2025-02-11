<?php
// Path: wp-content/plugins/wp2/src/Blocks/wp2/MainHeader/index.php

namespace WP2\Blocks\WP2\MainHeader;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'div',
    'wp2-main-header'
);
echo $inner_blocks;
