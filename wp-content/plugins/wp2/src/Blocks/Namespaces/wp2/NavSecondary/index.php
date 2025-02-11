<?php
// Path: wp-content/plugins/wp2/src/Blocks/wp2/NavbarSecondary/index.php

namespace WP2\Blocks\WP2\NavbarSecondary;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'nav',
    'wp2-navbar wp2-navbar--secondary'
);
echo $inner_blocks;
