<?php
// Path: wp-content/plugins/wp2/src/Blocks/SiteSearch/index.php

namespace WP2\Blocks\SiteSearch;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'div',
    'wp2-site-search'
);

echo $inner_blocks;
