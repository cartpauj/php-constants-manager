=== PHP Constants Manager ===
Contributors: cartpauj
Tags: constants, php, configuration, admin, defines
Requires at least: 5.0
Tested up to: 6.8
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Safely manage PHP constants (defines) through the WordPress admin interface with full CRUD functionality.

== Description ==

PHP Constants Manager provides a secure and user-friendly interface for managing PHP constants in WordPress. No more editing wp-config.php or theme files to add or modify constants!

= Features =

* **Easy Management**: Create, read, update, and delete PHP constants from the WordPress admin
* **WP_List_Table Integration**: Native WordPress interface with sorting, searching, and bulk actions
* **Type Support**: String, Integer, Float, Boolean, and NULL constant types
* **Active/Inactive States**: Toggle constants on/off without deleting them
* **Conflict Detection**: See when constants are already defined elsewhere
* **Secure**: Only administrators can manage constants
* **Database Storage**: Constants stored safely in a custom database table
* **Early Loading**: Constants loaded at the beginning of WordPress initialization

= Use Cases =

* Manage environment-specific constants
* Toggle debug constants without editing files
* Store API keys and configuration values
* Create fallback constants for different environments
* Document what each constant does with descriptions

== Installation ==

1. Upload the `php-constants-manager` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to 'PHP Constants' in the WordPress admin menu
4. Start managing your constants!

== Frequently Asked Questions ==

= Is it safe to manage constants this way? =

Yes! The plugin includes multiple security measures:
- Only administrators can access the interface
- All inputs are sanitized and validated
- Nonce verification on all actions
- Constants are checked before defining to prevent conflicts

= What happens if a constant is already defined? =

The plugin checks if constants are already defined before attempting to define them. If a constant exists, it will show as "Predefined" in the list, and the plugin won't override it.

= Can I deactivate constants without deleting them? =

Yes! Each constant has an active/inactive toggle. Inactive constants remain in the database but aren't loaded.

= When are the constants loaded? =

Constants are loaded very early in the WordPress initialization process (plugins_loaded hook with priority 1) to ensure they're available to themes and other plugins.

= Can I use this for WordPress configuration constants? =

While you can add any constant, WordPress configuration constants (like DB_NAME, WP_DEBUG) are usually already defined in wp-config.php. The plugin will show these as "Predefined" and won't override them.

== Screenshots ==

1. Main constants list table with sorting and bulk actions
2. Add new constant form with type selection
3. Edit existing constant interface
4. Constant status indicators (Active, Inactive, Predefined)

== Changelog ==

= 1.0.0 =
* Initial release
* Full CRUD functionality
* WP_List_Table integration
* Type support for all PHP constant types
* Active/inactive toggle feature
* Conflict detection for existing constants

== Developer Information ==

= Database Table =

The plugin creates a custom table with the following structure:
* `id` - Primary key
* `name` - Constant name (unique)
* `value` - Constant value
* `type` - Data type (string, integer, float, boolean, null)
* `is_active` - Whether the constant is loaded
* `description` - Optional description
* `created_at` - Creation timestamp
* `updated_at` - Last update timestamp

= Hooks =

The plugin follows WordPress coding standards and uses standard hooks:
* `plugins_loaded` - For loading constants (priority 1)
* `admin_menu` - For adding menu items
* `admin_post_*` - For handling form submissions

= Security =

* Capability checks: `manage_options`
* Nonce verification on all actions
* Prepared statements for database queries
* Input sanitization with WordPress functions