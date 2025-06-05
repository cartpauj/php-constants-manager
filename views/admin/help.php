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
    <h1 class="wp-heading-inline"><?php _e('PHP Constants Manager - Help', 'php-constants-manager'); ?></h1>
    
    <hr class="wp-header-end">
    
    <div class="pcm-help-content">
        
        <!-- Table of Contents -->
        <div class="pcm-toc-container">
            <h2><?php _e('Table of Contents', 'php-constants-manager'); ?></h2>
            <div class="pcm-toc-grid">
                <div class="pcm-toc-column">
                    <h3><?php _e('Getting Started', 'php-constants-manager'); ?></h3>
                    <ul>
                        <li><a href="#what-are-constants"><?php _e('What are PHP Constants?', 'php-constants-manager'); ?></a></li>
                        <li><a href="#how-to-use"><?php _e('How to Use This Plugin', 'php-constants-manager'); ?></a></li>
                        <li><a href="#constant-types"><?php _e('Constant Types', 'php-constants-manager'); ?></a></li>
                    </ul>
                    
                    <h3><?php _e('Advanced Topics', 'php-constants-manager'); ?></h3>
                    <ul>
                        <li><a href="#predefined-constants"><?php _e('Understanding Predefined Constants', 'php-constants-manager'); ?></a></li>
                        <li><a href="#load-order"><?php _e('Load Order & Timing', 'php-constants-manager'); ?></a></li>
                        <li><a href="#best-practices"><?php _e('Best Practices', 'php-constants-manager'); ?></a></li>
                    </ul>
                </div>
                
                <div class="pcm-toc-column">
                    <h3><?php _e('Features', 'php-constants-manager'); ?></h3>
                    <ul>
                        <li><a href="#import-export"><?php _e('Import/Export Constants', 'php-constants-manager'); ?></a></li>
                        <li><a href="#csv-format"><?php _e('CSV Format Guide', 'php-constants-manager'); ?></a></li>
                        <li><a href="#overwrite-vs-skip-existing-constants"><?php _e('Overwrite vs. Skip Existing', 'php-constants-manager'); ?></a></li>
                        <li><a href="#value-validation"><?php _e('Value Validation', 'php-constants-manager'); ?></a></li>
                        <li><a href="#screen-options"><?php _e('Customizing Views', 'php-constants-manager'); ?></a></li>
                    </ul>
                    
                    <h3><?php _e('Reference', 'php-constants-manager'); ?></h3>
                    <ul>
                        <li><a href="#common-constants"><?php _e('Common WordPress Constants', 'php-constants-manager'); ?></a></li>
                        <li><a href="#troubleshooting"><?php _e('Troubleshooting', 'php-constants-manager'); ?></a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Getting Started Section -->
        <h2 id="what-are-constants"><?php _e('What are PHP Constants?', 'php-constants-manager'); ?></h2>
        <p><?php _e('PHP constants are identifiers for values that cannot be changed during script execution. Unlike variables, constants do not have a dollar sign ($) prefix and are typically written in UPPERCASE.', 'php-constants-manager'); ?></p>
        <p><?php _e('Examples: <code>SITE_URL</code>, <code>API_KEY</code>, <code>DEBUG_MODE</code>', 'php-constants-manager'); ?></p>
        
        <h2 id="how-to-use"><?php _e('How to Use This Plugin', 'php-constants-manager'); ?></h2>
        <ol>
            <li><strong><?php _e('Add New Constants:', 'php-constants-manager'); ?></strong> <?php _e('Go to "My Constants" and click "Add New" to create a new constant.', 'php-constants-manager'); ?></li>
            <li><strong><?php _e('Manage Existing:', 'php-constants-manager'); ?></strong> <?php _e('View, edit, activate/deactivate, or delete your constants from the "My Constants" page.', 'php-constants-manager'); ?></li>
            <li><strong><?php _e('View All Constants:', 'php-constants-manager'); ?></strong> <?php _e('Use "All Constants" to see every constant defined in your WordPress installation.', 'php-constants-manager'); ?></li>
            <li><strong><?php _e('Import/Export:', 'php-constants-manager'); ?></strong> <?php _e('Use "Import/Export" to backup constants or migrate them between sites.', 'php-constants-manager'); ?></li>
            <li><strong><?php _e('Customize View:', 'php-constants-manager'); ?></strong> <?php _e('Use the "Screen Options" button (top-right) to control how many items to display per page and which columns to show/hide.', 'php-constants-manager'); ?></li>
        </ol>
        
        <h2 id="constant-types"><?php _e('Constant Types', 'php-constants-manager'); ?></h2>
        
        <h3><?php _e('String', 'php-constants-manager'); ?></h3>
        <p><?php _e('Text values. Most common type for configuration settings.', 'php-constants-manager'); ?></p>
        <p><strong><?php _e('Examples:', 'php-constants-manager'); ?></strong> <code>SITE_NAME</code> = "My Website", <code>API_URL</code> = "https://api.example.com"</p>
        
        <h3><?php _e('Integer', 'php-constants-manager'); ?></h3>
        <p><?php _e('Whole numbers (positive, negative, or zero).', 'php-constants-manager'); ?></p>
        <p><strong><?php _e('Examples:', 'php-constants-manager'); ?></strong> <code>MAX_POSTS</code> = 10, <code>TIMEOUT</code> = 30</p>
        
        <h3><?php _e('Float', 'php-constants-manager'); ?></h3>
        <p><?php _e('Decimal numbers.', 'php-constants-manager'); ?></p>
        <p><strong><?php _e('Examples:', 'php-constants-manager'); ?></strong> <code>TAX_RATE</code> = 0.08, <code>VERSION</code> = 1.5</p>
        
        <h3><?php _e('Boolean', 'php-constants-manager'); ?></h3>
        <p><?php _e('True or false values. Use "true", "1", "yes", or "on" for true; anything else for false.', 'php-constants-manager'); ?></p>
        <p><strong><?php _e('Examples:', 'php-constants-manager'); ?></strong> <code>DEBUG_MODE</code> = true, <code>MAINTENANCE</code> = false</p>
        
        <h3><?php _e('Null', 'php-constants-manager'); ?></h3>
        <p><?php _e('Represents no value. Useful for optional settings.', 'php-constants-manager'); ?></p>
        <p><strong><?php _e('Examples:', 'php-constants-manager'); ?></strong> <code>OPTIONAL_SETTING</code> = null</p>
        
        <h2 id="predefined-constants"><?php _e('Understanding Predefined Constants', 'php-constants-manager'); ?></h2>
        
        <p><?php _e('The "Predefined" column in your constants list shows whether a constant is already defined elsewhere:', 'php-constants-manager'); ?></p>
        
        <ul>
            <li><strong><?php _e('Predefined: No', 'php-constants-manager'); ?></strong> - <?php _e('This constant is only defined by this plugin. You have full control over it.', 'php-constants-manager'); ?></li>
            <li><strong><?php _e('Predefined: Yes', 'php-constants-manager'); ?></strong> - <?php _e('This constant is already defined by WordPress core, another plugin, or your theme. Your definition will only take effect if the original definition is removed.', 'php-constants-manager'); ?></li>
        </ul>
        
        <h3><?php _e('Why Can\'t I Override Existing Constants?', 'php-constants-manager'); ?></h3>
        <p><?php _e('PHP constants cannot be redefined once they are set. This is a fundamental rule of PHP, not a limitation of this plugin.', 'php-constants-manager'); ?></p>
        
        <h4><?php _e('What Happens When:', 'php-constants-manager'); ?></h4>
        <ul>
            <li><strong><?php _e('Constant is Active + Not Predefined:', 'php-constants-manager'); ?></strong> <?php _e('Your constant is successfully defined and working.', 'php-constants-manager'); ?></li>
            <li><strong><?php _e('Constant is Active + Predefined:', 'php-constants-manager'); ?></strong> <?php _e('The predefined value takes precedence. Your value is stored but not used.', 'php-constants-manager'); ?></li>
            <li><strong><?php _e('Constant is Inactive:', 'php-constants-manager'); ?></strong> <?php _e('Your constant is not defined at all, regardless of predefined status.', 'php-constants-manager'); ?></li>
        </ul>
        
        <h2 id="load-order"><?php _e('Load Order & Timing', 'php-constants-manager'); ?></h2>
        <p><?php _e('This plugin defines your constants during the <code>plugins_loaded</code> action with priority 1, which means it loads very early in the WordPress loading process. However, some constants may already be defined by:', 'php-constants-manager'); ?></p>
        <ul>
            <li><?php _e('<strong>wp-config.php file</strong> - Loads before any plugins', 'php-constants-manager'); ?></li>
            <li><?php _e('<strong>WordPress core</strong> - Many constants defined during WordPress bootstrap', 'php-constants-manager'); ?></li>
            <li><?php _e('<strong>Must-use plugins (mu-plugins)</strong> - Load before regular plugins', 'php-constants-manager'); ?></li>
            <li><?php _e('<strong>PHP extensions</strong> - Built-in PHP constants', 'php-constants-manager'); ?></li>
            <li><?php _e('<strong>Other plugins with higher priority</strong> - Plugins using <code>plugins_loaded</code> with priority 0 or negative values', 'php-constants-manager'); ?></li>
        </ul>
        
        <h3><?php _e('When Your Constants Are Available', 'php-constants-manager'); ?></h3>
        <p><?php _e('Your constants are defined during <code>plugins_loaded</code> priority 1, which means they are available to:', 'php-constants-manager'); ?></p>
        <ul>
            <li><?php _e('All theme code (themes load after plugins)', 'php-constants-manager'); ?></li>
            <li><?php _e('Most other plugins (unless they use higher priority)', 'php-constants-manager'); ?></li>
            <li><?php _e('WordPress hooks like <code>init</code>, <code>wp_loaded</code>, etc.', 'php-constants-manager'); ?></li>
        </ul>
        
        <h2 id="best-practices"><?php _e('Best Practices', 'php-constants-manager'); ?></h2>
        
        <h3><?php _e('Naming Conventions', 'php-constants-manager'); ?></h3>
        <ul>
            <li><?php _e('Use UPPERCASE letters only', 'php-constants-manager'); ?></li>
            <li><?php _e('Separate words with underscores: <code>MY_CUSTOM_SETTING</code>', 'php-constants-manager'); ?></li>
            <li><?php _e('Start with a letter, not a number', 'php-constants-manager'); ?></li>
            <li><?php _e('Use descriptive names: <code>MAX_LOGIN_ATTEMPTS</code> instead of <code>MLA</code>', 'php-constants-manager'); ?></li>
        </ul>
        
        <h3><?php _e('What to Define as Constants', 'php-constants-manager'); ?></h3>
        <ul>
            <li><?php _e('Configuration settings that never change during script execution', 'php-constants-manager'); ?></li>
            <li><?php _e('API keys and URLs', 'php-constants-manager'); ?></li>
            <li><?php _e('File paths and directories', 'php-constants-manager'); ?></li>
            <li><?php _e('Feature flags and debug settings', 'php-constants-manager'); ?></li>
            <li><?php _e('Version numbers and build information', 'php-constants-manager'); ?></li>
        </ul>
        
        <h3><?php _e('What NOT to Define as Constants', 'php-constants-manager'); ?></h3>
        <ul>
            <li><?php _e('Values that may need to change during script execution', 'php-constants-manager'); ?></li>
            <li><?php _e('User-specific data', 'php-constants-manager'); ?></li>
            <li><?php _e('Large arrays or objects (use variables instead)', 'php-constants-manager'); ?></li>
            <li><?php _e('Sensitive data like passwords (use secure storage methods)', 'php-constants-manager'); ?></li>
        </ul>
        
        
        <h2 id="import-export"><?php _e('Import/Export Constants', 'php-constants-manager'); ?></h2>
        
        <p><?php _e('The Import/Export feature allows you to backup your constants or migrate them between WordPress installations using CSV files.', 'php-constants-manager'); ?></p>
        
        <h3><?php _e('Exporting Constants', 'php-constants-manager'); ?></h3>
        <p><?php _e('Go to the "Import/Export" page and click "Export Constants" to download all your managed constants as a CSV file. The exported file includes:', 'php-constants-manager'); ?></p>
        <ul>
            <li><?php _e('All constants in your database (both active and inactive)', 'php-constants-manager'); ?></li>
            <li><?php _e('Complete data: Name, Value, Type, Status, Description', 'php-constants-manager'); ?></li>
            <li><?php _e('UTF-8 encoding for proper international character support', 'php-constants-manager'); ?></li>
            <li><?php _e('Timestamped filename: <code>php-constants-YYYY-MM-DD-HH-MM-SS.csv</code>', 'php-constants-manager'); ?></li>
        </ul>
        
        <h3><?php _e('Importing Constants', 'php-constants-manager'); ?></h3>
        <p><?php _e('Upload a CSV file to import constants into your database. The import process will:', 'php-constants-manager'); ?></p>
        <ul>
            <li><?php _e('Option to skip or overwrite existing constants', 'php-constants-manager'); ?></li>
            <li><?php _e('Validate constant names and data types with detailed error reporting', 'php-constants-manager'); ?></li>
            <li><?php _e('Show specific line numbers and reasons for any import errors', 'php-constants-manager'); ?></li>
            <li><?php _e('Provide comprehensive feedback on imported, updated, skipped, and error counts', 'php-constants-manager'); ?></li>
            <li><?php _e('Require header rows for proper column identification', 'php-constants-manager'); ?></li>
        </ul>
        
        <h2 id="csv-format"><?php _e('CSV Format Guide', 'php-constants-manager'); ?></h2>
        
        <div class="pcm-import-warning">
            <p><strong><?php _e('Important:', 'php-constants-manager'); ?></strong> <?php _e('CSV files MUST include a header row. The first column must be named "Name" or "Constant Name".', 'php-constants-manager'); ?></p>
        </div>
        
        <p><strong><?php _e('Required Columns (minimum):', 'php-constants-manager'); ?></strong> <?php _e('Name, Value, Type', 'php-constants-manager'); ?></p>
        <p><strong><?php _e('Optional Columns:', 'php-constants-manager'); ?></strong> <?php _e('Active, Description', 'php-constants-manager'); ?></p>
        
        <h4><?php _e('File Encoding', 'php-constants-manager'); ?></h4>
        <p><?php _e('CSV files should be saved in UTF-8 encoding for proper international character support. The importer automatically handles:', 'php-constants-manager'); ?></p>
        <ul>
            <li><?php _e('UTF-8 with BOM (Byte Order Mark) - BOM is automatically removed', 'php-constants-manager'); ?></li>
            <li><?php _e('UTF-8 without BOM - processed normally', 'php-constants-manager'); ?></li>
            <li><?php _e('Files exported from Excel, Google Sheets, or other spreadsheet applications', 'php-constants-manager'); ?></li>
        </ul>
        
        <h4><?php _e('Column Specifications', 'php-constants-manager'); ?></h4>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Column', 'php-constants-manager'); ?></th>
                    <th><?php _e('Required', 'php-constants-manager'); ?></th>
                    <th><?php _e('Format', 'php-constants-manager'); ?></th>
                    <th><?php _e('Examples', 'php-constants-manager'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong><?php _e('Name', 'php-constants-manager'); ?></strong></td>
                    <td><?php _e('Yes', 'php-constants-manager'); ?></td>
                    <td><?php _e('Uppercase letters, numbers, underscores only. Must start with letter.', 'php-constants-manager'); ?></td>
                    <td><code>MY_CONSTANT</code>, <code>API_KEY</code>, <code>DEBUG_MODE</code></td>
                </tr>
                <tr>
                    <td><strong><?php _e('Value', 'php-constants-manager'); ?></strong></td>
                    <td><?php _e('Yes', 'php-constants-manager'); ?></td>
                    <td><?php _e('Any text. Will be converted based on Type.', 'php-constants-manager'); ?></td>
                    <td><code>Hello World</code>, <code>123</code>, <code>true</code>, <code>3.14</code></td>
                </tr>
                <tr>
                    <td><strong><?php _e('Type', 'php-constants-manager'); ?></strong></td>
                    <td><?php _e('Yes', 'php-constants-manager'); ?></td>
                    <td><?php _e('Must be one of: string, integer, float, boolean, null', 'php-constants-manager'); ?></td>
                    <td><code>string</code>, <code>integer</code>, <code>boolean</code></td>
                </tr>
                <tr>
                    <td><strong><?php _e('Active', 'php-constants-manager'); ?></strong></td>
                    <td><?php _e('No', 'php-constants-manager'); ?></td>
                    <td><?php _e('1 for active, 0 for inactive. Defaults to 1 if omitted.', 'php-constants-manager'); ?></td>
                    <td><code>1</code>, <code>0</code></td>
                </tr>
                <tr>
                    <td><strong><?php _e('Description', 'php-constants-manager'); ?></strong></td>
                    <td><?php _e('No', 'php-constants-manager'); ?></td>
                    <td><?php _e('Any text description.', 'php-constants-manager'); ?></td>
                    <td><code>API key for external service</code></td>
                </tr>
            </tbody>
        </table>
        
        <h4><?php _e('Example CSV Files', 'php-constants-manager'); ?></h4>
        
        <p><strong><?php _e('Example CSV File (headers required):', 'php-constants-manager'); ?></strong></p>
        <pre><code>Name,Value,Type,Active,Description
MY_API_KEY,abc123def456,string,1,External API authentication key
MAX_POSTS,25,integer,1,Maximum posts per page
TAX_RATE,0.08,float,1,Sales tax rate
DEBUG_MODE,true,boolean,0,Enable debug output
OPTIONAL_SETTING,,null,1,Optional configuration value</code></pre>
        
        <p><strong><?php _e('Minimum Required CSV (with headers):', 'php-constants-manager'); ?></strong></p>
        <pre><code>Name,Value,Type
MY_API_KEY,abc123def456,string
MAX_POSTS,25,integer
DEBUG_MODE,false,boolean</code></pre>
        
        <h4><?php _e('Boolean Values', 'php-constants-manager'); ?></h4>
        <p><?php _e('For boolean types, these values are recognized as TRUE:', 'php-constants-manager'); ?></p>
        <ul>
            <li><code>true</code>, <code>TRUE</code>, <code>True</code></li>
            <li><code>1</code></li>
            <li><code>yes</code>, <code>YES</code>, <code>Yes</code></li>
            <li><code>on</code>, <code>ON</code>, <code>On</code></li>
        </ul>
        <p><?php _e('All other values (including <code>false</code>, <code>0</code>, empty string) are treated as FALSE.', 'php-constants-manager'); ?></p>
        
        <h3><?php _e('Import Tips', 'php-constants-manager'); ?></h3>
        <ul>
            <li><?php _e('Always backup your database before importing', 'php-constants-manager'); ?></li>
            <li><?php _e('CSV files must include a header row with column names', 'php-constants-manager'); ?></li>
            <li><?php _e('Start with a small test file to verify format', 'php-constants-manager'); ?></li>
            <li><?php _e('The import will show you exactly what was imported, skipped, and any errors', 'php-constants-manager'); ?></li>
            <li><?php _e('Existing constants with the same name are skipped (not overwritten)', 'php-constants-manager'); ?></li>
            <li><?php _e('Empty rows in your CSV file are automatically ignored', 'php-constants-manager'); ?></li>
            <li><?php _e('UTF-8 BOM (Byte Order Mark) is automatically detected and removed if present', 'php-constants-manager'); ?></li>
            <li><?php _e('Invalid constant names or types will be counted as errors and skipped', 'php-constants-manager'); ?></li>
        </ul>
        
        <div class="pcm-import-warning">
            <p><strong><?php _e('Important:', 'php-constants-manager'); ?></strong> <?php _e('The CSV import only adds constants to your plugin\'s database. Whether they actually take effect depends on load order and if they\'re already defined elsewhere (see <a href="#predefined-constants">Understanding Predefined Constants</a> above).', 'php-constants-manager'); ?></p>
        </div>
        
        <h3><?php _e('Import Error Reporting', 'php-constants-manager'); ?></h3>
        <p><?php _e('When importing CSV files, any errors will be displayed with specific details to help you fix your data:', 'php-constants-manager'); ?></p>
        <ul>
            <li><strong><?php _e('Line Numbers:', 'php-constants-manager'); ?></strong> <?php _e('Shows exactly which CSV line had the problem', 'php-constants-manager'); ?></li>
            <li><strong><?php _e('Specific Errors:', 'php-constants-manager'); ?></strong> <?php _e('Details what was wrong (invalid name, wrong type, missing columns)', 'php-constants-manager'); ?></li>
            <li><strong><?php _e('Value Examples:', 'php-constants-manager'); ?></strong> <?php _e('Shows the problematic value and suggests correct format', 'php-constants-manager'); ?></li>
        </ul>
        
        <p><strong><?php _e('Example Error Messages:', 'php-constants-manager'); ?></strong></p>
        <ul>
            <li><code>Line 5: Missing required columns (need at least Name, Value, Type)</code></li>
            <li><code>Line 8: Invalid constant name "my-constant" (must be uppercase letters, numbers, and underscores only)</code></li>
            <li><code>Line 12: Invalid boolean value "maybe" (Constant: DEBUG_MODE)</code></li>
            <li><code>Line 15: Invalid integer value "3.14" (Constant: MAX_ITEMS)</code></li>
        </ul>
        
        <h3><?php _e('Overwrite vs. Skip Existing Constants', 'php-constants-manager'); ?></h3>
        <p><?php _e('The import page includes an "Overwrite existing constants" checkbox that controls how duplicate constants are handled:', 'php-constants-manager'); ?></p>
        
        <h4><?php _e('Checkbox Unchecked (Default):', 'php-constants-manager'); ?></h4>
        <ul>
            <li><?php _e('Constants with matching names are skipped', 'php-constants-manager'); ?></li>
            <li><?php _e('Existing data is preserved unchanged', 'php-constants-manager'); ?></li>
            <li><?php _e('Only new constants (not in your database) are imported', 'php-constants-manager'); ?></li>
            <li><?php _e('Safe for adding new constants without affecting existing ones', 'php-constants-manager'); ?></li>
        </ul>
        
        <h4><?php _e('Checkbox Checked (Overwrite Mode):', 'php-constants-manager'); ?></h4>
        <ul>
            <li><?php _e('Constants with matching names are updated with CSV data', 'php-constants-manager'); ?></li>
            <li><?php _e('Value, type, status, and description are all updated', 'php-constants-manager'); ?></li>
            <li><?php _e('New constants are still imported normally', 'php-constants-manager'); ?></li>
            <li><?php _e('Useful for bulk updates or synchronizing data between environments', 'php-constants-manager'); ?></li>
        </ul>
        
        <div class="pcm-import-warning">
            <p><strong><?php _e('Warning:', 'php-constants-manager'); ?></strong> <?php _e('When overwrite mode is enabled, existing constant data will be replaced. Always backup your database before using overwrite mode.', 'php-constants-manager'); ?></p>
        </div>
        
        <h4><?php _e('Import Result Messages:', 'php-constants-manager'); ?></h4>
        <ul>
            <li><strong><?php _e('Imported:', 'php-constants-manager'); ?></strong> <?php _e('New constants added to the database', 'php-constants-manager'); ?></li>
            <li><strong><?php _e('Updated:', 'php-constants-manager'); ?></strong> <?php _e('Existing constants modified (only shown in overwrite mode)', 'php-constants-manager'); ?></li>
            <li><strong><?php _e('Skipped:', 'php-constants-manager'); ?></strong> <?php _e('Existing constants left unchanged (only shown in skip mode)', 'php-constants-manager'); ?></li>
            <li><strong><?php _e('Errors:', 'php-constants-manager'); ?></strong> <?php _e('Rows that could not be processed due to validation failures', 'php-constants-manager'); ?></li>
        </ul>
        
        <h2 id="value-validation"><?php _e('Value Validation', 'php-constants-manager'); ?></h2>
        <p><?php _e('The plugin validates that constant values match their selected type, both when creating/editing constants and during CSV import:', 'php-constants-manager'); ?></p>
        
        <h4><?php _e('Validation Rules', 'php-constants-manager'); ?></h4>
        <ul>
            <li><strong><?php _e('Integer:', 'php-constants-manager'); ?></strong> <?php _e('Must be whole numbers (42, -10, 0). Decimals like "3.14" are rejected.', 'php-constants-manager'); ?></li>
            <li><strong><?php _e('Float:', 'php-constants-manager'); ?></strong> <?php _e('Must be valid numbers (3.14, -2.5, 10). Text like "abc" is rejected.', 'php-constants-manager'); ?></li>
            <li><strong><?php _e('Boolean:', 'php-constants-manager'); ?></strong> <?php _e('Must be: true, false, 1, 0, yes, no, on, off (case-insensitive). Values like "maybe" are rejected.', 'php-constants-manager'); ?></li>
            <li><strong><?php _e('String:', 'php-constants-manager'); ?></strong> <?php _e('Any value is accepted, including quotes and special characters.', 'php-constants-manager'); ?></li>
            <li><strong><?php _e('NULL:', 'php-constants-manager'); ?></strong> <?php _e('Value field is disabled and automatically cleared.', 'php-constants-manager'); ?></li>
        </ul>
        
        <h4><?php _e('Real-time Validation', 'php-constants-manager'); ?></h4>
        <p><?php _e('When creating or editing constants, the form provides:', 'php-constants-manager'); ?></p>
        <ul>
            <li><?php _e('Immediate feedback as you type in the value field', 'php-constants-manager'); ?></li>
            <li><?php _e('Red borders and error messages for invalid values', 'php-constants-manager'); ?></li>
            <li><?php _e('Type-specific placeholder text and examples', 'php-constants-manager'); ?></li>
            <li><?php _e('Automatic validation when changing the type dropdown', 'php-constants-manager'); ?></li>
        </ul>
        
        <h2 id="common-constants"><?php _e('Common WordPress Constants', 'php-constants-manager'); ?></h2>
        <p><?php _e('Here are some constants you might see that are already defined by WordPress:', 'php-constants-manager'); ?></p>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Constant', 'php-constants-manager'); ?></th>
                    <th><?php _e('Description', 'php-constants-manager'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>WP_DEBUG</code></td>
                    <td><?php _e('Enables WordPress debug mode', 'php-constants-manager'); ?></td>
                </tr>
                <tr>
                    <td><code>WP_DEBUG_LOG</code></td>
                    <td><?php _e('Enables debug logging to file', 'php-constants-manager'); ?></td>
                </tr>
                <tr>
                    <td><code>ABSPATH</code></td>
                    <td><?php _e('Absolute path to WordPress directory', 'php-constants-manager'); ?></td>
                </tr>
                <tr>
                    <td><code>WP_CONTENT_DIR</code></td>
                    <td><?php _e('Path to wp-content directory', 'php-constants-manager'); ?></td>
                </tr>
                <tr>
                    <td><code>WP_CONTENT_URL</code></td>
                    <td><?php _e('URL to wp-content directory', 'php-constants-manager'); ?></td>
                </tr>
                <tr>
                    <td><code>DB_HOST</code></td>
                    <td><?php _e('Database server hostname', 'php-constants-manager'); ?></td>
                </tr>
                <tr>
                    <td><code>DB_NAME</code></td>
                    <td><?php _e('Database name', 'php-constants-manager'); ?></td>
                </tr>
            </tbody>
        </table>
        
        <h2 id="screen-options"><?php _e('Customizing Your View', 'php-constants-manager'); ?></h2>
        
        <h3><?php _e('Screen Options', 'php-constants-manager'); ?></h3>
        <p><?php _e('Both the "My Constants" and "All Constants" pages include a "Screen Options" button in the top-right corner that allows you to customize how the tables are displayed.', 'php-constants-manager'); ?></p>
        
        <h4><?php _e('Number of Items', 'php-constants-manager'); ?></h4>
        <p><?php _e('You can control how many constants are displayed per page. The default is 50, but you can choose from 5, 10, 20, 50, 100, or more. This is especially useful on the "All Constants" page which may show thousands of constants.', 'php-constants-manager'); ?></p>
        
        <h4><?php _e('Column Visibility', 'php-constants-manager'); ?></h4>
        <p><?php _e('You can show or hide specific columns in both tables:', 'php-constants-manager'); ?></p>
        <ul>
            <li><strong><?php _e('My Constants:', 'php-constants-manager'); ?></strong> <?php _e('Name, Value, Status, Predefined, Description, Created', 'php-constants-manager'); ?></li>
            <li><strong><?php _e('All Constants:', 'php-constants-manager'); ?></strong> <?php _e('Name, Value & Type, Category', 'php-constants-manager'); ?></li>
        </ul>
        <p><?php _e('When you hide columns, the remaining columns will automatically expand to fill the available space, making better use of your screen real estate.', 'php-constants-manager'); ?></p>
        
        <p><strong><?php _e('Tip:', 'php-constants-manager'); ?></strong> <?php _e('Your Screen Options preferences are saved automatically and will persist across page loads and browser sessions.', 'php-constants-manager'); ?></p>
        
        <h2 id="troubleshooting"><?php _e('Troubleshooting', 'php-constants-manager'); ?></h2>
        
        <h3><?php _e('My constant isn\'t working', 'php-constants-manager'); ?></h3>
        <ol>
            <li><?php _e('Check if it\'s marked as "Active"', 'php-constants-manager'); ?></li>
            <li><?php _e('Check if it shows "Predefined: Yes" (meaning something else defined it first)', 'php-constants-manager'); ?></li>
            <li><?php _e('Verify the constant name follows PHP naming rules', 'php-constants-manager'); ?></li>
            <li><?php _e('Check for PHP syntax errors in your constant value', 'php-constants-manager'); ?></li>
            <li><?php _e('Consider load order: if another plugin defines the same constant with higher priority (priority 0 or negative), it will override yours', 'php-constants-manager'); ?></li>
        </ol>
        
        <h3><?php _e('I see a warning about predefined constants', 'php-constants-manager'); ?></h3>
        <p><?php _e('This warning appears when you try to define a constant that\'s already defined elsewhere. Your definition is saved but won\'t take effect unless the original definition is removed.', 'php-constants-manager'); ?></p>
        
        <h3><?php _e('How to check if my constant is working', 'php-constants-manager'); ?></h3>
        <p><?php _e('You can test your constants in your theme or plugin code. Remember that your constants are available after the <code>plugins_loaded</code> action (priority 1), so test them in appropriate hooks:', 'php-constants-manager'); ?></p>
        <pre><code>// Test in theme functions.php or after plugins_loaded
add_action('init', function() {
    if (defined('MY_CONSTANT')) {
        echo 'MY_CONSTANT is defined with value: ' . MY_CONSTANT;
    } else {
        echo 'MY_CONSTANT is not defined';
    }
});

// Or test directly in template files (themes load after plugins)
if (defined('MY_CONSTANT')) {
    // Your constant is available here
}</code></pre>
        
        <h3><?php _e('Database table doesn\'t exist or can\'t be created', 'php-constants-manager'); ?></h3>
        <p><?php _e('If you see database errors, check the following:', 'php-constants-manager'); ?></p>
        <ol>
            <li><strong><?php _e('Database Permissions:', 'php-constants-manager'); ?></strong> <?php _e('Ensure your WordPress database user has CREATE TABLE privileges', 'php-constants-manager'); ?></li>
            <li><strong><?php _e('Disk Space:', 'php-constants-manager'); ?></strong> <?php _e('Check if your server has sufficient disk space', 'php-constants-manager'); ?></li>
            <li><strong><?php _e('MySQL Version:', 'php-constants-manager'); ?></strong> <?php _e('Verify MySQL 5.6+ or MariaDB 10.0+ compatibility', 'php-constants-manager'); ?></li>
            <li><strong><?php _e('Plugin Activation:', 'php-constants-manager'); ?></strong> <?php _e('Try deactivating and reactivating the plugin', 'php-constants-manager'); ?></li>
            <li><strong><?php _e('Manual Creation:', 'php-constants-manager'); ?></strong> <?php _e('Contact your hosting provider to manually create the table if needed', 'php-constants-manager'); ?></li>
        </ol>
        
        <h4><?php _e('Manual Table Creation SQL', 'php-constants-manager'); ?></h4>
        <p><?php _e('If automatic table creation fails, you can manually run this SQL in your database:', 'php-constants-manager'); ?></p>
        <pre><code>CREATE TABLE wp_pcm_constants (
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
        <p><strong><?php _e('Note:', 'php-constants-manager'); ?></strong> <?php _e('Replace "wp_" with your actual WordPress table prefix if different.', 'php-constants-manager'); ?></p>
        
        <h3><?php _e('Need More Help?', 'php-constants-manager'); ?></h3>
        <p><?php _e('Use the "All Constants" page to see every constant currently defined in your WordPress installation. This can help you understand what\'s already taken and avoid conflicts.', 'php-constants-manager'); ?></p>
        
    </div>
</div>

<style>
/* Table of Contents Styles */
.pcm-toc-container {
    background: #f8f9fa;
    border: 1px solid #e1e1e1;
    border-radius: 8px;
    padding: 24px;
    margin: 20px 0 40px 0;
}

.pcm-toc-container h2 {
    margin-top: 0 !important;
    margin-bottom: 20px;
    border-bottom: 2px solid #0073aa;
    padding-bottom: 8px;
    color: #0073aa;
}

.pcm-toc-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
}

.pcm-toc-column h3 {
    font-size: 16px;
    color: #1d2327;
    margin: 0 0 10px 0;
    padding-bottom: 5px;
    border-bottom: 1px solid #ddd;
}

.pcm-toc-column ul {
    margin: 0 0 25px 0;
    padding-left: 0;
    list-style: none;
}

.pcm-toc-column li {
    margin-bottom: 6px;
}

.pcm-toc-column a {
    color: #0073aa;
    text-decoration: none;
    padding: 4px 8px;
    display: block;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.pcm-toc-column a:hover {
    background: #e1f4ff;
    color: #005a87;
    text-decoration: none;
}

/* Smooth scrolling */
html {
    scroll-behavior: smooth;
}

/* Content sections */
.pcm-help-content h2 {
    border-bottom: 1px solid #ccd0d4;
    padding-bottom: 10px;
    margin-top: 40px;
    scroll-margin-top: 20px;
}

.pcm-help-content h2:first-of-type {
    margin-top: 20px;
}

.pcm-help-content h3 {
    color: #1d2327;
    margin-top: 20px;
    scroll-margin-top: 20px;
}

.pcm-help-content code {
    background: #f0f0f1;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: Consolas, Monaco, monospace;
    font-size: 13px;
}

.pcm-help-content pre {
    background: #f6f7f7;
    border: 1px solid #dcdcde;
    border-radius: 4px;
    padding: 15px;
    overflow-x: auto;
    margin: 15px 0;
}

.pcm-help-content pre code {
    background: none;
    padding: 0;
    font-size: 13px;
    line-height: 1.5;
}

.pcm-help-content ul, .pcm-help-content ol {
    margin-left: 20px;
}

.pcm-help-content li {
    margin-bottom: 8px;
    line-height: 1.5;
}

.pcm-help-content .wp-list-table {
    margin: 20px 0;
}

.pcm-help-content .wp-list-table td {
    padding: 12px;
    vertical-align: top;
}

.pcm-help-content .wp-list-table code {
    font-weight: 600;
    color: #1d2327;
}

/* Import warning box */
.pcm-import-warning {
    background: #fff8e1;
    border: 1px solid #f0c674;
    border-radius: 6px;
    padding: 16px;
    margin: 20px 0;
}

.pcm-import-warning p {
    margin: 0;
    color: #8b6914;
}

.pcm-import-warning a {
    color: #8b6914;
    text-decoration: underline;
}

/* Responsive design */
@media screen and (max-width: 782px) {
    .pcm-toc-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .pcm-toc-container {
        padding: 16px;
    }
    
    .pcm-help-content h2 {
        margin-top: 30px;
    }
}
</style>