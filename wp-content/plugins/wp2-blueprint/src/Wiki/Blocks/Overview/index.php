<?php

namespace WP2\Demo\Blocks\Wizard;

/**
 * ---------------------------------------------------------------------------
 * Configuration Variables
 * ---------------------------------------------------------------------------
 */
$prefix    = 'wp2_demo';
$post_type = $prefix . '_slide';
$taxonomy  = $prefix . '_slide_section';

/**
 * ---------------------------------------------------------------------------
 * Retrieve Slides
 * ---------------------------------------------------------------------------
 */
$slides = get_posts([
    'post_type'      => $post_type,
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
]);

/**
 * ---------------------------------------------------------------------------
 * Prepare Slides Data
 * ---------------------------------------------------------------------------
 */
$slides_data = array_map(function ($slide) {

    // Prepare Slide Data
    $title   = apply_filters('the_title', $slide->post_title);
    $content = apply_filters('the_content', $slide->post_content);
    $excerpt = $slide->post_excerpt;
    $slug    = 'wp2-demo-' . $slide->post_name;
    $menu_order = $slide->menu_order ?? 0;
    $image_id = get_post_thumbnail_id($slide->ID);
    $image = wp_get_attachment_image_url($image_id, 'full');
    $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);

    return [
        'id'      => $slide->ID,
        'title'   => $title,
        'content' => $content,
        'excerpt' => $excerpt,
        'slug'    => $slug,
        'order'   => $menu_order,
        'image' => [
            'id' => $image_id,
            'url' => $image,
            'alt' => $image_alt,
        ],
    ];
}, $slides);

/**
 * ---------------------------------------------------------------------------
 * Group Slides by Section (Term)
 * ---------------------------------------------------------------------------
 */
$slides_by_section = [];

foreach ($slides_data as $slide) {

    $terms = wp_get_object_terms($slide['id'], $taxonomy);

    if (! is_wp_error($terms) && ! empty($terms)) {

        foreach ($terms as $term) {
            $term_id          = $term->term_id;
            $term_name        = $term->name;
            $term_slug        = $term->slug;
            $term_description = $term->description;
            $term_order       = rwmb_meta($prefix . 'order', ['object_type' => 'term'], $term_id);
            $term_content     = rwmb_meta($prefix . 'content', ['object_type' => 'term'], $term_id);
            $term_theme       = rwmb_meta($prefix . 'theme', ['object_type' => 'term'], $term_id);

            global $wp_embed;
            $term_content = do_shortcode(wpautop($wp_embed->autoembed($term_content)));

            if (! isset($slides_by_section[$term_id])) {

                $slides_by_section[$term_id] = [
                    'term'   => [
                        'id'          => $term_id,
                        'name'        => $term_name,
                        'slug'        => $term_slug,
                        'description' => $term_description,
                        'order'       => $term_order,
                        'content'     => $term_content,
                        'theme'       => $term_theme,
                        'count'       => 0,
                    ],
                    'slides' => [],
                ];
            }

            $slides_by_section[$term_id]['slides'][] = $slide;
        }

        $slide_count = count($slides_by_section);

        $slides_by_section[$term_id]['term']['count'] = $slide_count;

        $slides_by_section = array_filter($slides_by_section, function ($section) {
            return $section['term']['order'] > 0;
        });
    }
}

usort($slides_by_section, function ($a, $b) {
    return $a['term']['order'] - $b['term']['order'];
});
?>

<div class="wp2-demo-wizard">

    <div class="wp2-demo-wizard__inner">

        <nav id="WP2DemoWizardMenu" class="wp2-wizard-menu">
            <div class="wp2-scroller">
                <ul class="wp2-scroller__inner">
                    <?php foreach ($slides_by_section as $index => $menu_item) : ?>
                        <?php
                        $menu_term_name  = $menu_item['term']['name'];
                        $menu_term_slug  = $menu_item['term']['slug'];
                        $menu_active     = ($index === 0) ? 'active' : '';
                        $order           = $menu_item['term']['order'];

                        // not 'missing-section' slug
                        if (empty($menu_term_name)) {
                            continue;
                        }

                        if ($menu_term_slug === 'missing-section') {
                            continue;
                        }

                        ?>
                        <li data-menuanchor="<?php echo esc_attr($menu_term_slug); ?>"
                            class="<?php echo esc_attr($menu_term_slug . ' ' . $menu_active); ?>">
                            <a href="#<?php echo esc_attr($menu_term_slug); ?>">
                                <?php echo esc_html($menu_term_name); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </nav>

        <div class="wp2-wizard" id="WP2DemoWizard">

            <?php foreach ($slides_by_section as $index => $section) : ?>
                <?php
                $section_term_name  = $section['term']['name'];
                $section_term_desc  = $section['term']['description'];
                $section_term_slug  = $section['term']['slug'];
                $section_active     = ($index === 0) ? 'active' : '';
                $section_content   = $section['term']['content'];
                $section_theme     = $section['term']['theme'] ?? 'default';

                $section_badges = [];
                foreach ($section['slides'] as $slide) {
                    $slide_title = $slide['title'];
                    $slide_slug  = $slide['slug'];
                    $section_badges[] = [
                        'title' => $slide_title,
                        'anchor' => '#' . $section_term_slug . '/' . $slide_slug,
                    ];
                }

                $classes = [
                    'wp2-wizard-section',
                    'wp2-wizard-section--' . $section_term_slug,
                    'wp2-wizard-section--' . $section_theme,
                    'fp-auto-height',
                    $section_active,
                ];

                $classes = implode(' ', $classes);

                if (empty($section_term_name)) {
                    continue;
                }
                ?>

                <div data-anchor="<?php echo esc_attr($section_term_slug); ?>" class="<?php echo esc_attr($classes); ?>">

                    <?php if (! empty($section_term_name) || ! empty($section_term_desc)) : ?>
                        <div class="wp2-wizard-section__header">

                            <div class="wp2-wizard-section__title">
                                <?php echo esc_html($section_term_name); ?>
                            </div>

                            <?php if (! empty($section_term_desc)) : ?>

                                <div class="wp2-wizard-section__description">
                                    <?php echo esc_html($section_term_desc); ?>
                                </div>
                            <?php endif; ?>

                            <?php if (! empty($section_content)) : ?>

                                <div class="wp2-wizard-section__content">
                                    <?php echo wp_kses_post($section_content); ?>
                                </div>
                            <?php endif; ?>

                        </div>

                    <?php endif; ?>

                    <div class="wp2-wizard-card-group">

                        <?php if (isset($section['slides']) && is_array($section['slides'])) : ?>

                            <?php foreach ($section['slides'] as $slide_index => $slide) : ?>

                                <?php
                                $slide_title   = $slide['title'] ?? '';
                                $slide_slug    = $slide['slug'] ?? '';
                                $slide_active  = ($slide_index === 0) ? 'active' : '';
                                $slide_id      = $slide['id'];

                                $slide_classes = [
                                    'wp2-wizard-card',
                                    $slide_slug,
                                    'fp-auto-height',
                                    $slide_active,
                                ];

                                $slide_classes = implode(' ', $slide_classes);

                                ?>

                                <div class="<?php echo esc_attr($slide_classes); ?>" data-tooltip="<?php echo esc_attr($slide_title); ?>" data-anchor="<?php echo esc_attr($slide_slug); ?>">
                                    <div class="wp2-wizard-card-content">
                                        <?php
                                        $slide_args = [
                                            'id' => 'wp2-demo/resource',
                                            'align' => 'full',
                                            'lock' => [
                                                'insert' => true,
                                                'move' => true,
                                            ],
                                            'data' => [
                                                'resource' => [
                                                    'value' => $slide_id,
                                                    'label' => $slide_title,
                                                    'innerBlocks' => false,
                                                ],
                                                'option' => [
                                                    'value' => 0,
                                                    'label' => 'Title + Subtitle',
                                                ],
                                            ],
                                        ];
                                        $slide_args = array_filter($slide_args);
                                        bs_render_block($slide_args);
                                        ?>
                                    </div>
                                </div>

                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>


                </div>

            <?php endforeach; ?>

        </div>

    </div>

</div>