<?php
// Path: wp-content/plugins/wp2/src/Blocks/wp2/MainFooter/index.php

namespace WP2\Blocks\WP2\MainFooter;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'div',
    'wp2-main-footer'
);
echo $inner_blocks;
