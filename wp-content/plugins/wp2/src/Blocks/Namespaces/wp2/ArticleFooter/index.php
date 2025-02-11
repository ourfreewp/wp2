<?php
// Path: wp-content/plugins/wp2/src/Blocks/wp2/ArticleFooter/index.php

namespace WP2\Blocks\WP2\ArticleFooter;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'footer',
    'wp2-article-footer'
);
echo $inner_blocks;
