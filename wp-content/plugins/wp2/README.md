# WP2 Core



### Block

```bash
.
└── BlockName/
    ├── block.json
    ├── index.php
    ├── init.php
    ├── *.js
    └── *.(s)css
```


```bash
.
└── wp-content/
    └── themes/
        └── wp2*/
            ├── assets/
            │   ├── fonts
            │   └── images
            ├── parts/
            │   └── {part}-part-{template}.html
            ├── patterns
            ├── templates/
            │   └── {template}.html
            ├── functions.php
            ├── readme.txt
            ├── rtl.css
            ├── style.css
            └── theme.json
```


## Structure

### App

```bash
.
├── wp-content/
│   ├── mu-plugins
│   ├── plugins
│   ├── themes
│   └── uploads
├── wp-config.php
└── README.md
```

### Daemon

```bash
.
└── wp-content/
    └── mu-plugins/
        ├── wp2*/
        │   └── src
        └── wp2*.php
```

### Module

```bash
.
└── wp-content/
    └── plugins/
        └── wp2*/
            ├── src/
            │   ├── Assets/
            │   │   ├── Scripts
            │   │   └── Styles/
            │   │       ├── Blocks/
            │   │       │   ├── core
            │   │       │   └── wp2*
            │   │       ├── Elements
            │   │       └── Zones
            │   ├── Blocks/
            │   │   ├── Namespaces/
            │   │   │   ├── core
            │   │   │   └── wp2*
            │   │   └── Settings
            │   ├── Elements
            │   ├── Helpers
            │   ├── Templates
            │   └── Types
            └── wp2*.php
```
