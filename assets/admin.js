jQuery(document).ready(function($) {
    'use strict';
    
    // Confirm bulk delete
    $('form').on('submit', function(e) {
        var $form = $(this);
        var action = $form.find('select[name="action"]').val();
        
        if (action === '-1') {
            action = $form.find('select[name="action2"]').val();
        }
        
        if (action === 'delete') {
            var checked = $form.find('input[name="constant[]"]:checked').length;
            
            if (checked > 0) {
                if (!confirm(pcm_ajax.confirm_bulk_delete)) {
                    e.preventDefault();
                    return false;
                }
            }
        }
    });
    
    // Add validation to constant name field
    $('#constant-name').on('input', function() {
        var $input = $(this);
        var value = $input.val();
        var isValid = /^[A-Z][A-Z0-9_]*$/.test(value);
        var $feedback = $('#constant-name-feedback');
        
        if (value && !isValid) {
            $input[0].setCustomValidity('Constant name must start with an uppercase letter and contain only uppercase letters, numbers, and underscores.');
            $feedback.html('<span style="color: #d63638;">' + 'Invalid format. Use uppercase letters, numbers, and underscores only.' + '</span>');
        } else {
            $input[0].setCustomValidity('');
            $feedback.empty();
            
            // Check if constant already exists (only for new constants)
            if (value && isValid && !$input.prop('readonly')) {
                checkExistingConstant(value);
            }
        }
    });
    
    // Check if constant already exists
    function checkExistingConstant(name) {
        var $feedback = $('#constant-name-feedback');
        
        // List of common WordPress constants to check
        var commonConstants = [
            'ABSPATH', 'WP_DEBUG', 'WP_DEBUG_LOG', 'WP_DEBUG_DISPLAY', 
            'WP_CONTENT_DIR', 'WP_CONTENT_URL', 'WP_PLUGIN_DIR', 'WP_PLUGIN_URL',
            'WPINC', 'WP_LANG_DIR', 'WP_MEMORY_LIMIT', 'WP_MAX_MEMORY_LIMIT',
            'DB_NAME', 'DB_USER', 'DB_PASSWORD', 'DB_HOST', 'DB_CHARSET', 'DB_COLLATE',
            'AUTH_KEY', 'SECURE_AUTH_KEY', 'LOGGED_IN_KEY', 'NONCE_KEY',
            'AUTH_SALT', 'SECURE_AUTH_SALT', 'LOGGED_IN_SALT', 'NONCE_SALT'
        ];
        
        if (commonConstants.indexOf(name) !== -1) {
            $feedback.html('<span style="color: #996800;">⚠ This is a WordPress core constant. You can still add it to manage when WordPress is not defining it.</span>');
        }
    }
    
    // Type field change handler
    $('#constant-type').on('change', function() {
        var type = $(this).val();
        var $valueField = $('#constant-value');
        var $valueRow = $valueField.closest('tr');
        
        if (type === 'null') {
            $valueRow.hide();
            $valueField.val('');
        } else {
            $valueRow.show();
            
            // Add placeholder based on type
            switch (type) {
                case 'boolean':
                    $valueField.attr('placeholder', 'true or false');
                    break;
                case 'integer':
                    $valueField.attr('placeholder', 'e.g., 123');
                    break;
                case 'float':
                    $valueField.attr('placeholder', 'e.g., 123.45');
                    break;
                default:
                    $valueField.attr('placeholder', '');
            }
        }
    }).trigger('change');
    
    // Auto-select all text in value field when focused
    $('#constant-value').on('focus', function() {
        $(this).select();
    });
    
    // Add visual feedback for form submission
    $('.pcm-form').on('submit', function() {
        var $submitBtn = $(this).find('button[type="submit"]');
        $submitBtn.prop('disabled', true).addClass('updating-message');
    });
});