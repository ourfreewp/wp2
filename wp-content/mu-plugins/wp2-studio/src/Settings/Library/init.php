<?php

namespace WP2_Daemon\WP2_Studio\Settings\Library;

class Controller
{
    public static function init()
    {
        add_filter('blockstudio/settings/library', function () {
            return false;
        });
    }
}
