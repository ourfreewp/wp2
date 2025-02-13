# WP2 Studio

WP2 Studio extends [Blockstudio](https:///blockstudio.dev) by building on its core philosophy and workflow.

Blockstudio allows you can easily create custom WordPress blocks using just PHP and the block.json format.

Using PHP templates with JSX-like tags, Blockstudio transforms your work into interactive React components in the editor and clean HTML on the front end. Plus, it registers blocks through the file system, which means that all your assets and templates are neatly connected to each specific block.

Best of all, Blockstudio works effortlessly without any configuration required. ​Blockstudio is built upon fundamental WordPress features and components from Gutenberg, ensuring a smooth experience.

This repository is organized into several main areas:

- **Types** – Define block data structures, icons, instance definitions, and rules.
- **Settings** – Centralize configuration via filters (global, user, asset, editor, etc.).
- **Handlers** – Implement callback logic for blocks, settings, assets, and instances.
- **Extensions** – Integrate external frameworks (Bootstrap, Tailwind, Twig) to extend Blockstudio.
