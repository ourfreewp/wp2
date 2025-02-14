#!/bin/bash
# Run this from the catalog directory containing the listing directories

# Function: Convert PascalCase to Title (insert spaces)
pascal_to_title() {
	# Insert a space before every uppercase letter that follows a lowercase letter.
	echo "$1" | sed -E 's/([a-z])([A-Z])/\1 \2/g'
}

# Function: Convert Title to slug (lowercase with hyphens)
title_to_slug() {
	echo "$1" | tr '[:upper:]' '[:lower:]' | sed 's/ /-/g'
}

for dir in */; do
	# Skip if not a directory
	[ -d "$dir" ] || continue

	# Remove trailing slash from directory name
	dir_name="${dir%/}"

	# Transform the directory name from PascalCase to Title (with spaces)
	title=$(pascal_to_title "$dir_name")
	# Create a slug for the JSON name (lowercase with hyphens)
	slug=$(title_to_slug "$title")
	# Parent directory path
	parent_dir=$(basename "$PWD") # Kind
	kind=$(title_to_slug "${parent_dir}")

# Convert plural kind to singular using a case statement
case "$kind" in
  extensions)
    kind="extension"
    ;;
  integrations)
    kind="integration"
    ;;
  markers)
    kind="marker"
    ;;
  plugins)
    kind="plugin"
    ;;
  themes)
    kind="theme"
    ;;
  modules)
    kind="module"
    ;;
  pages)
    kind="page"
    ;;
  *)
    # If no match is found, keep the original or handle it as needed
    ;;
esac

	# block name is the directory name prefixed with "wp2-"
	block_name="wp2-${kind}/${slug}"

	# block class is the directory name prefixed with "wp2-"
	block_class="wp2-${kind}"

	# Define file paths
	index_path="${dir}index.php"
	block_json_path="${dir}block.json"
	readme_path="${dir}README.md"

	# Overwrite index.php using the fixed path comment and the directory name for the namespace
	cat >"$index_path" <<EOF
<?php
// Path: wp-content/plugins/wp2-directory/src/Catalogs/${parent_dir}/${dir_name}/index.php

namespace WP2_Directory\Catalogs\\${parent_dir}\\${dir_name};

\$inner_blocks = sprintf(
	'<InnerBlocks useBlockProps tag="%s" class="%s"/>',
	'div',
	'${block_class}'
);

echo \$inner_blocks;
EOF

	# Overwrite block.json with the transformed title and slug appended to the name value
	cat >"$block_json_path" <<EOF
{
	"\$schema": "https://app.blockstudio.dev/schema",
	"apiVersion": 2,
	"name": "${block_name}",
	"title": "${title}",
	"ancestor": ["wp2-directory/catalog"],
	"blockstudio": true
}
EOF

	# Overwrite README.md using the Title (PascalCase transformed with spaces)
	cat >"$readme_path" <<EOF
# ${title}
EOF

	echo "Rewrote files for directory: ${dir_name}"
done
