# WP2

## Overview

> [!CAUTION]
> This is a work in progress. The information provided is subject to change and the project is not yet ready for production use.

WP2 is a powerful WordPress framework, an extensive block library, and a suite of custom modules. Designed for both developers and content creators, WP2 makes it easier than ever to build engaging, high-performance websites.

### Requirements

- [InstaWP](https://app.instawp.io/register?ref=39TUWaLAzX) — Or any WordPress site supporting PHP X.0+ and WP 6.X
- [Blockstudio](https://www.blockstudio.dev) — A tool for managing and creating custom blocks and more.

## Components

- [WP2 New](./wp-content/plugins/wp2-new/README.md) incluedes daemon (must-use plugin) and module (standard plugin) that aids in the template cloning process.
- [WP2 Core](./wp-content/plugins/wp2/README.md) is the foundation of WP2, providing essential functionality and utilities.
- [WP2 Theme](./wp-content/themes/wp2/README.md) is block-based, primarily configuration-based theme tailored for full-site editing.

## Modules

- [WP2 Work](./wp-content/plugins/wp2-work/README.m) establishes set of tools and utilities for connecting your site to your workspaces and collaboration tools.
- [WP2 Wiki](./wp-content/plugins/wp2-wiki/README.m) provides the docs and resources for WP2, including information specific about your site.

## Blocks

A rich collection of blocks is provided to help you build content quickly and effectively.

### Structural Blocks

[Root](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/Root/README.md) | [Root Header](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/RootHeader/README.md) | [Root Content](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/RootContent/README.md) | [Root Footer](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/RootFooter/README.md)

### Site Blocks

 [Site Header](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/SiteHeader/README.md) | [Site Content](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/SiteContent/README.md) | [Site Footer](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/SiteFooter/README.md)

### Nav Blocks

[Primary Nav](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/NavPrimary/README.md) | [Secondary Nav](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/NavSecondary/README.md)

### Main Blocks

[Main Header](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/MainHeader/README.md) | [Main Content](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/MainContent/README.md) | [Main Footer](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/MainFooter/README.md)

### Focus Blocks

[Primary Focus](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/PrimaryFocus/README.md) | [Secondary Focus](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/SecondaryFocus/README.md)

### Article Blocks

[Article Header](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/ArticleHeader/README.md) | [Article Content](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/ArticleContent/README.md) | [Article Footer](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/ArticleFooter/README.md)

### Query Blocks

[Query Header](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/QueryHeader/README.md) | [Query Content](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/QueryContent/README.md) | [Query Footer](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/QueryFooter/README.md)

### Dynamic Blocks

#### Dynamic Site Blocks

[Site Alert](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/SiteAlert/README.md) | [Site Placement](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/SitePlacement/README.md) | [Site Item](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/SiteItem/README.md) | [Site Menu](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/SiteMenu/README.md) | [Site Search](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/SiteSearch/README.md) | [Site Brand](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/SiteBrand/README.md)

#### Dynamic Item Blocks

[Item Title](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/ItemTitle/README.md) [Item Subtitle](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/ItemSubtitle/README.md) | [Item Term](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/ItemTerm/README.md) | [Item Byline](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/ItemByline/README.md) | [Item Dateline](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/ItemDateline/README.md) | [Item Media](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/ItemMedia/README.md) | [Item Cover](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/ItemCover/README.md) | [Item Meta](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/ItemMeta/README.md) | [Item Photo](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/ItemPhoto/README.md) | [Item Content](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/ItemContent/README.md) | [Item Share](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/ItemShare/README.md)

### Broadcast Blocks

[Broadcast Header](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/BroadcastHeader/README.md) [Broadcast Content](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/BroadcastContent/README.md) [Broadcast Footer](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/BroadcastFooter/README.md)

### Utility Blocks

[Stretched Link](./wp-content/plugins/wp2/src/Blocks/Namespaces/wp2/StretchedLink/README.md)

## Plugins

- [Daemons](./wp-content/mu-plugins/README.md)
- [Standard](./wp-content/plugins/README.md)

## Next Steps

We’re continuously improving WP2 and welcome your input. Here’s how you can help:

### Join

Become a member of [WP2S](https://www.wp2s.com/join/).

### Sponsor

Support ongoing development by sponsoring WP2. Every contribution helps us build a better tool for everyone.

### Collaborate

Interested in contributing? Whether it’s bug reports, feature requests, or pull requests, your input is invaluable.

## Contact

Have questions or need support? Reach out:
Email: [wp2@wp2s.com](mailto:hello+wp2@wp2s.com)
