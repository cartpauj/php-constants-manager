<?php
/**
 * Import/Export page view
 * 
 * Data available via $data array:
 * - message: string
 * - error: string
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php esc_html_e('Import/Export Constants', 'php-constants-manager'); ?></h1>
    
    <?php if (!empty($data['message'])): ?>
        <div class="notice notice-success is-dismissible">
            <p><?php echo esc_html(urldecode($data['message'])); ?></p>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($data['error'])): ?>
        <div class="notice notice-error is-dismissible">
            <p>
                <?php
                switch ($data['error']) {
                    case 'no_constants':
                        esc_html_e('No constants found to export.', 'php-constants-manager');
                        break;
                    case 'no_file':
                        esc_html_e('No file was uploaded.', 'php-constants-manager');
                        break;
                    case 'invalid_file':
                        esc_html_e('Invalid file type. Only CSV files are allowed.', 'php-constants-manager');
                        break;
                    case 'read_error':
                        esc_html_e('Error reading the uploaded file.', 'php-constants-manager');
                        break;
                    case 'empty_file':
                        esc_html_e('The uploaded CSV file is empty or contains no data.', 'php-constants-manager');
                        break;
                    case 'missing_header':
                        esc_html_e('CSV file must include a header row. The first column should be "Name" or "Constant Name".', 'php-constants-manager');
                        break;
                    case 'invalid_header':
                        esc_html_e('CSV header row must have at least 3 columns: Name, Value, Type.', 'php-constants-manager');
                        break;
                    default:
                        echo esc_html($data['error']);
                }
                ?>
            </p>
        </div>
    <?php endif; ?>
    
    <?php
    // Display import error details if available
    $import_errors = get_transient('pcm_import_errors');
    if ($import_errors && !empty($import_errors)) {
        delete_transient('pcm_import_errors');
        ?>
        <div class="notice notice-error">
            <p><strong><?php esc_html_e('Import Errors:', 'php-constants-manager'); ?></strong></p>
            <p><?php esc_html_e('The following rows had errors and were not imported:', 'php-constants-manager'); ?></p>
            <ul style="margin-left: 20px; margin-top: 10px;">
                <?php 
                // Limit to first 10 errors to avoid overwhelming display
                $display_errors = array_slice($import_errors, 0, 10);
                foreach ($display_errors as $error): 
                ?>
                    <li><?php echo esc_html($error); ?></li>
                <?php endforeach; ?>
            </ul>
            <?php if (count($import_errors) > 10): ?>
                <p><em><?php
                    /* translators: %d: number of additional errors beyond the displayed 10 */
                    printf(esc_html__('... and %d more errors.', 'php-constants-manager'), count($import_errors) - 10);
                ?></em></p>
            <?php endif; ?>
        </div>
        <?php
    }
    ?>
    
    <div class="pcm-import-export-container">
        <div class="pcm-import-export-grid">
            <!-- Export Section -->
            <div class="pcm-card">
                <div class="pcm-card-header">
                    <h2><?php esc_html_e('Export Constants', 'php-constants-manager'); ?></h2>
                    <p class="description"><?php esc_html_e('Download all your constants as a CSV file for backup or migration purposes.', 'php-constants-manager'); ?></p>
                </div>
                <div class="pcm-card-body">
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="pcm-export-form">
                        <input type="hidden" name="action" value="pcm_export_csv" />
                        <?php wp_nonce_field('pcm_export_csv', 'pcm_nonce'); ?>
                        
                        <div class="pcm-export-info">
                            <div class="pcm-info-item">
                                <span class="pcm-info-icon">📄</span>
                                <div class="pcm-info-content">
                                    <strong><?php esc_html_e('Format:', 'php-constants-manager'); ?></strong>
                                    <span><?php esc_html_e('CSV (Comma Separated Values)', 'php-constants-manager'); ?></span>
                                </div>
                            </div>
                            <div class="pcm-info-item">
                                <span class="pcm-info-icon">📊</span>
                                <div class="pcm-info-content">
                                    <strong><?php esc_html_e('Includes:', 'php-constants-manager'); ?></strong>
                                    <span><?php esc_html_e('Name, Value, Type, Status, Description', 'php-constants-manager'); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="pcm-btn pcm-btn-primary">
                            <span class="pcm-btn-icon">⬇️</span>
                            <?php esc_html_e('Export Constants', 'php-constants-manager'); ?>
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Import Section -->
            <div class="pcm-card">
                <div class="pcm-card-header">
                    <h2><?php esc_html_e('Import Constants', 'php-constants-manager'); ?></h2>
                    <p class="description"><?php esc_html_e('Upload a CSV file to import constants. Choose whether to skip or overwrite existing constants.', 'php-constants-manager'); ?></p>
                </div>
                <div class="pcm-card-body">
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data" class="pcm-import-form">
                        <input type="hidden" name="action" value="pcm_import_csv" />
                        <?php wp_nonce_field('pcm_import_csv', 'pcm_nonce'); ?>
                        
                        <div class="pcm-file-upload-area">
                            <input type="file" id="csv_file" name="csv_file" accept=".csv" required class="pcm-file-input" />
                            <label for="csv_file" class="pcm-file-label">
                                <span class="pcm-file-icon">📁</span>
                                <span class="pcm-file-text">
                                    <strong><?php esc_html_e('Choose CSV file', 'php-constants-manager'); ?></strong>
                                    <span><?php esc_html_e('or drag and drop', 'php-constants-manager'); ?></span>
                                </span>
                            </label>
                            <div class="pcm-file-selected" style="display: none;">
                                <span class="pcm-file-name"></span>
                                <button type="button" class="pcm-file-remove">×</button>
                            </div>
                        </div>
                        
                        <div class="pcm-import-requirements">
                            <h4><?php esc_html_e('CSV Format Requirements:', 'php-constants-manager'); ?></h4>
                            <ul>
                                <li><?php esc_html_e('First row can be headers (optional): Name, Value, Type, Active, Description', 'php-constants-manager'); ?></li>
                                <li><?php esc_html_e('Minimum columns: Name, Value, Type', 'php-constants-manager'); ?></li>
                                <li><?php esc_html_e('Constant names must be uppercase with underscores (A-Z, 0-9, _)', 'php-constants-manager'); ?></li>
                                <li><?php esc_html_e('Valid types: string, integer, float, boolean, null', 'php-constants-manager'); ?></li>
                                <li><?php esc_html_e('Active column: 1 for active, 0 for inactive (default: 1)', 'php-constants-manager'); ?></li>
                            </ul>
                        </div>
                        
                        <div class="pcm-import-options">
                            <label class="pcm-checkbox-label">
                                <input type="checkbox" name="overwrite_existing" value="1" class="pcm-checkbox">
                                <span class="pcm-checkbox-custom"></span>
                                <span class="pcm-checkbox-text">
                                    <strong><?php esc_html_e('Overwrite existing constants', 'php-constants-manager'); ?></strong>
                                    <br>
                                    <small class="description"><?php esc_html_e('If checked, constants with matching names will be updated. If unchecked, they will be skipped.', 'php-constants-manager'); ?></small>
                                </span>
                            </label>
                        </div>
                        
                        <button type="submit" class="pcm-btn pcm-btn-primary" disabled id="import-submit-btn">
                            <span class="pcm-btn-icon">⬆️</span>
                            <?php esc_html_e('Import Constants', 'php-constants-manager'); ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>