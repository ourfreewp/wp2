<?php
// Path: wp-content/plugins/wp2-wiki/src/Wiki/Blocks/README/init-meta.php

namespace WP2_Wiki\Blocks\Readme;



class MetaController
{

    private $post_type = 'wp2_wiki_readme';

    public function __construct()
    {
        add_action('init', [$this, 'init']);
    }

    public function init()
    {
        $this->register_readme_path_meta();
        $this->register_readme_html_meta();
        $this->register_readme_toc_meta();
        $this->register_readme_raw_meta();
    }

    private function register_readme_path_meta()
    {
        register_post_meta($this->post_type, '_wp2_wiki_readme_path', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
        ]);
    }

    private function register_readme_html_meta()
    {
        register_post_meta($this->post_type, '_wp2_wiki_readme_html', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
        ]);
    }

    private function register_readme_toc_meta()
    {
        register_post_meta($this->post_type, '_wp2_wiki_readme_toc', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
        ]);
    }

    private function register_readme_raw_meta()
    {
        register_post_meta($this->post_type, '_wp2_wiki_readme_raw', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
        ]);
    }
}


new MetaController();
