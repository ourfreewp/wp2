<?php
// Path: wp-content/plugins/wp2-directory/src/Blocks/Namespaces/wp2-directory/CatalogListing/index.php

namespace WP2_Directory\Blocks\Namespaces\WP2_Directory\CatalogListing;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'div',
    'wp2-directory-catalog-listing'
);

echo $inner_blocks;
