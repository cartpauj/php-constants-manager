=== PHP Constants Manager ===
Contributors: cartpauj
Tags: constants, php, configuration, admin, defines
Requires at least: 5.0
Tested up to: 6.8
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Safely manage PHP constants (defines) through the WordPress admin interface with full CRUD functionality and comprehensive viewing capabilities.

== Description ==

PHP Constants Manager provides a secure and user-friendly interface for managing PHP constants in WordPress. No more editing wp-config.php or theme files to add or modify constants!

= Key Features =

* **Complete Constant Management**: Create, read, update, and delete PHP constants from the WordPress admin
* **Dual View System**: "My Constants" for your custom constants and "All Constants" to view every constant in your WordPress installation
* **Native WordPress UI**: Built using WP_List_Table with sorting, searching, and bulk actions
* **Multiple Data Types**: Support for String, Integer, Float, Boolean, and NULL constant types
* **Active/Inactive States**: Toggle constants on/off without deleting them
* **Conflict Detection**: Visual indicators show when constants are already defined elsewhere (predefined)
* **Screen Options**: Customize table views with adjustable items per page and column visibility controls
* **Load Order Awareness**: Constants loaded during plugins_loaded action (priority 1) for broad compatibility
* **Comprehensive Help**: Built-in help system with detailed documentation and best practices
* **Administrator Only**: Secure access restricted to users with manage_options capability
* **Database Storage**: Constants stored safely in a custom database table with full audit trail
* **Import/Export**: Backup and migrate constants using CSV files with comprehensive format validation

= Understanding Predefined Constants =

The plugin intelligently detects when constants are already defined by WordPress core, other plugins, or your theme:
* **Not Predefined**: Your constant is unique and will work normally
* **Predefined**: The constant exists elsewhere - your definition is saved but won't override the existing value due to PHP's constant rules

= Use Cases =

* Manage environment-specific configuration
* Toggle debug constants without file editing
* Store API keys and configuration values securely
* Create fallback constants for different environments
* Document constant purposes with built-in descriptions
* Audit all constants in your WordPress installation
* Backup constants to CSV files for migration between sites
* Import constants in bulk from properly formatted CSV files

== Installation ==

1. Upload the `php-constants-manager` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to 'PHP Constants' in the WordPress admin menu
4. Start managing your constants!

The plugin will automatically create the necessary database table upon activation.

== Frequently Asked Questions ==

= Is it safe to manage constants this way? =

Yes! The plugin includes multiple security measures:
- Only administrators can access the interface
- All inputs are sanitized and validated
- Nonce verification on all actions
- Constants are checked before defining to prevent conflicts

= What's the difference between "My Constants" and "All Constants"? =

"My Constants" shows only the constants you've created through this plugin, with full management capabilities. "All Constants" displays every constant defined in your WordPress installation (core, plugins, themes) for auditing and reference purposes.

= What happens if a constant is already defined (shows as "Predefined")? =

The plugin checks if constants are already defined before attempting to define them. If a constant exists, it will show as "Predefined" in the list. Your definition is saved but won't take effect due to PHP's rule that constants cannot be redefined. This commonly happens with WordPress core constants or those defined in wp-config.php.

= Can I deactivate constants without deleting them? =

Yes! Each constant has an active/inactive toggle. Inactive constants remain in the database but aren't loaded into PHP.

= When are my constants available in my code? =

Constants are loaded during the `plugins_loaded` action with priority 1, making them available to:
- All theme code (themes load after plugins)
- Most other plugins (unless they use higher priority)
- WordPress hooks like `init`, `wp_loaded`, etc.

= Can I customize how the tables are displayed? =

Yes! Use the "Screen Options" button in the top-right corner to:
- Control how many constants are displayed per page (5, 10, 20, 50, 100+)
- Show/hide specific columns in both tables
- Your preferences are saved automatically

= How do I know if my constant is working? =

Check the "Predefined" column in "My Constants". If it shows "No" and the status is "Active", your constant is working. You can also test it in your code using `defined('MY_CONSTANT')`.

= Why can't I override WordPress core constants? =

This is a PHP limitation, not a plugin restriction. Constants cannot be redefined once set. Since WordPress core constants are defined very early (in wp-config.php or during WordPress bootstrap), they cannot be overridden by plugins.

= How do I import/export constants? =

Go to the "Import/Export" page in the plugin menu. You can export all your constants to a CSV file for backup or migration purposes. To import, upload a CSV file with the required format. The CSV must include at minimum: Name, Value, Type columns. Optional columns are Active and Description.

= What CSV format should I use for importing? =

Your CSV should have these columns:
- **Name** (required): Uppercase constant name, e.g., "MY_CONSTANT"
- **Value** (required): The constant value
- **Type** (required): One of: string, integer, float, boolean, null
- **Active** (optional): 1 for active, 0 for inactive (defaults to 1)
- **Description** (optional): Text description

Example CSV:
```
Name,Value,Type,Active,Description
MY_API_KEY,abc123,string,1,API key for service
MAX_POSTS,25,integer,1,Maximum posts per page
DEBUG_MODE,true,boolean,0,Enable debug output
```

= Will importing overwrite existing constants? =

No, the import process skips constants that already exist in your database. Only new constants are added. You'll receive a detailed report showing what was imported, skipped, and any errors.

== Screenshots ==

1. "My Constants" page showing custom constants with full management capabilities
2. "All Constants" page displaying every constant in the WordPress installation
3. Add new constant form with data type selection and description field
4. Import/Export page with CSV upload and download functionality
5. Screen Options panel for customizing table display preferences
6. Help page with comprehensive documentation and best practices

== Changelog ==

= 1.0.0 =
* Initial release
* Complete constant management with full CRUD functionality
* Dual view system: "My Constants" and "All Constants" pages
* WP_List_Table integration with native WordPress UI
* Support for all PHP constant types (string, integer, float, boolean, null)
* Active/inactive toggle functionality
* Predefined constant detection and conflict management
* Screen Options for customizable table views
* Comprehensive built-in help documentation
* Load order optimization with plugins_loaded priority 1
* Administrator-only access with proper capability checks
* Secure database storage with audit trail
* Import/Export functionality with CSV format support
* Comprehensive duplicate prevention and validation
* Enhanced error handling and user feedback

== Developer Information ==

= Database Schema =

The plugin creates a custom table `{prefix}pcm_constants` with the following structure:
* `id` - Primary key (auto-increment)
* `name` - Constant name (unique, varchar 191)
* `value` - Constant value (longtext)
* `type` - Data type (enum: string, integer, float, boolean, null)
* `is_active` - Whether the constant is loaded (tinyint)
* `description` - Optional description (text)
* `created_at` - Creation timestamp (datetime)
* `updated_at` - Last update timestamp (datetime)

= WordPress Hooks Used =

* `plugins_loaded` (priority 1) - Early constant loading for maximum compatibility
* `admin_menu` - Menu registration
* `admin_post_*` - Form submission handling
* `wp_ajax_*` - AJAX operations

= Load Order & Compatibility =

Constants are defined during `plugins_loaded` with priority 1, ensuring they are available to:
* All theme functions and templates
* Other plugins (unless using higher priority)
* WordPress core hooks like `init`, `wp_loaded`, etc.

= Security Implementation =

* Capability requirement: `manage_options` (administrators only)
* Nonce verification on all form submissions and AJAX requests
* SQL injection prevention with prepared statements
* Input sanitization using WordPress core functions
* Output escaping for all displayed data

= Code Standards =

This plugin follows WordPress coding standards and best practices:
* PSR-4 autoloading structure
* WordPress database abstraction layer
* Internationalization ready
* WP_List_Table implementation
* Standard WordPress admin UI patterns