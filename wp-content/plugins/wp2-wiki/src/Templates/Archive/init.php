<?php
// Path: wp-content/plugins/wp2-wiki/src/Templates/init-archive-readme.php

namespace WP2_Wiki\Templates\Archives;


class Controller
{

    /**
     * Textdomain
     * 
     * @var string
     */
    private $text_domain = 'wp2-wiki';

    /**
     * Prefix
     *
     * @var string
     */
    private $prefix = 'wp2_wiki_';


    /**
     * Initial Post Type
     *
     * @var array
     */
    private $post_type = 'readme';


    /**
     * Constructor
     */
    public function __construct()
    {
        add_action('init', [$this, 'set_default_query'], 12);
    }


    public function set_default_query()
    {
        add_action('pre_get_posts', function ($query) {
            $prefix    = $this->prefix;
            $post_type = $this->prefix . $this->post_type;
            //return if is admin
            if (is_admin()) {
                return;
            }
            if (is_post_type_archive($post_type) && $query->is_main_query()) {
                $query->set('post_name__in', ['wiki']);
                $query->set('posts_per_page', 1);
            }
        });
    }
}

new Controller();
