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
    
    // Debounce timer for AJAX requests
    var checkConstantTimer = null;
    
    // Check if constant already exists via AJAX
    function checkExistingConstant(name) {
        var $feedback = $('#constant-name-feedback');
        
        // Clear any existing timer
        if (checkConstantTimer) {
            clearTimeout(checkConstantTimer);
        }
        
        // Show loading indicator
        $feedback.html('<span style="color: #666;"><em>Checking...</em></span>');
        
        // Debounce the AJAX request
        checkConstantTimer = setTimeout(function() {
            $.ajax({
                url: pcm_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'pcm_check_constant',
                    constant_name: name,
                    nonce: pcm_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        if (response.data.is_defined) {
                            var value = response.data.value;
                            var displayValue = typeof value === 'string' ? '"' + value + '"' : String(value);
                            $feedback.html('<span style="color: #996800;">⚠ This constant is already defined with value: <code>' + displayValue + '</code>. You can still add it to manage when it\'s not predefined.</span>');
                        } else {
                            $feedback.html('<span style="color: #046b00;">✓ This constant is available.</span>');
                        }
                    } else {
                        $feedback.html('<span style="color: #d63638;">Error checking constant.</span>');
                    }
                },
                error: function() {
                    $feedback.html('<span style="color: #d63638;">Error checking constant.</span>');
                }
            });
        }, 2000); // 2 second debounce
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