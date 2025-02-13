<?php

namespace WP2_Daemon\WP2_Studio\Settings\Global;

class Controller
{
    public static function init()
    {
        // Adjust the path of the blockstudio.json file.
        add_filter('blockstudio/settings/path', function () {
            return get_stylesheet_directory() . '/settings';
        });
    }
}
