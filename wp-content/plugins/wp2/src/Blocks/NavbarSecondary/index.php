<?php
// Path: wp-content/plugins/wp2/src/Blocks/NavbarSecondary/index.php

namespace WP2\Blocks\NavbarSecondary;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'nav',
    'wp2-navbar wp2-navbar--secondary'
);
echo $inner_blocks;
?>
