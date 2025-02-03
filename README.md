# WP2

## Overview

The WP2 Template is a WordPress starter framework built for modern, block-based, component-driven development. It leverages Full Site Editing (FSE) and the Block Editor to provide a controlled, programmatically generated environment. This approach ensures that while clients enjoy full site editing, they’re limited to making only approved, localized changes without the risk of deviating from the baseline design.

## Requirements

- **WordPress:** The application framework
- **Blockstudio:** The block framework
- **Bootstrap:** The design framework

## Key Concepts

### Programmatically Generated Themes
Themes, templates, and their parts are generated by code rather than manually built. Knowing the exact depth and location from the outset lets you precisely control the editing experience.

### Strictly Controlled Full Site Editing
Every template comes with its own set of template parts. Drill down without switching contexts, and ensure that every change is localized and contained.

- **Localized Changes:** Editing a template part (e.g., a header) only impacts that particular template.
- **Controlled Experiments:** You can test new patterns in a confined context. Even if clients are allowed to modify inner blocks (with locking disabled in production), the changes remain minimally invasive and isolated.

### Dynamic Block Templates

Use Select and Radio field types to set dynamically generated InnerBlocks templates depending on the field value. This is useful if you want to allow the user to select a different template for the InnerBlocks component.

### Custom Context-Aware Blocks

- **Wrapped Core Blocks:** Complex elements such as navigation or search are wrapped (e.g., as site-menu or site-search) so you extend their functionality without re-creating them.

- **Semantic Blocks:** Instead of relying on clients to assign correct HTML semantics (header, footer, main, aside, nav), dedicated custom blocks ensure all elements meet technical SEO standards.

- **Queried-Item Blocks:** Replace duplicative blocks (multiple title, excerpt, or description blocks) with context-aware blocks that dynamically provide the intended visual element in any loop or template position. Special blocks handle traditionally cumbersome items like authors, dates, bylines, and datelines with precision.

### Minimalistic Styling with Modern Tooling

Blend of traditional responsive development with intrinsic design for pixel-perfect outcomes. 

- All assets are handled natively via BlockStudio, with SCSS and ES module support and no external build tools required.
- The system imports only what’s needed—the Bootstrap grid, responsive utilities, and select mixins—without extra styles. 

###  Distraction-Free Editor Experience

The WP2 Template removes every unnecessary element from the site editor. 
- No third-party patterns or media libraries cluttering the interface
- A rules engine governs blocks by context, streamlining the editing process.

## Project Structure

The WP2 Template is organized as a WordPress project with a custom theme and plugins:

### WP Content Hierarchy

```plaintext
wp-content/
├── mu-plugins/
│   └── wp2-bootstrap/        # Must-use plugins (Template Bootstrap Daemon)
├── themes/
│   └── wp2/                  # Core Theme
└── plugins/
├── wp2/                  # Core Plugin
└── wp2-bootstrap/        # Template Bootstrap Plugin
```

### Application Code

All primary application code resides in wp-content/plugins/wp2/src, which includes:

#### Assets

The assets directory contains all styles and scripts that are shared across the application. Each asset is organized by type and purpose, with a clear separation of concerns. The build process is handled by BlockStudio, which compiles and enqueues assets as needed.

```plaintext
Assets/
├── Styles/                             #
│   ├── scss/                           # 
│   │   ├── Blocks/                     # 
│   │   ├── Elements/                   # 
│   │   ├── Zones/                      # 
│   │   ├── admin-styles.scss           #
│   │   ├── block-editor-styles.scss    #
│   │   ├── global-styles.scss          # 
│   Scripts/                            #
│   ├── global-scripts.js               #     
```

#### Block Extensions
These extend and configure existing blocks using a centralized extension.json file and initialization scripts.

**Core:**

- **Template Part:** The template part block is the primary block that wraps the template part.

#### Blocks

**Root:**

The direct descendants of the `.wp-site-blocks` element where the entire site is contained when working with Full Site Editing.

- **Root:** The root block is the primary block that wraps the entire
- **Header:** The header block is the primary block that wraps the header.
- **Content:** The content block is the primary block that wraps the content.
- **Footer:** The footer block is the primary block that wraps the footer.

**Semantic:**

Dedicated blocks to enforce proper HTML semantics and SEO standards.

- **Header:** The header block
- **Main:** The main block
- **Footer:** The footer block
- **Article:** The article block
- **Aside:** The aside block 
- **Nav:** The nav block is

**Navigation:**
Starts with `Navbar` and are followed by a descriptive name (e.g., NavbarPrimary, NavbarSecondary).
- **Primary:** The primary navigation block is
- **Secondary:** The secondary navigation block is

**Focus:**
Each of these start with `Focus` and are followed by the specific block type.
- **Primary:** Wraps the primary focus area.
- **Secondary:** Wraps the secondary focus area.

**Article:**
Each of these start with `Article` and are followed by the specific block type.
- **Content:** The article content block is 
- **Footer:** The article footer block is 
- **Header:** The article header block is 

**Query:**
Each of these start with `Query` and are followed by the specific block type.
- **Name:** The query name block 
- **Description:** The query description block 
- **Meta:** The query meta block 
- **Header:** The query header block
- **Content:** The query content block 
- **Footer:** The query footer block

**Site:**
. Each of these start with `Site` and are followed by the specific block type.

- **Alert:** The site alert block
- **Brand:** The site logo block
- **Item:** The site item block
- **Menu:** The site menu block
- **Placement:** The site placement block
- **Search:** The site search block

**Queried Item:**
Replace duplicative implementations with dynamic, context-specific content. Each of these start with `QueriedItem` and are followed by the specific block type.

- **Byline:** The queried item byline block is the primary block that wraps the queried item byline.
- **Cover:** The queried item cover block is the primary block that wraps the queried item cover.
- **Dateline:** The queried item dateline block is the primary block that wraps the queried item dateline.
- **edia:** The queried item media block is the primary block that wraps the queried item media.
- **Meta:** The queried item meta block is the primary block that wraps the queried item meta.
- **Photo:** The queried item photo block is the primary block that wraps the queried item photo.
- **Share:** The queried item share block is the primary block that wraps the queried item share.
- **Subtitle:** The queried item subtitle block is the primary block that wraps the queried item subtitle.
- **Term:** The queried item term block is the primary block that wraps the queried item term.
- **Title:** The queried item title block is the primary block that wraps the queried item title.

**Broadcasts:**
Broadcasts are a way to communicate important information to users. Each of these start with `Broadcast` and are followed by the specific block type. They are introduced as the `InnerBlocks` of a `SitePlacement` block.`

- **BroadcastHeader:** The broadcast header block is the primary block that wraps the broadcast header.
- **BroadcastContent:** The broadcast content block is the primary block that wraps the broadcast content.
- **BroadcastFooter:** The broadcast footer block is the primary block that wraps the broadcast footer.

**Utility Blocks:**
- **StretchedLink:** A block that stretches a link to fill its container.

#### Filters
Manage block editor behaviors, block patterns, allowed block types, body class modifications, and template configurations.
- Sets a custom post lock timeout window
- Restrict code editor to specific roles
- Disable Openverse media library
- Disable the block directory
- Removes support for core, third-party, and remote block patterns
- Filters the allowed block type by role, context, etc.
- Extend body class with custom classes, including context
- Constructs the templates, parts, areas ,blocks, zones, and types

#### Helpers 
Provide utility functionality used throughout the application.

- **Asset:** Retrieves and processes assets from the theme directory.
- **Context:** Parses contextual arguments and returns a string defining the current context.
- **Query:** Constructs and executes queries based on provided arguments—including support for custom named queries.

#### Templates
Template controllers. Each template can have its own controller, which is responsible for any template-specific logic, styles, and scripts.

Each directory name matches to the template hierarchy using pascal case (e.g., Archive, FrontPage, 404).

**404**
- Disables attempt to guess a redirect URL for a 404 request.

**Archive**
- Constructs the main archive query.

**Front Page**
- Constructs the front page query.

### Template Bootstrap Process

The wp2-bootstrap plugin streamlines the site generation process, ensuring a clean, programmatic creation of a new site instance:

**Daemon:** Located in wp-content/mu-plugins/wp2-bootstrap.php, it provides CLI commands to run the bootstrapping process.

**Plugin Lifecycle:** Residing in wp-content/plugins/wp2-bootstrap, the plugin handles:

- Activation
- Deactivation
- Uninstall routines

The full lifecycle is triggered by running a CLI commmand. After bootstrapping, the plugin is designed to be uninstalled, leaving behind a fully configured site.

**Webhook Integration:** A webhook address can be set in wp-config.php so that during CLI command execution, events are sent to trigger additional actions or workflows.

---

The WP2 Template delivers a robust framework that marries the flexibility of full site editing with strict, programmatically enforced controls. By generating themes and templates in a predictable and isolated manner, it allows for fine-tuned customizations that remain consistent, localized, and secure. Whether you’re tweaking a single template part or managing complex blocks, WP2 ensures that every change adheres to your design and development standards.
