# WP2

## Overview

The WP2 Template is a WordPress starter template that provides a modern, block-based, and component-driven architecture for building WordPress experiences using Full Site Editing (FSE) and the Block Editor.

## Structure

The WP2 Template is structured as a WordPress project with a custom theme and plugins. The project includes a core theme (`wp2`), a core plugin (`wp2`), and a template bootstrap plugin (`wp2-bootstrap`). The core theme and plugin provide the main application code, while the template bootstrap plugin initializes the template and provides CLI commands for managing the template.

### WP Content

- **wp-content/**: Contains the main application code, including themes, plugins, and mu-plugins.
  - **mu-plugins/**: Must-use plugins, including the Template Bootstrap Daemon (`wp2-bootstrap`).
  - **themes/**: Contains themes, notably the core theme (`wp2`).
  - **plugins/**: Contains plugins, notably the core plugin (`wp2`) and the template bootstrap plugin (`wp2-bootstrap`).
    - **wp2/**: Contains the core plugin code.
    - **wp2-bootstrap/**: Contains the template bootstrap plugin code.

### Application Code

- **/plugins/wp2*/src**: Contains the main application code including assets, blocks, filters, helpers, and templates.

#### Detailed Structure

- **Core Components**
  - `wp-content/plugins/wp2/` – Core Plugin.
  - `wp-content/themes/wp2/` – Core Theme.
  - `wp-content/mu-plugins/wp2-bootstrap` – Template Bootstrap Daemon.
  - `wp-content/plugins/wp2-bootstrap` – Template Bootstrap Plugin.

- **Libraries**
  - Bootstrap - Introduces the grid system and responsive utilities.
  - Floating UI [ES Module]

- **Assets**
  - **Scripts & Styles**
    - `wp-content/plugins/wp2/src/Assets/Styles`
    - SCSS directories for admin, block editor, and global styles.
  - **Styles - Blocks**
    - SCSS for various blocks:
      - `core-social-links.scss`
      - `core-template-part.scss`
      - `site-footer.scss`
      - `site-header.scss`
  - **Styles - Elements & Zones**
    - **Elements:** e.g., `body.scss`
    - **Zones:** e.g., `root.scss`

- **Block Extensions**
  - Located in `wp-content/plugins/wp2/src/BlockExtensions/`
  - Includes configuration (`extension.json`) and initialization scripts for extending existing blocks.

- **Blocks**
  - **Site Blocks:** `SiteRoot`, `SiteContent`, `SiteFooter`, `SiteHeader`, etc.
  - **Main Blocks:** `MainContent`, `MainFooter`, `MainHeader`.
  - **Content Blocks:** `ContentPrimary`, `ContentSecondary`.
  - **Article Blocks:** `ArticleContent`, `ArticleFooter`, `ArticleHeader`.
  - **Query Blocks:** `QueryContent`, `QueryDescription`, `QueryFooter`, `QueryHeader`, `QueryMeta`, `QueryName`.
  - **Broadcast Blocks:** `BroadcastContent`, `BroadcastFooter`, `BroadcastHeader`.
  - **Navbar Blocks:** `NavbarPrimary`, `NavbarSecondary`.
  - **Queried Blocks:** e.g., `QueriedItemByline`, `QueriedItemCover`, `QueriedItemDateline`, `QueriedItemMedia`, `QueriedItemMeta`, `QueriedItemPhoto`, `QueriedItemShare`, `QueriedItemSubtitle`, `QueriedItemTerm`, `QueriedItemTitle`.
  - **Misc Blocks:** `StretchedLink`

- **Filters**
  - **Manage Blocks:**
    - Block Editor, Block Patterns, Allowed Block Types.
  - **Body Class Management**
  - **Template Filters:**
    - Templates, Template Areas, Template Blocks (Patterns), Template Parts, Template Types.

- **Helpers**
  - Asset Helper (`init-asset.php`)
  - Context Helper (`init-context.php`)
  - Query Helper (`init-query.php`)

- **Templates**
  - Template Controllers:
    - 404 Template (`wp-content/plugins/wp2/src/Templates/404`)
    - Archive Template (`wp-content/plugins/wp2/src/Templates/Archive`)
    - Front Page Template (`wp-content/plugins/wp2/src/Templates/FrontPage`)

- **Bootstrap**

The template bootstrap plugin (`wp2-bootstrap`) manages the cloning experience when generating a new site from a solution like InstaWP. The plugin is designed to be uninstalled after the `mu-plugins/wp2-bootstrap.php` commands are triggered from the post-creation job ran during the cloning process. The plugin includes the following components:

  - **Daemon:** `wp-content/mu-plugins/wp2-bootstrap.php` (includes CLI commands).
  - **Plugin:** Located in `wp-content/plugins/wp2-bootstrap`
    - Activation (`activate.php`)
    - Deactivation (`deactivate.php`)
    - Uninstall routines (`uninstall.php`)

The full lifecycle of the boostrapping process is triggered by `wp wp2-bootstrap run` and will `activate`, `deactivate`, and `uninstall` the plugin. During each step, routines are performed to prepare the site for delivery before the plugin is removed.

A webhook address can be defined in `wp-config.php` so during the excution of the any cli command, an event will be sent to the webhook address. This can be used to trigger additional actions or workflows.

- **Theme**

The core theme (`wp2`) is primarily responsible for delivering the assets and templates that make up the site's front-end experience. The theme includes the following directories:

  - **Directory:** `wp-content/themes/wp2` 
  - **Assets:** Images and branding assets (e.g., SVG logos).
  - **Components:** Programmatically generated parts and templates, including `theme.json`.
  - **Miscellaneous:** Functions and styling files (`functions.php`, `style.css`, etc.).
