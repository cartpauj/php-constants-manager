<?php
/**
 * Constant add/edit form view
 * 
 * Data available via $data array:
 * - constant: object|null
 * - title: string
 * - is_edit: bool
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php echo esc_html($data['title']); ?></h1>
    
    <?php
    // Display transient notices
    $transient_notice = get_transient('phpcm_admin_notice');
    if ($transient_notice) {
        delete_transient('phpcm_admin_notice');
        $notice_class = $transient_notice['type'] === 'error' ? 'notice-error' : 'notice-warning';
        ?>
        <div class="notice <?php echo esc_attr($notice_class); ?> is-dismissible">
            <p><?php echo wp_kses($transient_notice['message'], array('a' => array('href' => array()), 'code' => array())); ?></p>
        </div>
        <?php
    }
    
    // Check if constant is already defined (for edit mode)
    if ($data['is_edit']) {
        $plugin_instance = PHP_Constants_Manager::get_instance();
        $predefined_check = $plugin_instance->is_constant_predefined(
            $data['constant']->name, 
            $data['constant']->value, 
            $data['constant']->type, 
            $data['constant']->is_active
        );
        
        if ($predefined_check['is_predefined']) {
            ?>
            <div class="notice notice-warning">
                <p><?php 
                    echo wp_kses(
                        sprintf(
                            /* translators: 1: constant name, 2: current value of the constant */
                            __('Note: The constant "%1$s" is currently defined with value: %2$s. Changes will only take effect if this predefined constant is removed.', 'php-constants-manager'),
                            esc_html($data['constant']->name),
                            '<code>' . phpcm_format_constant_value($predefined_check['existing_value']) . '</code>'
                        ),
                        array('code' => array())
                    ); 
                ?></p>
            </div>
            <?php
        }
    }
    ?>
    
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="phpcm-form phpcm-modern-form">
        <input type="hidden" name="action" value="phpcm_save_constant" />
        <?php if ($data['is_edit']): ?>
            <input type="hidden" name="id" value="<?php echo esc_attr($data['constant']->id); ?>" />
        <?php endif; ?>
        <?php wp_nonce_field('phpcm_save_constant', 'phpcm_nonce'); ?>
        
        <div class="phpcm-form-container">
            <div class="phpcm-form-grid">
                <div class="phpcm-form-group phpcm-form-group-full">
                    <label for="constant-name" class="phpcm-form-label">
                        <span class="phpcm-label-text"><?php esc_html_e('Constant Name', 'php-constants-manager'); ?></span>
                        <span class="phpcm-required">*</span>
                    </label>
                    <div class="phpcm-input-wrapper">
                        <input type="text" id="constant-name" name="constant_name" class="phpcm-input" 
                               value="<?php echo $data['is_edit'] ? esc_attr($data['constant']->name) : ''; ?>" 
                               required pattern="[A-Z][A-Z0-9_]*" 
                               placeholder="MY_CONSTANT_NAME"
                               <?php echo $data['is_edit'] ? esc_attr('readonly') : ''; ?> />
                        <?php if ($data['is_edit']): ?>
                            <div class="phpcm-input-icon">🔒</div>
                        <?php endif; ?>
                    </div>
                    <p class="phpcm-help-text"><?php esc_html_e('Use uppercase letters, numbers, and underscores only. Must start with a letter.', 'php-constants-manager'); ?></p>
                    <?php if ($data['is_edit']): ?>
                        <p class="phpcm-help-text phpcm-help-warning"><?php esc_html_e('Note: Constant names cannot be changed after creation.', 'php-constants-manager'); ?></p>
                    <?php endif; ?>
                    <div id="constant-name-feedback"></div>
                </div>

                <div class="phpcm-form-group phpcm-form-group-full">
                    <label for="constant-value" class="phpcm-form-label">
                        <span class="phpcm-label-text"><?php esc_html_e('Value', 'php-constants-manager'); ?></span>
                    </label>
                    <div class="phpcm-input-wrapper">
                        <input type="text" id="constant-value" name="constant_value" class="phpcm-input" 
                               value="<?php echo $data['is_edit'] ? esc_attr($data['constant']->value) : ''; ?>" 
                               placeholder="Enter constant value" />
                    </div>
                </div>

                <div class="phpcm-form-group phpcm-form-group-full">
                    <label for="constant-type" class="phpcm-form-label">
                        <span class="phpcm-label-text"><?php esc_html_e('Type', 'php-constants-manager'); ?></span>
                    </label>
                    <div class="phpcm-select-wrapper">
                        <select id="constant-type" name="constant_type" class="phpcm-select">
                            <option value="string" <?php selected($data['is_edit'] ? $data['constant']->type : '', 'string'); ?>><?php esc_html_e('String', 'php-constants-manager'); ?></option>
                            <option value="integer" <?php selected($data['is_edit'] ? $data['constant']->type : '', 'integer'); ?>><?php esc_html_e('Integer', 'php-constants-manager'); ?></option>
                            <option value="float" <?php selected($data['is_edit'] ? $data['constant']->type : '', 'float'); ?>><?php esc_html_e('Float', 'php-constants-manager'); ?></option>
                            <option value="boolean" <?php selected($data['is_edit'] ? $data['constant']->type : '', 'boolean'); ?>><?php esc_html_e('Boolean', 'php-constants-manager'); ?></option>
                            <option value="null" <?php selected($data['is_edit'] ? $data['constant']->type : '', 'null'); ?>><?php esc_html_e('NULL', 'php-constants-manager'); ?></option>
                        </select>
                    </div>
                </div>

                <div class="phpcm-form-group phpcm-form-group-full">
                    <label for="constant-active" class="phpcm-form-label">
                        <span class="phpcm-label-text"><?php esc_html_e('Status', 'php-constants-manager'); ?></span>
                    </label>
                    <div class="phpcm-checkbox-wrapper">
                        <label class="phpcm-checkbox-label">
                            <input type="checkbox" id="constant-active" name="constant_active" value="1" 
                                   class="phpcm-checkbox"
                                   <?php checked($data['is_edit'] ? $data['constant']->is_active : true, true); ?> />
                            <span class="phpcm-checkbox-custom"></span>
                            <span class="phpcm-checkbox-text"><?php esc_html_e('Active', 'php-constants-manager'); ?></span>
                        </label>
                        <p class="phpcm-help-text"><?php esc_html_e('Only active constants are loaded.', 'php-constants-manager'); ?></p>
                    </div>
                </div>

                <div class="phpcm-form-group phpcm-form-group-full">
                    <label for="constant-description" class="phpcm-form-label">
                        <span class="phpcm-label-text"><?php esc_html_e('Description', 'php-constants-manager'); ?></span>
                    </label>
                    <div class="phpcm-textarea-wrapper">
                        <textarea id="constant-description" name="constant_description" rows="3" class="phpcm-textarea" 
                                  placeholder="Optional: Describe what this constant is used for"><?php 
                            echo $data['is_edit'] ? esc_textarea($data['constant']->description) : ''; 
                        ?></textarea>
                    </div>
                    <p class="phpcm-help-text"><?php esc_html_e('Optional: Describe what this constant is used for.', 'php-constants-manager'); ?></p>
                </div>
            </div>
        </div>
        
        <div class="phpcm-form-actions">
            <button type="submit" class="phpcm-btn phpcm-btn-primary">
                <?php if ($data['is_edit']): ?>
                    <span class="phpcm-btn-icon">💾</span>
                    <?php esc_html_e('Update Constant', 'php-constants-manager'); ?>
                <?php else: ?>
                    <span class="phpcm-btn-icon">➕</span>
                    <?php esc_html_e('Add Constant', 'php-constants-manager'); ?>
                <?php endif; ?>
            </button>
            <a href="<?php echo esc_url(admin_url('admin.php?page=php-constants-manager')); ?>" class="phpcm-btn phpcm-btn-secondary">
                <span class="phpcm-btn-icon">✖️</span>
                <?php esc_html_e('Cancel', 'php-constants-manager'); ?>
            </a>
        </div>
    </form>
</div>