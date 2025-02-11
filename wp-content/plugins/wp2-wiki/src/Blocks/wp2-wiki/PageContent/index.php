<?php
// Path: wp-content/plugins/wp2-wiki/src/Blocks/PageContent/index.php

namespace WP2\Wiki\Blocks\PageContent;

$post_id = $b['postId'];

if (!$post_id) {
    return;
}

$post = get_post($post_id);

$readme_html = rwmb_meta('_wp2_wiki_readme_html', array(), $post_id);

?>

<div useBlockProps class="wp2-wiki-readme">
    <?php echo wp_kses_post($readme_html); ?>
</div>