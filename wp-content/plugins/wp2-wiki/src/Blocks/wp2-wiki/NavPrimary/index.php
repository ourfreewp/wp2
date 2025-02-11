<?php
// Path: wp-content/plugins/wp2-wiki/src/Blocks/NavPrimary/index.php

namespace WP2\Wiki\Blocks\NavPrimary;

$prefix = 'wp2_wiki';
$type   = 'readme';

$tax_collection = $prefix . '_collection';
$tax_section    = $prefix . '_section';
$single_readme  = $prefix . '_readme';


$nav_sections = [];

$all_sections = get_terms($tax_section, ['hide_empty' => false]);

foreach ($all_sections as $section) {
    $nav_sections[$section->term_id] = [
        'name' => $section->name,
        'slug' => $section->slug,
        'id'   => $section->term_id,
        'description' => $section->description,
        'readmes' => [],
    ];
}

$all_readmes = get_posts([
    'post_type' => $single_readme,
    'posts_per_page' => -1,
]);

$readme_data = [];

foreach ($all_readmes as $readme) {
    $sections = wp_get_post_terms($readme->ID, $tax_section, ['fields' => 'ids']);
    $readme_data[$readme->ID] = [
        'title' => $readme->post_title,
        'slug' => $readme->post_name,
        'id'   => $readme->ID,
        'link' => get_permalink($readme->ID),
        'sections' => $sections,
    ];
}


foreach ($readme_data as $readme) {
    foreach ($readme['sections'] as $section) {
        $nav_sections[$section]['readmes'][] = $readme;
    }
}

$sorted_sections = [];

foreach ($nav_sections as $section) {
    $sorted_sections[$section['id']] = $section;

    $sorted_readmes = [];

    foreach ($section['readmes'] as $readme) {
        $sorted_readmes[$readme['id']] = $readme;
    }
}


?>


<div class="wp2-nav wp2-nav--primary" useBlockProps>

    <div class="wp2-nav__inner">

        <?php foreach ($sorted_sections as $section) : ?>
            <div class="wp2-nav__section">
                <h3 class="wp2-nav__section-title"><?php echo $section['name']; ?></h3>
                <ul class="wp2-nav__section-list">
                    <?php foreach ($section['readmes'] as $readme) : ?>
                        <li class="wp2-nav__section-item">
                            <a href="<?php echo $readme['link']; ?>" class="wp2-nav__section-link">
                                <?php echo $readme['title']; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>

    </div>

</div>