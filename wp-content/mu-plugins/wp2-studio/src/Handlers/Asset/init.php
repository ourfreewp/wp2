<?php

namespace WP2_Daemon\WP2_Studio\Handlers\Asset;

class Controller
{
    public static function init()
    {
        add_filter('blockstudio/assets/enable', function ($value, $data) {
            if (isset($data['type']) && $data['type'] === 'css') {
                return false;
            }
            return $value;
        }, 10, 2);
        add_filter('blockstudio/assets/process/scss/importPaths', function () {
            $paths = [];
            $paths[] = get_template_directory() . '/src/scss/';
            return $paths;
        });
        add_filter('blockstudio/assets/process/css/content', function ($content) {
            return str_replace('background-color: red;', 'background-color: blue;', $content);
        });
        add_filter('blockstudio/assets/process/js/content', function ($content) {
            return str_replace('console.log("hi");', 'console.log("hello");', $content);
        });
    }
}
