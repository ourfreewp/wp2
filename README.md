# WP2
WP2 is a powerful WordPress framework, an extensive block library, and a suite of custom modules. Designed for both developers and content creators, WP2 makes it easier than ever to build engaging, high-performance websites.

## Requirements
Before you get started, make sure you have the following installed:
- WordPress — A robust CMS powering your site, supporting PHP X.0+ and WP 6.X
- Blockstudio — A tool for managing and creating custom blocks and more.

## Components
The essentials of WP2 are structured into several key components:

- WP2 New
- WP2 Core
- WP2 Theme
- WP2 Wiki

## Features

- Site Blueprint - Kickstart your project with a pre-defined site structure.
- Theme Framework - Build and customize themes with ease.
- Block Library -  Leverage a wide variety of blocks to design engaging content.
- Code Studios - Enhance your development workflow with integrated tools.


## Structure
### App
```
├── wp-config.php       # Wordpress configuration file
└── wp-content          # Wordpress content directory
    ├── mu-plugins      # Wordpress must-use plugins
    ├── plugins         # Wordpress plugins
    ├── themes          # Wordpress themes
    └── uploads         # Wordpress uploads
```

### Daemon
```
└── wp-content
    └── mu-plugins
        └── wp2-*
        └── wp2-*.php 
```

### Module
```
└── plugins
    ├── wp2-*
    │   ├── src
    │   │   ├── Assets
    │   │   │   ├── Scripts
    │   │   │   └── Styles
    │   │   │       └── scss
    │   │   │           ├── Blocks
    │   │   │           │   ├── core
    │   │   │           │   └── wp2-*
    │   │   │           ├── Elements
    │   │   │           └── Zones
    │   │   ├── Blocks
    │   │   │   ├── Namespaces
    │   │   │   │   ├── core
    │   │   │   │   └── wp2-*
    │   │   │   └── Settings
    │   │   ├── Elements
    │   │   ├── Helpers
    │   │   ├── Templates
    │   └── wp2-*.php
```

### Theme
```
└── themes
    └── wp2
        ├── assets
        │   ├── fonts
        │   └── images
        ├── parts
        │   └── {part}-part-{template}.html
        ├── patterns
        ├── templates
        │   ├── {template}.html
        ├── functions.php
        ├── readme.txt
        ├── rtl.css
        ├── style.css
        └── theme.json
```

## Modules
WP2 is built with modularity in mind. The available modules include:

- WP2
- WP2 New
- WP2 Wiki
- WP2 Work

## Blocks
A rich collection of blocks is provided to help you build content quickly and effectively.

### Root Blocks
- Root, Root Header, Root Content, Root Footer

### Nav Blocks
- Nav Primary, Nav Secondary

### Focus Blocks
- Primary Focus, Secondary Focus

### Site Blocks
- Site Alert, Site Brand, Site Item, Site Menu, Site Placement, Site Search, Site Root, Site Header, Site Content, Site Footer

### Main Blocks
- Main Header, Main Content, Main Footer

### Content Blocks
- Article Header, Article Content, Article Footer
- Query Header, Query Content, Query Footer

### Item Blocks
- Item Byline, Item Content, Item Cover, Item Dateline, Item Media, Item Meta, Item Photo, Item Share, Item Subtitle, Item Term, Item Title

### Broadcast Blocks
- Broadcast Header, Broadcast Content, Broadcast Footer

### Utility Blocks
- Stretched Link

## Plugins
WP2 leverages a variety of plugins that enhance functionality across different aspects of the site:

### Content Management
- Classic Editor - Replaces the block editor with the traditional editing interface.
- Simple Page Ordering - Enables drag‑and‑drop reordering of pages.
- Insert Special Characters - Makes it easier to add special characters within content.
- Yoast Duplicate Post - Facilitates quick duplication of posts or pages.
- Redirection - Manages URL redirections to improve SEO and user experience.

### Development
#### Themes
- Create Block Theme - Aids in building block-based themes.
- Theme Check - Verifies themes against WordPress standards.
- Block Visibility - Helps control the visibility of Gutenberg blocks.

#### Plugins
- Plugin Check - Scans and checks for plugin issues or conflicts.
- Blockstudio - Assists with creating or managing custom blocks.
- Meta Box - Framework for adding custom meta boxes and fields.

### Automation
- EasyCron - Manages scheduled tasks (cron jobs) externally or within WP.
- WP Crontrol - Offers a UI to view and manage WordPress cron events.

### Insights
- Fathom Analytics - Provides privacy‑focused site analytics.
- Microsoft Clarity - Offers session replays and heatmaps to understand user behavior.

### Compliance
- Iubenda - Helps ensure compliance with privacy and cookie laws.

### Accessibility
- Accessibility Checker - Assists in checking and improving web accessibility.

### Performance
- Perfmatters - Optimizes site performance by disabling unnecessary features.
- Imagify - Optimizes images for faster loading.

### Monitoring & Debugging
- Stream - Logs user activity for security/audit purposes.
- Health Check - Performs a diagnostic check of the site’s health.
- Query Monitor - Helps debug database queries, hooks, and more.
- InstaWP Connect - Facilitates connecting to staging or development environments.

### Media
#### Management
- Enable Media Replace - Allows you to replace media files without deleting and re‑uploading.
#### SVGs
- Safe SVG - Ensures SVG files are safely uploaded and sanitized.
#### Generation
- SleekPixel - creates pixel‑perfect images for various devices.
- RealFaviconGenerator - Generates favicons for various platforms.
#### Embedding
- Iframely - Simplifies embedding rich content from external sources.
#### Podcasting
- Easy Podcast Pro - Provides tools tailored for podcast publishing and management.

### Security
- Advanced Access Manager - Manages user permissions and access controls.
- Patchstack - Scans for vulnerabilities and enhances security.

### Authentication
- WP OAuth Server - Provides OAuth authentication capabilities.
- User Switching - Makes it easy to switch between user accounts in the dashboard.
- Simple Local Avatars - Allows use of local avatars instead of relying on Gravatar.
- Nextend Social Login - Enables login via social media accounts.

### Revenue
#### Commerce
- ShopWP - Integrates Shopify for e‑commerce functionality.
#### Advertising
- Ads.txt Manager - Helps manage the ads.txt file to ensure advertising transparency.
### Distribution

#### Search
- Slim SEO Schema - Adds schema markup for enhanced search engine results.
- The SEO Framework - Provides a lightweight SEO toolkit.
#### Social
- Novashare - Adds social sharing buttons to your content.

### Submissions
#### Forms
- WS Form - A flexible form builder for creating various forms.
#### Emails
- Postmark - Integrates with the Postmark service for transactional emails.

### Mobile
- PWA - Enables Progressive Web App features for a more app‑like experience.

### Admin
- Admin Columns - Improves the WordPress admin interface by managing table columns.

### Feedback
- Marker.io - Facilitates bug reporting and feedback directly from the site.

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
