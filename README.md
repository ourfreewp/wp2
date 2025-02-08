# WP2 Full-Site Editing Framework

## Overview

The WP2 Template is a WordPress starter framework optimized for modern, block-based, and component-driven development. It extends Full Site Editing (FSE) and the Block Editor, ensuring a controlled, scalable, and version-controlled environment that empowers users to make approved, structured modifications while maintaining design integrity.

This framework is designed to eliminate unnecessary complexity, reduce bloat, and enforce maintainability, making it ideal for enterprise-ready WordPress development.

## Key Features & Differentiators

### Structured Full-Site Editing with Controlled Theming

Programmatically Generated Themes: Themes and templates are generated via JSON-based configurations, ensuring precise control over modifications and structure.

#### Strictly Controlled Full Site Editing:
  - Template parts are isolated to prevent accidental modifications.
  - Users drill down into template parts without switching context.
  - Each template is self-contained, ensuring structural integrity.

#### Automated Template Bootstrap Process:
  - The wp2-blueprint plugin automates CLI-based site generation, activation, and workflow management.
  - Webhook integrations allow automated theme and template provisioning.

## Role-Based Block & Template Access for Consistency & Maintainability

### Filters ensure only relevant blocks and templates are available based on user roles:
- Administrators: Full access to all blocks and templates.
- Editors: Limited to content blocks, restricted from structural modifications.
- Authors & Contributors: Can edit post content only, ensuring layout consistency.
- Developers & Designers: Can modify templates in a controlled versioned environment.
### Context-Aware Block Visibility:
- Blocks are dynamically filtered based on the editing context (e.g., only content blocks for posts, only navigation blocks for headers).
- Unnecessary WordPress core blocks are disabled to eliminate distractions and bloat.
- Third-party block libraries that are not required are hidden to maintain efficiency and consistency.

## Dynamic Block System for Enhanced Scalability

### Custom Blocks & Extended Core Blocks

#### Wrapped Core Blocks:
- core/search, core/navigation, core/query, core/post-content are wrapped to allow customization without modifying core behavior.
#### Context-Aware Queried Item Blocks:
- Replace redundant core blocks by dynamically adjusting their behavior based on context.
#### Multi-Block Consolidation:
- Byline Block replaces core/author-name, core/author-avatar.
- Dateline Block replaces core/date, core/time.
#### Utility Blocks:
- Stretched Links, Broadcasts, and Focus Blocks provide additional layout and functionality options.

## Hierarchical Template & Template Part Management

### Hierarchical Template Handling

#### Every Template Starts with a Site Root Block
- Ensures a predictable, structured base for every template.
- Includes a Dynamic Template Selector to switch predefined layouts.
#### Template Parts are Generated Automatically
- Classified by Area, Zone, Type, and HTML Tag for consistent file naming and hierarchy.
- Prevents misconfigurations and redundant components.
- Templates dynamically adapt based on the predefined JSON configurations.
### Template Controllers & Bootstrap Process
Custom controllers define logic and styles for each template, ensuring context-specific behavior.

#### Automated Cloning & Site Generation
- wp2-blueprint plugin provides command-line control for site generation.
- Webhooks trigger workflow automation during setup.

## Minimalistic Styling with Modern Tooling
### Bootstrap for Styling:
- Provides a structured, responsive design foundation.
### SCSS-based Styling:
- Ensures modular and maintainable styles, reducing CSS redundancy.
### Intrinsic Web Design Principles:
- Prioritizes performance by using minimal assets, ensuring efficient rendering.

## Filters & Helpers for Block & Editor Customization

### Editor Distraction-Free Mode:
- Removes unnecessary elements and third-party patterns.
### Dynamic InnerBlocks Templates:
- Select and Radio fields allow programmatic template modifications.
### Custom Block Extensions via extension.json:
- Defines additional block behaviors & styling extensions centrally.
### Advanced Query Handling & Asset Retrieval:
- Custom filters control block editor behaviors, template parsing, and asset loading.

## Project Structure

### WP Content Hierarchy

```plaintext
wp-content/                             # Content Directory
├── mu-plugins/                         # Must-use plugins
│   └── wp2-blueprint/                  # Template Daemon
├── themes/                             # Themes Directory
│   └── wp2/                            # Core Theme
└── plugins/                            # Plugins Directory
    ├── wp2/                            # Core Plugin
    └── wp2-blueprint/                  # Template Plugin
├── wp2/                  # Core Plugin
└── wp2-blueprint/        # Template Bootstrap Plugin
```

### Application Code & Assets
```plaintext
wp-content/wp2/src/                     #
Assets/                                 #
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
│   ├── block-editor-scripts.js         #
BlockExtensions/                        #
Blocks/                                 #
Filters/                                #
Helpers/                                #
Templates/                              #
wp2.php                                 #
```


## How This Framework Improves WordPress FSE

- **Template Hierarchy:** Replace loose, manual templating with strict, programmatically generated templates.
- **Role-Based Editing:** All users see a tailored editing experience using dynamic, contextual block filtering.
- **Core Block Customization:** Wrapped core blocks for safe customizations.
- **Multi-Block Replacement:** Pre-built consolidated blocks replace multiple blocks.
- **Version Control & Safety:** Structured rollbacks and fully versioned templates & layouts
- **Automated Layouts:** JSON-driven automated setups.
- **Bloatware Control:** Limits available blocks and removes unnecessary blocks hidden for consistency
- **Enterprise Scalability:** Fully scalable with JSON-driven configuration

## Why WP2 FSE?

This framework doesn’t just extend Full-Site Editing—it redefines it for structured, maintainable, and scalable WordPress development.

- ✔ Eliminates unnecessary distractions & bloat
- ✔ Ensures strict structural consistency
- ✔ Allows only role-appropriate customizations
- ✔ Prevents clients from making accidental site-breaking edits
- ✔ Programmatically enforces best practices & hierarchy
- ✔ Makes WordPress FSE enterprise-ready with version control

With automated structure, role-based restrictions, and JSON-driven configurability, WP2 transforms Full Site Editing into a predictable, scalable, and professional-grade solution.

## Requirements

- **WordPress:**- The application framework
- **Blockstudio:**- The block framework
- **Bootstrap:**- The design framework

## Block Extensions
These extend and configure existing blocks using a centralized extension.json file and initialization scripts.

**Core:**

- **Template Part:**- The template part block is the primary block that wraps the template part.

## Blocks

**Root:**

The direct descendants of the `.wp-site-blocks` element where the entire site is contained when working with Full Site Editing.

- **Root:**- The root block is the primary block that wraps the entire
- **Header:**- The header block is the primary block that wraps the header.
- **Content:**- The content block is the primary block that wraps the content.
- **Footer:**- The footer block is the primary block that wraps the footer.

**Semantic:**

Dedicated blocks to enforce proper HTML semantics and SEO standards.

- **Header:**- The header block
- **Main:**- The main block
- **Footer:**- The footer block
- **Article:**- The article block
- **Aside:**- The aside block 
- **Nav:**- The nav block is

**Navigation:**
Starts with `Navbar` and are followed by a descriptive name (e.g., NavbarPrimary, NavbarSecondary).
- **Primary:**- The primary navigation block is
- **Secondary:**- The secondary navigation block is

**Focus:**
Each of these start with `Focus` and are followed by the specific block type.
- **Primary:**- Wraps the primary focus area.
- **Secondary:**- Wraps the secondary focus area.

**Article:**
Each of these start with `Article` and are followed by the specific block type.
- **Content:**- The article content block is 
- **Footer:**- The article footer block is 
- **Header:**- The article header block is 

**Query:**
Each of these start with `Query` and are followed by the specific block type.
- **Name:**- The query name block 
- **Description:**- The query description block 
- **Meta:**- The query meta block 
- **Header:**- The query header block
- **Content:**- The query content block 
- **Footer:**- The query footer block

**Site:**
. Each of these start with `Site` and are followed by the specific block type.

- **Alert:**- The site alert block
- **Brand:**- The site logo block
- **Item:**- The site item block
- **Menu:**- The site menu block
- **Placement:**- The site placement block
- **Search:**- The site search block

**Queried Item:**
Replace duplicative implementations with dynamic, context-specific content. Each of these start with `Item` and are followed by the specific block type.

- **Byline:**- The queried item byline block is the primary block that wraps the queried item byline.
- **Cover:**- The queried item cover block is the primary block that wraps the queried item cover.
- **Dateline:**- The queried item dateline block is the primary block that wraps the queried item dateline.
- **edia:**- The queried item media block is the primary block that wraps the queried item media.
- **Meta:**- The queried item meta block is the primary block that wraps the queried item meta.
- **Photo:**- The queried item photo block is the primary block that wraps the queried item photo.
- **Share:**- The queried item share block is the primary block that wraps the queried item share.
- **Subtitle:**- The queried item subtitle block is the primary block that wraps the queried item subtitle.
- **Term:**- The queried item term block is the primary block that wraps the queried item term.
- **Title:**- The queried item title block is the primary block that wraps the queried item title.

**Broadcasts:**
Broadcasts are a way to communicate important information to users. Each of these start with `Broadcast` and are followed by the specific block type. They are introduced as the `InnerBlocks` of a `SitePlacement` block.`

- **BroadcastHeader:**- The broadcast header block is the primary block that wraps the broadcast header.
- **BroadcastContent:**- The broadcast content block is the primary block that wraps the broadcast content.
- **BroadcastFooter:**- The broadcast footer block is the primary block that wraps the broadcast footer.

**Utility Blocks:**
- **StretchedLink:**- A block that stretches a link to fill its container.

## Filters
Manage block editor behaviors, block patterns, allowed block types, body class modifications, and template configurations.
- Sets a custom post lock timeout window
- Restrict code editor to specific roles
- Disable Openverse media library
- Disable the block directory
- Removes support for core, third-party, and remote block patterns
- Filters the allowed block type by role, context, etc.
- Extend body class with custom classes, including context
- Constructs the templates, parts, areas ,blocks, zones, and types

## Helpers 
Provide utility functionality used throughout the application.

- **Asset:**- Retrieves and processes assets from the theme directory.
- **Context:**- Parses contextual arguments and returns a string defining the current context.
- **Query:**- Constructs and executes queries based on provided arguments—including support for custom named queries.

## Templates
Template controllers. Each template can have its own controller, which is responsible for any template-specific logic, styles, and scripts.

Each directory name matches to the template hierarchy using pascal case (e.g., Archive, FrontPage, 404).

**404**
- Disables attempt to guess a redirect URL for a 404 request.

**Archive**
- Constructs the main archive query.

**Front Page**
- Constructs the front page query.

## Template Daemon & Plugin Lifecycle

The wp2-blueprint plugin streamlines the site generation process, ensuring a clean, programmatic creation of a new site instance:

**Daemon:**- Located in wp-content/mu-plugins/wp2-blueprint.php, it provides CLI commands to run the bootstrapping process.

**Plugin Lifecycle:**- Residing in wp-content/plugins/wp2-blueprint, the plugin handles:

- Activation
- Deactivation
- Uninstall routines

The full lifecycle is triggered by running a CLI commmand. After bootstrapping, the plugin is designed to be uninstalled, leaving behind a fully configured site.

**Webhook Integration:**- A webhook address can be set in wp-config.php so that during CLI command execution, events are sent to trigger additional actions or workflows.