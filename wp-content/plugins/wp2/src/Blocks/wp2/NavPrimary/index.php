<?php
// Path: wp-content/plugins/wp2/src/Blocks/wp2/NavbarPrimary/index.php

namespace WP2\Blocks\WP2\NavbarPrimary;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'nav',
    'wp2-navbar wp2-navbar--primary'
);
echo $inner_blocks;
