<?php
// Path: wp-content/plugins/wp2-blueprint/src/Wiki/Relations/init-sections.php

namespace WP2\Blueprint\Wiki\Relations;


class SectionController
{

    /**
     * Textdomain
     * 
     * @var string
     */
    private $text_domain = 'wp2-blueprint';

    /**
     * Type Prefix
     *
     * @var string
     */
    private $prefix = 'wp2_blueprint';

    /**
     * Taxonomy
     *
     * @var string
     */
    private $type = 'section';

    /**
     * Types
     * 
     * @var array
     */
    private $types = [
        'thing',
    ];


    /**
     * Constructor
     */

    public function __construct()
    {
        add_action('init', [$this, 'register_relations'], 11);
    }

    public function register_relations()
    {

        $prefix = $this->prefix;
        $taxonomy = $this->prefix . '_' . $this->type;

        $types = array_map(function ($type) {
            return $this->prefix . '_' . $type;
        }, $this->types);

        register_taxonomy_for_object_type($taxonomy, $types);
    }
}


new SectionController();
