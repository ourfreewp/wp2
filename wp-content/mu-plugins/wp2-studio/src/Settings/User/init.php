<?php

namespace WP2_Daemon\WP2_Studio\Settings\User;

class Controller
{

    public function __construct()
    {
        add_filter('blockstudio/settings/users/ids', [$this, 'filter_user_ids']);
    }

    public function filter_user_ids($user_ids)
    {
        return array_merge($user_ids, defined('WP2_BLOCKSTUDIO_USERS') ? WP2_BLOCKSTUDIO_USERS : []);
    }
}
