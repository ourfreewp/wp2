<?php
// Path: wp-content/plugins/wp2/src/Blocks/ArticleHeader/index.php

namespace WP2\Blocks\ArticleHeader;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'header',
    'wp2-article-header'
);
echo $inner_blocks;
?>
