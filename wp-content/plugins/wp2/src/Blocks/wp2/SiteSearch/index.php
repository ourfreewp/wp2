<?php
// Path: wp-content/plugins/wp2/src/Blocks/wp2/SiteSearch/index.php

namespace WP2\Blocks\WP2\SiteSearch;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'div',
    'wp2-site-search'
);

echo $inner_blocks;
