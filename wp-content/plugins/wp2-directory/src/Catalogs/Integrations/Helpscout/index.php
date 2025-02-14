<?php
// Path: wp-content/plugins/wp2-directory/src/Catalogs/Integrations/Helpscout/index.php

namespace WP2_Directory\Catalogs\Integrations\Helpscout;

$inner_blocks = sprintf(
'<InnerBlocks useBlockProps tag="%s" class="%s"/>',
'div',
'wp2-integration'
);

echo $inner_blocks;
