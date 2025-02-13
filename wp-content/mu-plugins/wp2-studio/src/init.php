<?php
// Path: wp-content/mu-plugins/wp2-studio/src/init.php

namespace WP2_Daemon\WP2_Studio;

use WP2_Daemon\WP2_Studio\Settings\User\Controller as UserSettingsController;

class Controller
{
    public function __construct()
    {
        $this->init_settings();
    }

    public function init_extensions()
    {
        // Initialize extensions here.
    }

    public function init_handlers()
    {
        // Initialize handlers here.
    }

    public function init_settings()
    {
        new UserSettingsController();
    }

    public function init_types()
    {
        // Initialize types here.
    }
}
