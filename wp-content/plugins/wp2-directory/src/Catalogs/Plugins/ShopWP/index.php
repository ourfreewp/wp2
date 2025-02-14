<?php
// Path: wp-content/plugins/wp2-directory/src/Catalogs/Plugins/ShopWP/index.php

namespace WP2_Directory\Catalogs\Plugins\ShopWP;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'div',
    'wp2-directory-plugin'
);

echo $inner_blocks;
