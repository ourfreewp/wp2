<?php
// Path: wp-content/plugins/wp2-directory/src/Catalogs/Modules/New/index.php

namespace WP2_Directory\Catalogs\Modules\New;

$inner_blocks = sprintf(
'<InnerBlocks useBlockProps tag="%s" class="%s"/>',
'div',
'wp2-module'
);

echo $inner_blocks;
