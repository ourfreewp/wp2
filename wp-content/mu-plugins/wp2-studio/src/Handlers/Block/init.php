<?php

namespace WP2_Daemon\WP2_Studio\Handlers\Block;

class Controller
{
    public static function init()
    {
        // Register block output adjustments.
        add_filter('blockstudio/blocks/render', function ($value, $block, $isEditor, $isPreview) {
            if ($block->name === 'blockstudio/my-block') {
                $value = str_replace('%CONTENT%', 'Replace in frontend', $value);
                if ($isEditor) {
                    $value = str_replace('%CONTENT%', 'Only replace in editor', $value);
                }
                if ($isPreview) {
                    $value = str_replace('%CONTENT%', 'Only replace in preview', $value);
                }
            }
            return $value;
        }, 10, 4);

        // Register additional block filters (meta, conditions, attributes, etc.) here.
        add_filter('blockstudio/blocks/meta', function ($block) {
            if (strpos($block->name, 'marketing') === 0) {
                $block->blockstudio['icon'] = '<svg></svg>';
            }
            return $block;
        });
    }
}
