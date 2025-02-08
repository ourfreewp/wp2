<?php
// Path: wp-content/plugins/wp2/src/Blocks/wp2/MainContent/index.php

namespace WP2\Blocks\WP2\MainContent;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'div',
    'wp2-main-content'
);
echo $inner_blocks;
