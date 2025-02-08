<?php
// Path: wp-content/plugins/wp2/src/Blocks/wp2/ArticleContent/index.php

namespace WP2\Blocks\WP2\ArticleContent;

$inner_blocks = sprintf(
    '<InnerBlocks useBlockProps tag="%s" class="%s"/>',
    'div',
    'wp2-article-content'
);
echo $inner_blocks;
