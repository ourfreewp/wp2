<?php
// Path: wp-content/plugins/wp2-directory/src/Blocks/Namespaces/wp2-directory/Catalog/index.php

namespace WP2_Directory\Blocks\Namespaces\WP2_Directory\Catalog;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'div',
    'wp2-directory-catalog'
);

echo $inner_blocks;
