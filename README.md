# Language Dropdown WordPress Plugin

The Language Dropdown plugin adds a customizable language selection menu to your WordPress site, allowing visitors to easily switch between different language versions of your content.

## Features

- Customizable language dropdown menu
- Shortcode for easy integration
- Bootstrap styling for modern appearance
- Admin settings page for easy configuration
- Support for custom flags and language codes
- Multisite compatible
- Drag-and-drop sorting of language options

## Installation

1. Upload the `language-dropdown` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the plugin settings under 'Settings' > 'Language Dropdown'

## Usage

Add the language dropdown menu to your site using the shortcode:

```php
[language_dropdown]
``` 
You can place this shortcode in your posts, pages, or in your theme files using the `do_shortcode()` function:

```php
<?php echo do_shortcode('[language_dropdown]'); ?>
```

## Directory Structure
```
language-dropdown/
├── admin-page.php
├── css/
│   ├── admin-style.css
│   └── style.css
├── js/
│   └── script.js
└── language-dropdown.php
```

# Configuration

1. Go to 'Settings' > 'Language Dropdown' in the WordPress admin panel.
2. Add your desired languages, including:
   - Language Name
   - Language Code
   - Flag Image (upload via media library)
   - Language URL
3. Drag and drop to reorder languages as needed.
4. Save your changes.

## Multisite Support

When used on a multisite installation, network admins can set network-wide language options that will be used as defaults for all sites.

## Styling

The plugin uses Bootstrap 4.5.2 for styling. You can further customize the appearance by modifying the `css/style.css` file.

## Requirements

- WordPress 5.2 or higher
- PHP 7.2 or higher

## License

This plugin is licensed under the GPL v2 or later.

## Support

For support, feature requests, or bug reports, please use the GitHub issues page for this repository.
