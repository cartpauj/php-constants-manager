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
        
        <h2><?php _e('What are PHP Constants?', 'php-constants-manager'); ?></h2>
        <p><?php _e('PHP constants are identifiers for values that cannot be changed during script execution. Unlike variables, constants do not have a dollar sign ($) prefix and are typically written in UPPERCASE.', 'php-constants-manager'); ?></p>
        <p><?php _e('Examples: <code>SITE_URL</code>, <code>API_KEY</code>, <code>DEBUG_MODE</code>', 'php-constants-manager'); ?></p>
        
        <h2><?php _e('How to Use This Plugin', 'php-constants-manager'); ?></h2>
        <ol>
            <li><strong><?php _e('Add New Constants:', 'php-constants-manager'); ?></strong> <?php _e('Go to "My Constants" and click "Add New" to create a new constant.', 'php-constants-manager'); ?></li>
            <li><strong><?php _e('Manage Existing:', 'php-constants-manager'); ?></strong> <?php _e('View, edit, activate/deactivate, or delete your constants from the "My Constants" page.', 'php-constants-manager'); ?></li>
            <li><strong><?php _e('View All Constants:', 'php-constants-manager'); ?></strong> <?php _e('Use "All Constants" to see every constant defined in your WordPress installation.', 'php-constants-manager'); ?></li>
            <li><strong><?php _e('Customize View:', 'php-constants-manager'); ?></strong> <?php _e('Use the "Screen Options" button (top-right) to control how many items to display per page and which columns to show/hide.', 'php-constants-manager'); ?></li>
        </ol>
        
        <h2><?php _e('Constant Types', 'php-constants-manager'); ?></h2>
        
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
        
        <h2><?php _e('Understanding the "Predefined" Column', 'php-constants-manager'); ?></h2>
        
        <div class="notice notice-info" style="margin: 20px 0;">
            <p><strong><?php _e('Predefined: No', 'php-constants-manager'); ?></strong> - <?php _e('This constant is only defined by this plugin. You have full control over it.', 'php-constants-manager'); ?></p>
        </div>
        
        <div class="notice notice-warning" style="margin: 20px 0;">
            <p><strong><?php _e('Predefined: Yes', 'php-constants-manager'); ?></strong> - <?php _e('This constant is already defined by WordPress core, another plugin, or your theme. Your definition will only take effect if the original definition is removed.', 'php-constants-manager'); ?></p>
        </div>
        
        <h2><?php _e('Why Can\'t I Override Existing Constants?', 'php-constants-manager'); ?></h2>
        <p><?php _e('PHP constants cannot be redefined once they are set. This is a fundamental rule of PHP, not a limitation of this plugin.', 'php-constants-manager'); ?></p>
        
        <h3><?php _e('What Happens When:', 'php-constants-manager'); ?></h3>
        <ul>
            <li><strong><?php _e('Constant is Active + Not Predefined:', 'php-constants-manager'); ?></strong> <?php _e('Your constant is successfully defined and working.', 'php-constants-manager'); ?></li>
            <li><strong><?php _e('Constant is Active + Predefined:', 'php-constants-manager'); ?></strong> <?php _e('The predefined value takes precedence. Your value is stored but not used.', 'php-constants-manager'); ?></li>
            <li><strong><?php _e('Constant is Inactive:', 'php-constants-manager'); ?></strong> <?php _e('Your constant is not defined at all, regardless of predefined status.', 'php-constants-manager'); ?></li>
        </ul>
        
        <h2><?php _e('Load Order Matters', 'php-constants-manager'); ?></h2>
        <p><?php _e('This plugin defines your constants during the <code>plugins_loaded</code> action with priority 1, which means it loads very early in the WordPress loading process. However, some constants may already be defined by:', 'php-constants-manager'); ?></p>
        <ul>
            <li><?php _e('<strong>wp-config.php file</strong> - Loads before any plugins', 'php-constants-manager'); ?></li>
            <li><?php _e('<strong>WordPress core</strong> - Many constants defined during WordPress bootstrap', 'php-constants-manager'); ?></li>
            <li><?php _e('<strong>Must-use plugins (mu-plugins)</strong> - Load before regular plugins', 'php-constants-manager'); ?></li>
            <li><?php _e('<strong>PHP extensions</strong> - Built-in PHP constants', 'php-constants-manager'); ?></li>
            <li><?php _e('<strong>Other plugins with higher priority</strong> - Plugins using <code>plugins_loaded</code> with priority 0 or negative values', 'php-constants-manager'); ?></li>
        </ul>
        
        <div class="notice notice-info" style="margin: 20px 0;">
            <p><strong><?php _e('Technical Note:', 'php-constants-manager'); ?></strong> <?php _e('Your constants are defined during <code>plugins_loaded</code> priority 1, which means they are available to:', 'php-constants-manager'); ?></p>
            <ul style="margin-left: 20px;">
                <li><?php _e('All theme code (themes load after plugins)', 'php-constants-manager'); ?></li>
                <li><?php _e('Most other plugins (unless they use higher priority)', 'php-constants-manager'); ?></li>
                <li><?php _e('WordPress hooks like <code>init</code>, <code>wp_loaded</code>, etc.', 'php-constants-manager'); ?></li>
            </ul>
        </div>
        
        <h2><?php _e('Best Practices', 'php-constants-manager'); ?></h2>
        
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
        
        <h2><?php _e('Common WordPress Constants', 'php-constants-manager'); ?></h2>
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
        
        <h2><?php _e('Troubleshooting', 'php-constants-manager'); ?></h2>
        
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
        
        <h2><?php _e('Import/Export Constants', 'php-constants-manager'); ?></h2>
        
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
            <li><?php _e('Skip constants that already exist (no duplicates)', 'php-constants-manager'); ?></li>
            <li><?php _e('Validate constant names and data types', 'php-constants-manager'); ?></li>
            <li><?php _e('Provide detailed feedback on imported, skipped, and error counts', 'php-constants-manager'); ?></li>
            <li><?php _e('Handle both header and non-header CSV files automatically', 'php-constants-manager'); ?></li>
        </ul>
        
        <h3><?php _e('CSV Format Requirements', 'php-constants-manager'); ?></h3>
        
        <div class="notice notice-info" style="margin: 20px 0;">
            <p><strong><?php _e('Required Columns (minimum):', 'php-constants-manager'); ?></strong> <?php _e('Name, Value, Type', 'php-constants-manager'); ?></p>
            <p><strong><?php _e('Optional Columns:', 'php-constants-manager'); ?></strong> <?php _e('Active, Description', 'php-constants-manager'); ?></p>
        </div>
        
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
        
        <p><strong><?php _e('With Headers (Recommended):', 'php-constants-manager'); ?></strong></p>
        <pre><code>Name,Value,Type,Active,Description
MY_API_KEY,abc123def456,string,1,External API authentication key
MAX_POSTS,25,integer,1,Maximum posts per page
TAX_RATE,0.08,float,1,Sales tax rate
DEBUG_MODE,true,boolean,0,Enable debug output
OPTIONAL_SETTING,,null,1,Optional configuration value</code></pre>
        
        <p><strong><?php _e('Without Headers (Minimum Required Columns):', 'php-constants-manager'); ?></strong></p>
        <pre><code>MY_API_KEY,abc123def456,string
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
            <li><?php _e('Start with a small test file to verify format', 'php-constants-manager'); ?></li>
            <li><?php _e('The import will show you exactly what was imported, skipped, and any errors', 'php-constants-manager'); ?></li>
            <li><?php _e('Existing constants with the same name are skipped (not overwritten)', 'php-constants-manager'); ?></li>
            <li><?php _e('Empty rows in your CSV file are automatically ignored', 'php-constants-manager'); ?></li>
            <li><?php _e('Invalid constant names or types will be counted as errors and skipped', 'php-constants-manager'); ?></li>
        </ul>
        
        <div class="notice notice-warning" style="margin: 20px 0;">
            <p><strong><?php _e('Important:', 'php-constants-manager'); ?></strong> <?php _e('The CSV import only adds constants to your plugin\'s database. Whether they actually take effect depends on load order and if they\'re already defined elsewhere (see "Understanding Predefined Constants" above).', 'php-constants-manager'); ?></p>
        </div>
        
        <h2><?php _e('Customizing Your View', 'php-constants-manager'); ?></h2>
        
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
        
        <div class="notice notice-info" style="margin: 20px 0;">
            <p><strong><?php _e('Tip:', 'php-constants-manager'); ?></strong> <?php _e('Your Screen Options preferences are saved automatically and will persist across page loads and browser sessions.', 'php-constants-manager'); ?></p>
        </div>
        
        <div class="notice notice-info" style="margin: 20px 0;">
            <h3><?php _e('Need More Help?', 'php-constants-manager'); ?></h3>
            <p><?php _e('Use the "All Constants" page to see every constant currently defined in your WordPress installation. This can help you understand what\'s already taken and avoid conflicts.', 'php-constants-manager'); ?></p>
        </div>
        
    </div>
</div>

<style>
.pcm-help-content h2 {
    border-bottom: 1px solid #ccd0d4;
    padding-bottom: 10px;
    margin-top: 30px;
}

.pcm-help-content h3 {
    color: #1d2327;
    margin-top: 20px;
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
</style>