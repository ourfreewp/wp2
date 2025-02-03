Below are two parts: a quick reference for the WP‐CLI commands and a sample README file.

WP‐CLI Commands

You can use these commands in your terminal (assuming WP-CLI is installed and you’re in your WordPress root directory):

# Run the full plugin lifecycle (activate, then deactivate, then uninstall)
wp wp2-cli-bootstrap run

# Activate the plugin
wp wp2-cli-bootstrap activate

# Deactivate the plugin
wp wp2-cli-bootstrap deactivate

# Uninstall the plugin
wp wp2-cli-bootstrap uninstall

README.md

# WP2 CLI Bootstrap Controller

WP2 CLI Bootstrap Controller is a WordPress plugin that adds WP-CLI commands to manage the plugin's lifecycle. It lets you activate, deactivate, and uninstall the plugin via WP-CLI, with built-in retry logic and webhook notifications for lifecycle events.

## Features

- **Activation:** Activate the plugin via WP-CLI.
- **Deactivation:** Deactivate the plugin via WP-CLI.
- **Uninstallation:** Uninstall the plugin via WP-CLI.
- **Full Lifecycle Management:** Execute the complete plugin lifecycle (activation, deactivation, and uninstallation) with one command.
- **Webhook Notifications:** Send lifecycle event notifications to a configurable webhook URL.

## Requirements

- WordPress
- [WP-CLI](https://wp-cli.org/)

## Installation

1. Place the plugin files in your WordPress installation’s `wp-content/plugins` directory.
2. Activate the plugin through the WordPress admin dashboard or via WP-CLI:
   ```bash
   wp plugin activate wp2-bootstrap/wp2-bootstrap.php

	3.	(Optional) Define the webhook URL constant in your wp-config.php:

define('WP2_BOOTSTRAP_WEBHOOK_URL', 'https://your-webhook-url.com');



Usage

Once the plugin is installed and WP-CLI is available, you can use the following commands:

Run Full Lifecycle

Executes the complete lifecycle (activation, deactivation, uninstallation) in one go:

wp wp2-bootstrap run

Activate Plugin

Activates the plugin:

wp wp2-bootstrap activate

Deactivate Plugin

Deactivates the plugin:

wp wp2-bootstrap deactivate

Uninstall Plugin

Uninstalls the plugin (ensuring it’s deactivated first):

wp wp2-bootstrap uninstall

Troubleshooting
	•	WP-CLI Context: The commands only work when WP-CLI is available.
	•	Webhook Notifications: To receive notifications, ensure you’ve defined a valid WP2_BOOTSTRAP_WEBHOOK_URL.
	•	Retry Mechanisms: The plugin includes retry logic for activation and webhook notifications. Check your WP-CLI logs for details if a command fails.

License

This project is licensed under the MIT License.

Author

WP2S

---

Simply include the plugin file in your WordPress installation, and you’re ready to manage its lifecycle from the command line!