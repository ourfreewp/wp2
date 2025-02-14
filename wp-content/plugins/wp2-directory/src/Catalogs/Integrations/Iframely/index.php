<?php
// Path: wp-content/plugins/wp2-directory/src/Catalogs/Integrations/Iframely/index.php

namespace WP2_Directory\Catalogs\Integrations\Iframely;

$inner_blocks = sprintf(
'<InnerBlocks useBlockProps tag="%s" class="%s"/>',
'div',
'wp2-integration'
);

echo $inner_blocks;
