<?php
// Path: wp-content/plugins/wp2/src/Blocks/wp2/SiteAlert/index.php

namespace WP2\Blocks\SiteAlert;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'div',
    'wp2-site-alert'
);

echo $inner_blocks;
