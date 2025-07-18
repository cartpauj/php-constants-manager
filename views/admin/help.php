<?php
/**
 * Help page view
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e('PHP Constants Manager - Help', 'php-constants-manager'); ?></h1>
    
    <hr class="wp-header-end">
    
    <div class="phpcm-help-content">
        
        <!-- Table of Contents -->
        <div class="phpcm-toc-container">
            <h2><?php esc_html_e('Table of Contents', 'php-constants-manager'); ?></h2>
            <div class="phpcm-toc-grid">
                <div class="phpcm-toc-column">
                    <h3><?php esc_html_e('Getting Started', 'php-constants-manager'); ?></h3>
                    <ul>
                        <li><a href="#what-are-constants"><?php esc_html_e('What are PHP Constants?', 'php-constants-manager'); ?></a></li>
                        <li><a href="#how-to-use"><?php esc_html_e('How to Use This Plugin', 'php-constants-manager'); ?></a></li>
                        <li><a href="#constant-types"><?php esc_html_e('Constant Types', 'php-constants-manager'); ?></a></li>
                    </ul>
                    
                    <h3><?php esc_html_e('Advanced Topics', 'php-constants-manager'); ?></h3>
                    <ul>
                        <li><a href="#predefined-constants"><?php esc_html_e('Understanding Predefined Constants', 'php-constants-manager'); ?></a></li>
                        <li><a href="#load-order"><?php esc_html_e('Load Order & Timing', 'php-constants-manager'); ?></a></li>
                        <li><a href="#best-practices"><?php esc_html_e('Best Practices', 'php-constants-manager'); ?></a></li>
                    </ul>
                </div>
                
                <div class="phpcm-toc-column">
                    <h3><?php esc_html_e('Features', 'php-constants-manager'); ?></h3>
                    <ul>
                        <li><a href="#early-loading"><?php esc_html_e('Early Loading Settings', 'php-constants-manager'); ?></a></li>
                        <li><a href="#import-export"><?php esc_html_e('Import/Export Constants', 'php-constants-manager'); ?></a></li>
                        <li><a href="#csv-format"><?php esc_html_e('CSV Format Guide', 'php-constants-manager'); ?></a></li>
                        <li><a href="#overwrite-vs-skip-existing-constants"><?php esc_html_e('Overwrite vs. Skip Existing', 'php-constants-manager'); ?></a></li>
                        <li><a href="#value-validation"><?php esc_html_e('Value Validation', 'php-constants-manager'); ?></a></li>
                        <li><a href="#screen-options"><?php esc_html_e('Customizing Views', 'php-constants-manager'); ?></a></li>
                    </ul>
                    
                    <h3><?php esc_html_e('Reference', 'php-constants-manager'); ?></h3>
                    <ul>
                        <li><a href="#common-constants"><?php esc_html_e('Common WordPress Constants', 'php-constants-manager'); ?></a></li>
                        <li><a href="#troubleshooting"><?php esc_html_e('Troubleshooting', 'php-constants-manager'); ?></a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Getting Started Section -->
        <h2 id="what-are-constants"><?php esc_html_e('What are PHP Constants?', 'php-constants-manager'); ?></h2>
        <p><?php esc_html_e('PHP constants are identifiers for values that cannot be changed during script execution. Unlike variables, constants do not have a dollar sign ($) prefix and are typically written in UPPERCASE.', 'php-constants-manager'); ?></p>
        <p><?php esc_html_e('Examples:', 'php-constants-manager'); ?> <code>SITE_URL</code>, <code>API_KEY</code>, <code>DEBUG_MODE</code></p>
        
        <h2 id="how-to-use"><?php esc_html_e('How to Use This Plugin', 'php-constants-manager'); ?></h2>
        <ol>
            <li><strong><?php esc_html_e('Add New Constants:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Go to "My Constants" and click "Add New" to create a new constant.', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('Manage Existing:', 'php-constants-manager'); ?></strong> <?php esc_html_e('View, edit, activate/deactivate, or delete your constants from the "My Constants" page.', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('View All Constants:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Use "All Constants" to see every constant defined in your WordPress installation.', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('Import/Export:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Use "Import/Export" to backup constants or migrate them between sites.', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('Customize View:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Use the "Screen Options" button (top-right) to control how many items to display per page and which columns to show/hide.', 'php-constants-manager'); ?></li>
        </ol>
        
        <h2 id="constant-types"><?php esc_html_e('Constant Types', 'php-constants-manager'); ?></h2>
        
        <h3><?php esc_html_e('String', 'php-constants-manager'); ?></h3>
        <p><?php esc_html_e('Text values. Most common type for configuration settings.', 'php-constants-manager'); ?></p>
        <p><strong><?php esc_html_e('Examples:', 'php-constants-manager'); ?></strong> <code>SITE_NAME</code> = "My Website", <code>API_URL</code> = "https://api.example.com"</p>
        
        <h3><?php esc_html_e('Integer', 'php-constants-manager'); ?></h3>
        <p><?php esc_html_e('Whole numbers (positive, negative, or zero).', 'php-constants-manager'); ?></p>
        <p><strong><?php esc_html_e('Examples:', 'php-constants-manager'); ?></strong> <code>MAX_POSTS</code> = 10, <code>TIMEOUT</code> = 30</p>
        
        <h3><?php esc_html_e('Float', 'php-constants-manager'); ?></h3>
        <p><?php esc_html_e('Decimal numbers.', 'php-constants-manager'); ?></p>
        <p><strong><?php esc_html_e('Examples:', 'php-constants-manager'); ?></strong> <code>TAX_RATE</code> = 0.08, <code>VERSION</code> = 1.5</p>
        
        <h3><?php esc_html_e('Boolean', 'php-constants-manager'); ?></h3>
        <p><?php esc_html_e('True or false values. Use "true", "1", "yes", or "on" for true; anything else for false.', 'php-constants-manager'); ?></p>
        <p><strong><?php esc_html_e('Examples:', 'php-constants-manager'); ?></strong> <code>DEBUG_MODE</code> = true, <code>MAINTENANCE</code> = false</p>
        
        <h3><?php esc_html_e('Null', 'php-constants-manager'); ?></h3>
        <p><?php esc_html_e('Represents no value. Useful for optional settings.', 'php-constants-manager'); ?></p>
        <p><strong><?php esc_html_e('Examples:', 'php-constants-manager'); ?></strong> <code>OPTIONAL_SETTING</code> = null</p>
        
        <h2 id="predefined-constants"><?php esc_html_e('Understanding Predefined Constants', 'php-constants-manager'); ?></h2>
        
        <p><?php esc_html_e('The "Predefined" column in your constants list shows whether a constant is already defined elsewhere:', 'php-constants-manager'); ?></p>
        
        <ul>
            <li><strong><?php esc_html_e('Predefined: No', 'php-constants-manager'); ?></strong> - <?php esc_html_e('This constant is only defined by this plugin. You have full control over it.', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('Predefined: Overridden', 'php-constants-manager'); ?></strong> - <?php esc_html_e('This constant is already defined by WordPress core, another plugin, or your theme. Your custom value will only apply when the system constant is not available.', 'php-constants-manager'); ?></li>
        </ul>
        
        <h3><?php esc_html_e('Why Can\'t I Override Existing Constants?', 'php-constants-manager'); ?></h3>
        <p><?php esc_html_e('PHP constants cannot be redefined once they are set. This is a fundamental rule of PHP, not a limitation of this plugin.', 'php-constants-manager'); ?></p>
        
        <h4><?php esc_html_e('What Happens When:', 'php-constants-manager'); ?></h4>
        <ul>
            <li><strong><?php esc_html_e('Constant is Active + Not Predefined:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Your constant is successfully defined and working.', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('Constant is Active + Overridden:', 'php-constants-manager'); ?></strong> <?php esc_html_e('The system value takes precedence. Your value is stored and will be used if the system constant becomes unavailable.', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('Constant is Inactive:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Your constant is not defined at all, regardless of predefined status.', 'php-constants-manager'); ?></li>
        </ul>
        
        <h2 id="early-loading"><?php esc_html_e('Early Loading Settings', 'php-constants-manager'); ?></h2>
        
        <p><?php esc_html_e('The Early Loading feature allows you to load your constants before any other plugins, ensuring maximum compatibility. This is available in the Settings page.', 'php-constants-manager'); ?></p>
        
        <h3><?php esc_html_e('How Early Loading Works', 'php-constants-manager'); ?></h3>
        <p><?php esc_html_e('When you enable Early Loading, the plugin creates a must-use plugin file that WordPress loads automatically before any regular plugins. This file queries your constants database and defines all active constants immediately.', 'php-constants-manager'); ?></p>
        
        <h3><?php esc_html_e('When to Enable Early Loading', 'php-constants-manager'); ?></h3>
        <ul>
            <li><strong><?php esc_html_e('Plugin Dependencies:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Other plugins need your constants during their initialization', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('Configuration Constants:', 'php-constants-manager'); ?></strong> <?php esc_html_e('You\'re defining constants that affect how other plugins behave', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('Maximum Compatibility:', 'php-constants-manager'); ?></strong> <?php esc_html_e('You want to ensure constants are available as early as possible', 'php-constants-manager'); ?></li>
        </ul>
        
        <h3><?php esc_html_e('When Normal Loading is Fine', 'php-constants-manager'); ?></h3>
        <ul>
            <li><strong><?php esc_html_e('Theme Usage Only:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Constants are only used by themes or template files', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('No Compatibility Issues:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Everything works fine with the default loading', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('Clean mu-plugins:', 'php-constants-manager'); ?></strong> <?php esc_html_e('You prefer to minimize files in the mu-plugins directory', 'php-constants-manager'); ?></li>
        </ul>
        
        <h2 id="load-order"><?php esc_html_e('Load Order & Timing', 'php-constants-manager'); ?></h2>
        
        <h3><?php esc_html_e('Normal Loading (Default)', 'php-constants-manager'); ?></h3>
        <p><?php esc_html_e('By default, this plugin defines your constants during the', 'php-constants-manager'); ?> <code>plugins_loaded</code> <?php esc_html_e('action with priority 1, which means it loads very early in the WordPress loading process. However, some constants may already be defined by:', 'php-constants-manager'); ?></p>
        <ul>
            <li><strong><?php esc_html_e('wp-config.php file', 'php-constants-manager'); ?></strong> - <?php esc_html_e('Loads before any plugins', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('WordPress core', 'php-constants-manager'); ?></strong> - <?php esc_html_e('Many constants defined during WordPress bootstrap', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('Must-use plugins (mu-plugins)', 'php-constants-manager'); ?></strong> - <?php esc_html_e('Load before regular plugins', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('PHP extensions', 'php-constants-manager'); ?></strong> - <?php esc_html_e('Built-in PHP constants', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('Other plugins with higher priority', 'php-constants-manager'); ?></strong> - <?php esc_html_e('Plugins using', 'php-constants-manager'); ?> <code>plugins_loaded</code> <?php esc_html_e('with priority 0 or negative values', 'php-constants-manager'); ?></li>
        </ul>
        
        <h3><?php esc_html_e('Early Loading (Optional)', 'php-constants-manager'); ?></h3>
        <p><?php esc_html_e('When Early Loading is enabled, the complete WordPress loading order becomes:', 'php-constants-manager'); ?></p>
        <ol>
            <li><strong><?php esc_html_e('WordPress Core', 'php-constants-manager'); ?></strong> - <?php esc_html_e('wp-config.php and WordPress core constants', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('Must-Use Plugins', 'php-constants-manager'); ?></strong> - <?php esc_html_e('Your constants (when Early Loading is enabled)', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('Regular Plugins', 'php-constants-manager'); ?></strong> - <?php esc_html_e('All other plugins, including this plugin\'s normal loading', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('Themes', 'php-constants-manager'); ?></strong> - <?php esc_html_e('Active theme and child theme', 'php-constants-manager'); ?></li>
        </ol>
        
        <h3><?php esc_html_e('When Your Constants Are Available', 'php-constants-manager'); ?></h3>
        
        <h4><?php esc_html_e('Normal Loading (Default)', 'php-constants-manager'); ?></h4>
        <p><?php esc_html_e('Constants are defined during', 'php-constants-manager'); ?> <code>plugins_loaded</code> <?php esc_html_e('priority 1, available to:', 'php-constants-manager'); ?></p>
        <ul>
            <li><?php esc_html_e('All theme code (themes load after plugins)', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('Most other plugins (unless they use higher priority)', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('WordPress hooks like', 'php-constants-manager'); ?> <code>init</code>, <code>wp_loaded</code>, <?php esc_html_e('etc.', 'php-constants-manager'); ?></li>
        </ul>
        
        <h4><?php esc_html_e('Early Loading (When Enabled)', 'php-constants-manager'); ?></h4>
        <p><?php echo wp_kses(__('<strong>Important:</strong> When early loading is enabled, your constants are <em>not</em> loaded during <code>plugins_loaded</code> priority 1. Instead, they are loaded much earlier via the must-use plugin system.', 'php-constants-manager'), array('strong' => array(), 'em' => array(), 'code' => array())); ?></p>
        <p><?php esc_html_e('Your constants will be available to:', 'php-constants-manager'); ?></p>
        <ul>
            <li><strong><?php esc_html_e('All regular plugins', 'php-constants-manager'); ?></strong> - <?php esc_html_e('During their initialization and loading phase', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('All theme code', 'php-constants-manager'); ?></strong> - <?php esc_html_e('Functions, templates, and hooks', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('All WordPress hooks', 'php-constants-manager'); ?></strong> - <?php esc_html_e('Including very early hooks like', 'php-constants-manager'); ?> <code>plugins_loaded</code>, <code>init</code>, <?php esc_html_e('etc.', 'php-constants-manager'); ?></li>
        </ul>
        <p><?php esc_html_e('The only things that can override your constants when early loading is enabled are WordPress core constants (from wp-config.php) and other must-use plugins that load before this one.', 'php-constants-manager'); ?></p>
        
        <h2 id="best-practices"><?php esc_html_e('Best Practices', 'php-constants-manager'); ?></h2>
        
        <h3><?php esc_html_e('Naming Conventions', 'php-constants-manager'); ?></h3>
        <ul>
            <li><?php esc_html_e('Use UPPERCASE letters only', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('Separate words with underscores:', 'php-constants-manager'); ?> <code>MY_CUSTOM_SETTING</code></li>
            <li><?php esc_html_e('Start with a letter, not a number', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('Use descriptive names:', 'php-constants-manager'); ?> <code>MAX_LOGIN_ATTEMPTS</code> <?php esc_html_e('instead of', 'php-constants-manager'); ?> <code>MLA</code></li>
        </ul>
        
        <h3><?php esc_html_e('What to Define as Constants', 'php-constants-manager'); ?></h3>
        <ul>
            <li><?php esc_html_e('Configuration settings that never change during script execution', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('API keys and URLs', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('File paths and directories', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('Feature flags and debug settings', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('Version numbers and build information', 'php-constants-manager'); ?></li>
        </ul>
        
        <h3><?php esc_html_e('What NOT to Define as Constants', 'php-constants-manager'); ?></h3>
        <ul>
            <li><?php esc_html_e('Values that may need to change during script execution', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('User-specific data', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('Large arrays or objects (use variables instead)', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('Sensitive data like passwords (use secure storage methods)', 'php-constants-manager'); ?></li>
        </ul>
        
        
        <h2 id="import-export"><?php esc_html_e('Import/Export Constants', 'php-constants-manager'); ?></h2>
        
        <p><?php esc_html_e('The Import/Export feature allows you to backup your constants or migrate them between WordPress installations using CSV files.', 'php-constants-manager'); ?></p>
        
        <h3><?php esc_html_e('Exporting Constants', 'php-constants-manager'); ?></h3>
        <p><?php esc_html_e('Go to the "Import/Export" page and click "Export Constants" to download all your managed constants as a CSV file. The exported file includes:', 'php-constants-manager'); ?></p>
        <ul>
            <li><?php esc_html_e('All constants in your database (both active and inactive)', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('Complete data: Name, Value, Type, Status, Description', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('UTF-8 encoding for proper international character support', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('Timestamped filename:', 'php-constants-manager'); ?> <code>php-constants-YYYY-MM-DD-HH-MM-SS.csv</code></li>
        </ul>
        
        <h3><?php esc_html_e('Importing Constants', 'php-constants-manager'); ?></h3>
        <p><?php esc_html_e('Upload a CSV file to import constants into your database. The import process will:', 'php-constants-manager'); ?></p>
        <ul>
            <li><?php esc_html_e('Option to skip or overwrite existing constants', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('Validate constant names and data types with detailed error reporting', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('Show specific line numbers and reasons for any import errors', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('Provide comprehensive feedback on imported, updated, skipped, and error counts', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('Require header rows for proper column identification', 'php-constants-manager'); ?></li>
        </ul>
        
        <h2 id="csv-format"><?php esc_html_e('CSV Format Guide', 'php-constants-manager'); ?></h2>
        
        <div class="phpcm-import-warning">
            <p><strong><?php esc_html_e('Important:', 'php-constants-manager'); ?></strong> <?php esc_html_e('CSV files MUST include a header row. The first column must be named "Name" or "Constant Name".', 'php-constants-manager'); ?></p>
        </div>
        
        <p><strong><?php esc_html_e('Required Columns (minimum):', 'php-constants-manager'); ?></strong> <?php esc_html_e('Name, Value, Type', 'php-constants-manager'); ?></p>
        <p><strong><?php esc_html_e('Optional Columns:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Active, Description', 'php-constants-manager'); ?></p>
        
        <h4><?php esc_html_e('File Encoding', 'php-constants-manager'); ?></h4>
        <p><?php esc_html_e('CSV files should be saved in UTF-8 encoding for proper international character support. The importer automatically handles:', 'php-constants-manager'); ?></p>
        <ul>
            <li><?php esc_html_e('UTF-8 with BOM (Byte Order Mark) - BOM is automatically removed', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('UTF-8 without BOM - processed normally', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('Files exported from Excel, Google Sheets, or other spreadsheet applications', 'php-constants-manager'); ?></li>
        </ul>
        
        <h4><?php esc_html_e('Column Specifications', 'php-constants-manager'); ?></h4>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('Column', 'php-constants-manager'); ?></th>
                    <th><?php esc_html_e('Required', 'php-constants-manager'); ?></th>
                    <th><?php esc_html_e('Format', 'php-constants-manager'); ?></th>
                    <th><?php esc_html_e('Examples', 'php-constants-manager'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong><?php esc_html_e('Name', 'php-constants-manager'); ?></strong></td>
                    <td><?php esc_html_e('Yes', 'php-constants-manager'); ?></td>
                    <td><?php esc_html_e('Uppercase letters, numbers, underscores only. Must start with letter.', 'php-constants-manager'); ?></td>
                    <td><code>MY_CONSTANT</code>, <code>API_KEY</code>, <code>DEBUG_MODE</code></td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e('Value', 'php-constants-manager'); ?></strong></td>
                    <td><?php esc_html_e('Yes', 'php-constants-manager'); ?></td>
                    <td><?php esc_html_e('Any text. Will be converted based on Type.', 'php-constants-manager'); ?></td>
                    <td><code>Hello World</code>, <code>123</code>, <code>true</code>, <code>3.14</code></td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e('Type', 'php-constants-manager'); ?></strong></td>
                    <td><?php esc_html_e('Yes', 'php-constants-manager'); ?></td>
                    <td><?php esc_html_e('Must be one of: string, integer, float, boolean, null', 'php-constants-manager'); ?></td>
                    <td><code>string</code>, <code>integer</code>, <code>boolean</code></td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e('Active', 'php-constants-manager'); ?></strong></td>
                    <td><?php esc_html_e('No', 'php-constants-manager'); ?></td>
                    <td><?php esc_html_e('1 for active, 0 for inactive. Defaults to 1 if omitted.', 'php-constants-manager'); ?></td>
                    <td><code>1</code>, <code>0</code></td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e('Description', 'php-constants-manager'); ?></strong></td>
                    <td><?php esc_html_e('No', 'php-constants-manager'); ?></td>
                    <td><?php esc_html_e('Any text description.', 'php-constants-manager'); ?></td>
                    <td><code>API key for external service</code></td>
                </tr>
            </tbody>
        </table>
        
        <h4><?php esc_html_e('Example CSV Files', 'php-constants-manager'); ?></h4>
        
        <p><strong><?php esc_html_e('Example CSV File (headers required):', 'php-constants-manager'); ?></strong></p>
        <pre><code>Name,Value,Type,Active,Description
MY_API_KEY,abc123def456,string,1,External API authentication key
MAX_POSTS,25,integer,1,Maximum posts per page
TAX_RATE,0.08,float,1,Sales tax rate
DEBUG_MODE,true,boolean,0,Enable debug output
OPTIONAL_SETTING,,null,1,Optional configuration value</code></pre>
        
        <p><strong><?php esc_html_e('Minimum Required CSV (with headers):', 'php-constants-manager'); ?></strong></p>
        <pre><code>Name,Value,Type
MY_API_KEY,abc123def456,string
MAX_POSTS,25,integer
DEBUG_MODE,false,boolean</code></pre>
        
        <h4><?php esc_html_e('Boolean Values', 'php-constants-manager'); ?></h4>
        <p><?php esc_html_e('For boolean types, these values are recognized as TRUE:', 'php-constants-manager'); ?></p>
        <ul>
            <li><code>true</code>, <code>TRUE</code>, <code>True</code></li>
            <li><code>1</code></li>
            <li><code>yes</code>, <code>YES</code>, <code>Yes</code></li>
            <li><code>on</code>, <code>ON</code>, <code>On</code></li>
        </ul>
        <p><?php esc_html_e('All other values (including', 'php-constants-manager'); ?> <code>false</code>, <code>0</code>, <?php esc_html_e('empty string) are treated as FALSE.', 'php-constants-manager'); ?></p>
        
        <h3><?php esc_html_e('Import Tips', 'php-constants-manager'); ?></h3>
        <ul>
            <li><?php esc_html_e('Always backup your database before importing', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('CSV files must include a header row with column names', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('Start with a small test file to verify format', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('The import will show you exactly what was imported, skipped, and any errors', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('Existing constants with the same name are skipped (not overwritten)', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('Empty rows in your CSV file are automatically ignored', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('UTF-8 BOM (Byte Order Mark) is automatically detected and removed if present', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('Invalid constant names or types will be counted as errors and skipped', 'php-constants-manager'); ?></li>
        </ul>
        
        <div class="phpcm-import-warning">
            <p><strong><?php esc_html_e('Important:', 'php-constants-manager'); ?></strong> <?php echo wp_kses(__('The CSV import only adds constants to your plugin\'s database. Whether they actually take effect depends on load order and if they\'re already defined elsewhere (see <a href="#predefined-constants">Understanding Predefined Constants</a> above).', 'php-constants-manager'), array('a' => array('href' => array()))); ?></p>
        </div>
        
        <h3><?php esc_html_e('Import Error Reporting', 'php-constants-manager'); ?></h3>
        <p><?php esc_html_e('When importing CSV files, any errors will be displayed with specific details to help you fix your data:', 'php-constants-manager'); ?></p>
        <ul>
            <li><strong><?php esc_html_e('Line Numbers:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Shows exactly which CSV line had the problem', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('Specific Errors:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Details what was wrong (invalid name, wrong type, missing columns)', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('Value Examples:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Shows the problematic value and suggests correct format', 'php-constants-manager'); ?></li>
        </ul>
        
        <p><strong><?php esc_html_e('Example Error Messages:', 'php-constants-manager'); ?></strong></p>
        <ul>
            <li><code>Line 5: Missing required columns (need at least Name, Value, Type)</code></li>
            <li><code>Line 8: Invalid constant name "my-constant" (must be uppercase letters, numbers, and underscores only)</code></li>
            <li><code>Line 12: Invalid boolean value "maybe" (Constant: DEBUG_MODE)</code></li>
            <li><code>Line 15: Invalid integer value "3.14" (Constant: MAX_ITEMS)</code></li>
        </ul>
        
        <h3 id="overwrite-vs-skip-existing-constants"><?php esc_html_e('Overwrite vs. Skip Existing Constants', 'php-constants-manager'); ?></h3>
        <p><?php esc_html_e('The import page includes an "Overwrite existing constants" checkbox that controls how duplicate constants are handled:', 'php-constants-manager'); ?></p>
        
        <h4><?php esc_html_e('Checkbox Unchecked (Default):', 'php-constants-manager'); ?></h4>
        <ul>
            <li><?php esc_html_e('Constants with matching names are skipped', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('Existing data is preserved unchanged', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('Only new constants (not in your database) are imported', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('Safe for adding new constants without affecting existing ones', 'php-constants-manager'); ?></li>
        </ul>
        
        <h4><?php esc_html_e('Checkbox Checked (Overwrite Mode):', 'php-constants-manager'); ?></h4>
        <ul>
            <li><?php esc_html_e('Constants with matching names are updated with CSV data', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('Value, type, status, and description are all updated', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('New constants are still imported normally', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('Useful for bulk updates or synchronizing data between environments', 'php-constants-manager'); ?></li>
        </ul>
        
        <div class="phpcm-import-warning">
            <p><strong><?php esc_html_e('Warning:', 'php-constants-manager'); ?></strong> <?php esc_html_e('When overwrite mode is enabled, existing constant data will be replaced. Always backup your database before using overwrite mode.', 'php-constants-manager'); ?></p>
        </div>
        
        <h4><?php esc_html_e('Import Result Messages:', 'php-constants-manager'); ?></h4>
        <ul>
            <li><strong><?php esc_html_e('Imported:', 'php-constants-manager'); ?></strong> <?php esc_html_e('New constants added to the database', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('Updated:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Existing constants modified (only shown in overwrite mode)', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('Skipped:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Existing constants left unchanged (only shown in skip mode)', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('Errors:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Rows that could not be processed due to validation failures', 'php-constants-manager'); ?></li>
        </ul>
        
        <h2 id="value-validation"><?php esc_html_e('Value Validation', 'php-constants-manager'); ?></h2>
        <p><?php esc_html_e('The plugin validates that constant values match their selected type, both when creating/editing constants and during CSV import:', 'php-constants-manager'); ?></p>
        
        <h4><?php esc_html_e('Validation Rules', 'php-constants-manager'); ?></h4>
        <ul>
            <li><strong><?php esc_html_e('Integer:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Must be whole numbers (42, -10, 0). Decimals like "3.14" are rejected.', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('Float:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Must be valid numbers (3.14, -2.5, 10). Text like "abc" is rejected.', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('Boolean:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Must be: true, false, 1, 0, yes, no, on, off (case-insensitive). Values like "maybe" are rejected.', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('String:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Any value is accepted, including quotes and special characters.', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('NULL:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Value field is disabled and automatically cleared.', 'php-constants-manager'); ?></li>
        </ul>
        
        <h4><?php esc_html_e('Real-time Validation', 'php-constants-manager'); ?></h4>
        <p><?php esc_html_e('When creating or editing constants, the form provides:', 'php-constants-manager'); ?></p>
        <ul>
            <li><?php esc_html_e('Immediate feedback as you type in the value field', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('Red borders and error messages for invalid values', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('Type-specific placeholder text and examples', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('Automatic validation when changing the type dropdown', 'php-constants-manager'); ?></li>
        </ul>
        
        <h2 id="common-constants"><?php esc_html_e('Common WordPress Constants', 'php-constants-manager'); ?></h2>
        <p><?php esc_html_e('Here are some constants you might see that are already defined by WordPress:', 'php-constants-manager'); ?></p>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('Constant', 'php-constants-manager'); ?></th>
                    <th><?php esc_html_e('Description', 'php-constants-manager'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>WP_DEBUG</code></td>
                    <td><?php esc_html_e('Enables WordPress debug mode', 'php-constants-manager'); ?></td>
                </tr>
                <tr>
                    <td><code>WP_DEBUG_LOG</code></td>
                    <td><?php esc_html_e('Enables debug logging to file', 'php-constants-manager'); ?></td>
                </tr>
                <tr>
                    <td><code>ABSPATH</code></td>
                    <td><?php esc_html_e('Absolute path to WordPress directory', 'php-constants-manager'); ?></td>
                </tr>
                <tr>
                    <td><code>WP_CONTENT_DIR</code></td>
                    <td><?php esc_html_e('Path to wp-content directory', 'php-constants-manager'); ?></td>
                </tr>
                <tr>
                    <td><code>WP_CONTENT_URL</code></td>
                    <td><?php esc_html_e('URL to wp-content directory', 'php-constants-manager'); ?></td>
                </tr>
                <tr>
                    <td><code>DB_HOST</code></td>
                    <td><?php esc_html_e('Database server hostname', 'php-constants-manager'); ?></td>
                </tr>
                <tr>
                    <td><code>DB_NAME</code></td>
                    <td><?php esc_html_e('Database name', 'php-constants-manager'); ?></td>
                </tr>
            </tbody>
        </table>
        
        <h2 id="screen-options"><?php esc_html_e('Customizing Your View', 'php-constants-manager'); ?></h2>
        
        <h3><?php esc_html_e('Screen Options', 'php-constants-manager'); ?></h3>
        <p><?php esc_html_e('Both the "My Constants" and "All Constants" pages include a "Screen Options" button in the top-right corner that allows you to customize how the tables are displayed.', 'php-constants-manager'); ?></p>
        
        <h4><?php esc_html_e('Number of Items', 'php-constants-manager'); ?></h4>
        <p><?php esc_html_e('You can control how many constants are displayed per page. The default is 50, but you can choose from 5, 10, 20, 50, 100, or more. This is especially useful on the "All Constants" page which may show thousands of constants.', 'php-constants-manager'); ?></p>
        
        <h4><?php esc_html_e('Column Visibility', 'php-constants-manager'); ?></h4>
        <p><?php esc_html_e('You can show or hide specific columns in both tables:', 'php-constants-manager'); ?></p>
        <ul>
            <li><strong><?php esc_html_e('My Constants:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Name, Value, Status, Predefined, Description, Created', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('All Constants:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Name, Value & Type, Category', 'php-constants-manager'); ?></li>
        </ul>
        <p><?php esc_html_e('When you hide columns, the remaining columns will automatically expand to fill the available space, making better use of your screen real estate.', 'php-constants-manager'); ?></p>
        
        <p><strong><?php esc_html_e('Tip:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Your Screen Options preferences are saved automatically and will persist across page loads and browser sessions.', 'php-constants-manager'); ?></p>
        
        <h2 id="troubleshooting"><?php esc_html_e('Troubleshooting', 'php-constants-manager'); ?></h2>
        
        <h3><?php esc_html_e('My constant isn\'t working', 'php-constants-manager'); ?></h3>
        <ol>
            <li><?php esc_html_e('Check if it\'s marked as "Active"', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('Check if it shows "Predefined: Overridden" (meaning something else defined it first)', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('Verify the constant name follows PHP naming rules', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('Check for PHP syntax errors in your constant value', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('Consider load order: if another plugin defines the same constant with higher priority, it will override yours', 'php-constants-manager'); ?></li>
            <li><?php esc_html_e('Try enabling Early Loading in Settings if other plugins need your constants during initialization', 'php-constants-manager'); ?></li>
        </ol>
        
        <h3><?php esc_html_e('I see "Overridden" in the predefined column', 'php-constants-manager'); ?></h3>
        <p><?php esc_html_e('The "Overridden" badge (with warning icon) indicates that your constant is already defined elsewhere in the system. Your definition is saved and will be used as a fallback if the system constant becomes unavailable, but currently the system value takes precedence.', 'php-constants-manager'); ?></p>
        
        <h3><?php esc_html_e('How to check if my constant is working', 'php-constants-manager'); ?></h3>
        <p><?php esc_html_e('You can test your constants in your theme or plugin code. Remember that your constants are available after the', 'php-constants-manager'); ?> <code>plugins_loaded</code> <?php esc_html_e('action (priority 1), so test them in appropriate hooks:', 'php-constants-manager'); ?></p>
        <pre><code>// Test in theme functions.php or after plugins_loaded
add_action('init', function() {
    if (defined('MY_CONSTANT')) {
        echo esc_html('MY_CONSTANT is defined with value: ' . MY_CONSTANT);
    } else {
        echo esc_html('MY_CONSTANT is not defined');
    }
});

// Or test directly in template files (themes load after plugins)
if (defined('MY_CONSTANT')) {
    // Your constant is available here
}</code></pre>
        
        <h3><?php esc_html_e('Database table doesn\'t exist or can\'t be created', 'php-constants-manager'); ?></h3>
        <p><?php esc_html_e('If you see database errors, check the following:', 'php-constants-manager'); ?></p>
        <ol>
            <li><strong><?php esc_html_e('Database Permissions:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Ensure your WordPress database user has CREATE TABLE privileges', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('Disk Space:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Check if your server has sufficient disk space', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('MySQL Version:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Verify MySQL 5.6+ or MariaDB 10.0+ compatibility', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('Plugin Activation:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Try deactivating and reactivating the plugin', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('Manual Creation:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Contact your hosting provider to manually create the table if needed', 'php-constants-manager'); ?></li>
        </ol>
        
        <h4><?php esc_html_e('Manual Table Creation SQL', 'php-constants-manager'); ?></h4>
        <p><?php esc_html_e('If automatic table creation fails, you can manually run this SQL in your database:', 'php-constants-manager'); ?></p>
        <pre><code>CREATE TABLE wp_phpcm_constants (
    id int(11) NOT NULL AUTO_INCREMENT,
    name varchar(255) NOT NULL,
    value text,
    type varchar(20) NOT NULL DEFAULT 'string',
    is_active tinyint(1) NOT NULL DEFAULT 1,
    description text,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY name (name),
    KEY is_active (is_active)
) DEFAULT CHARSET=utf8mb4;</code></pre>
        <p><strong><?php esc_html_e('Note:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Replace "wp_" with your actual WordPress table prefix if different.', 'php-constants-manager'); ?></p>
        
        <h3><?php esc_html_e('Early Loading Setup Failed', 'php-constants-manager'); ?></h3>
        <p><?php esc_html_e('If the automatic Early Loading setup fails in Settings, you can manually create the must-use plugin file:', 'php-constants-manager'); ?></p>
        
        <h4><?php esc_html_e('Manual Must-Use Plugin Creation', 'php-constants-manager'); ?></h4>
        <ol>
            <li><strong><?php esc_html_e('Create Directory:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Ensure the mu-plugins directory exists:', 'php-constants-manager'); ?> <code>wp-content/mu-plugins/</code></li>
            <li><strong><?php esc_html_e('Create File:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Create a new file named:', 'php-constants-manager'); ?> <code>0001-php-constants-manager-early.php</code></li>
            <li><strong><?php esc_html_e('Add Content:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Copy the following code into the file:', 'php-constants-manager'); ?></li>
        </ol>
        
        <h4><?php esc_html_e('Must-Use Plugin Code', 'php-constants-manager'); ?></h4>
        <p><strong><?php esc_html_e('File:', 'php-constants-manager'); ?></strong> <code>wp-content/mu-plugins/0001-php-constants-manager-early.php</code></p>
        <pre><code>&lt;?php
/**
 * PHP Constants Manager - Early Loading
 * This file loads constants from PHP Constants Manager before other plugins
 * DO NOT EDIT - Managed by PHP Constants Manager plugin
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Load PHP Constants Manager constants early
function phpcm_load_early_constants() {
    global $wpdb;
    
    // Initialize the global array
    $GLOBALS['phpcm_early_defined_constants'] = array();
    
    // Get the table name
    $table_name = $wpdb->prefix . 'phpcm_constants';
    
    // Check if table exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") !== $table_name) {
        return;
    }
    
    // Get active constants
    $constants = $wpdb->get_results(
        "SELECT name, value, type FROM $table_name WHERE is_active = 1"
    );
    
    if (empty($constants)) {
        return;
    }
    
    // Track which constants we successfully define
    $phpcm_early_defined = array();
    
    foreach ($constants as $constant) {
        if (!defined($constant->name)) {
            $value = $constant->value;
            
            switch ($constant->type) {
                case 'boolean':
                    if (is_string($value)) {
                        $lower_value = strtolower(trim($value));
                        if (in_array($lower_value, ['true', '1', 'yes', 'on'], true)) {
                            $value = true;
                        } elseif (in_array($lower_value, ['false', '0', 'no', 'off', ''], true)) {
                            $value = false;
                        } else {
                            $value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                            if ($value === null) {
                                $value = false;
                            }
                        }
                    } elseif (is_numeric($value)) {
                        $value = (bool)intval($value);
                    } else {
                        $value = (bool)$value;
                    }
                    break;
                case 'integer':
                    if (is_numeric($value)) {
                        $value = intval($value);
                    } else {
                        $value = 0;
                    }
                    break;
                case 'float':
                    if (is_numeric($value)) {
                        $value = floatval($value);
                    } else {
                        $value = 0.0;
                    }
                    break;
                case 'null':
                    $value = null;
                    break;
            }
            
            define($constant->name, $value);
            $phpcm_early_defined[] = $constant->name;
        }
    }
    
    // Store the list for the main plugin to check
    $GLOBALS['phpcm_early_defined_constants'] = $phpcm_early_defined;
}

// Load constants
phpcm_load_early_constants();</code></pre>
        
        <h4><?php esc_html_e('Important Notes', 'php-constants-manager'); ?></h4>
        <ul>
            <li><strong><?php esc_html_e('Filename Format:', 'php-constants-manager'); ?></strong> <?php esc_html_e('The "0001-" prefix ensures this file loads before other must-use plugins', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('File Permissions:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Ensure the file is readable by your web server (typically 644)', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('Table Prefix:', 'php-constants-manager'); ?></strong> <?php esc_html_e('The code automatically uses your WordPress table prefix', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('Settings Integration:', 'php-constants-manager'); ?></strong> <?php esc_html_e('After manual creation, visit Settings and check "Enable early loading" to sync the plugin state with the file', 'php-constants-manager'); ?></li>
        </ul>
        
        <h4><?php esc_html_e('Common Issues', 'php-constants-manager'); ?></h4>
        <ul>
            <li><strong><?php esc_html_e('Permission Denied:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Contact your hosting provider to create the mu-plugins directory and set proper permissions', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('File Not Loading:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Verify the filename is exactly: 0001-php-constants-manager-early.php', 'php-constants-manager'); ?></li>
            <li><strong><?php esc_html_e('PHP Errors:', 'php-constants-manager'); ?></strong> <?php esc_html_e('Check that the file content was copied correctly without any extra characters', 'php-constants-manager'); ?></li>
        </ul>

        <h3><?php esc_html_e('Need More Help?', 'php-constants-manager'); ?></h3>
        <p><?php esc_html_e('Use the "All Constants" page to see every constant currently defined in your WordPress installation. This can help you understand what\'s already taken and avoid conflicts.', 'php-constants-manager'); ?></p>
        
    </div>
</div>