# Must-Use Plugins

This directory contains all must-use (MU) plugins for this WordPress installation. MU plugins load automatically and cannot be deactivated from the admin dashboard.

## WP2 Daemon

This file is the main entry point for the WP2 Daemon plugin. It sets up the core environment and autoloads additional WP2 modules from the must-use plugins directory.

### Overview

The bootstrap file performs the following key tasks:

#### Security Check

It ensures that the file is only executed within a WordPress context by checking for the existence of ABSPATH.

#### Constant Definitions

It defines several important constants used throughout WP2 modules:

- WP2_MU_PLUGIN_DIR: Path to the Must-Use plugins directory.
- WP2_CORE_DIR: Path to the core WP2 plugin directory.
- WP2_NEW_DIR: Path to the WP2 New plugin directory.
- WP2_WIKI_DIR: Path to the WP2 Wiki plugin directory.
- WP2_WORK_DIR: Path to the WP2 Work plugin directory.

#### Initialization via the WP2_Daemon Class

The WP2_Daemon class:

- Defines additional constants like WP2_NAMESPACE, WP2_PREFIX, and WP2_TEXTDOMAIN.
- Adds a filter (blockstudio/settings/users/ids) to merge custom user IDs.
- Registers an autoloader for all classes in the `WP2_Daemon\WP2_` namespace.
- Loads module initializer files from each WP2 module’s src directory.

#### Autoloader

The autoloader uses spl_autoload_register to automatically include class files:

- It scans each `wp2-*` directory under the must-use plugins folder.
- It builds the file path from the class namespace by replacing namespace separators (\) and underscores (_) with the directory separator.
- If a file is found, it’s required; otherwise, an error is logged in debug mode.

#### Module Initializers

The bootstrap file loads all init*.php files from the src directories of WP2 modules. This ensures that every module’s initializer runs, setting up their individual functionalities.

## Usage

### Installation

Place this bootstrap file in your MU plugins folder (e.g., wp-content/mu-plugins/).

### Module Structure

Ensure that your WP2 modules (such as wp2-new, wp2-wiki, etc.) follow the expected structure. For example:

- Each module should reside in a folder beginning with wp2- in the MU plugins directory.
- Module code lives under a src directory.
- Initializer files follow the naming convention init*.php so they can be automatically loaded.

### Extensibility

As your site grows, you can add new WP2 modules without modifying the bootstrap file—just drop a new module in the appropriate location, and its initializer will be loaded automatically.

## Summary

This bootstrap file is designed to provide a modular and maintainable architecture for WP2 Daemon modules. It handles constant definitions, autoloading, and module initialization, enabling you to extend your plugin system with minimal friction.