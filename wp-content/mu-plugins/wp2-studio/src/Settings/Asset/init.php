<?php

namespace WP2_Daemon\WP2_Studio\Settings\Asset;

class Controller
{
    public static function init()
    {
        add_filter('blockstudio/settings/assets/enqueue', function () {
            return true;
        });
        add_filter('blockstudio/settings/assets/minify/css', function () {
            return false;
        });
        add_filter('blockstudio/settings/assets/minify/js', function () {
            return false;
        });
        add_filter('blockstudio/settings/assets/process/scss', function () {
            return false;
        });
        add_filter('blockstudio/settings/assets/process/scssFiles', function () {
            return true;
        });
    }
}
