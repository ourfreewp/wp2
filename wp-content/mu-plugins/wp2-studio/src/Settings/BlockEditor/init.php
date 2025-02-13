<?php

namespace WP2_Daemon\WP2_Studio\Settings\BlockEditor;

class Controller
{
    public static function init()
    {
        add_filter('blockstudio/settings/blockEditor/disableLoading', function () {
            return false;
        });
        add_filter('blockstudio/settings/blockEditor/cssClasses', function () {
            return ['my-stylesheet', 'another-stylesheet'];
        });
        add_filter('blockstudio/settings/blockEditor/cssVariables', function () {
            return ['my-stylesheet', 'another-stylesheet'];
        });
    }
}
