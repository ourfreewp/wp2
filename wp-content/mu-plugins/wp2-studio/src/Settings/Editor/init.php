<?php

namespace WP2_Daemon\WP2_Studio\Settings\Editor;

class Controller
{
    public static function init()
    {
        add_filter('blockstudio/settings/editor/formatOnSave', function () {
            return false;
        });
        add_filter('blockstudio/settings/editor/assets', function () {
            return ['my-stylesheet', 'another-stylesheet'];
        });
        add_filter('blockstudio/settings/editor/markup', function () {
            return '<style>body { background: black; }</style>';
        });
    }
}
