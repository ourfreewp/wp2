<?php
// Path: wp-content/plugins/wp2/src/Blocks/NavbarPrimary/index.php

namespace WP2\Blocks\NavbarPrimary;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'nav',
    'wp2-navbar wp2-navbar--primary'
);
echo $inner_blocks;
?>
