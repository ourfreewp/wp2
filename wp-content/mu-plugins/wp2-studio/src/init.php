<?php
// Path: wp-content/mu-plugins/wp2-studio/src/init.php

namespace WP2_Daemon\WP2_Studio;

use WP2_Daemon\WP2_Studio\Settings\User\Controller as UserSettingsController;
use WP2_Daemon\WP2_Studio\Handlers\Instance\Controller as StudioInitializer;

class Controller
{
    public function __construct()
    {
        $this->init_settings();
        $this->init_handlers();
    }

    public function init_handlers()
    {
        new StudioInitializer();
    }

    public function init_settings()
    {
        new UserSettingsController();
    }
}
