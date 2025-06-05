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
        
        // Auto-convert to uppercase (only for non-readonly fields)
        if (!$input.prop('readonly')) {
            var cursorPos = $input[0].selectionStart;
            var upperValue = value.toUpperCase();
            
            if (value !== upperValue) {
                $input.val(upperValue);
                // Restore cursor position
                $input[0].setSelectionRange(cursorPos, cursorPos);
                value = upperValue;
            }
        }
        
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
        var $valueWrapper = $valueField.closest('.pcm-input-wrapper');
        var $lockIcon = $valueWrapper.find('.pcm-input-icon');
        
        if (type === 'null') {
            // Clear and disable the value field
            $valueField.val('').prop('disabled', true).addClass('pcm-input-disabled');
            
            // Add lock icon if it doesn't exist
            if ($lockIcon.length === 0) {
                $valueWrapper.append('<div class="pcm-input-icon">🔒</div>');
            }
            
            // Update placeholder
            $valueField.attr('placeholder', 'NULL values cannot be edited');
        } else {
            // Enable the value field
            $valueField.prop('disabled', false).removeClass('pcm-input-disabled');
            
            // Remove lock icon
            $lockIcon.remove();
            
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
                case 'string':
                    $valueField.attr('placeholder', 'Enter constant value');
                    break;
                default:
                    $valueField.attr('placeholder', 'Enter constant value');
            }
        }
        
        // Re-validate current value when type changes
        $valueField.trigger('input');
    }).trigger('change');
    
    // Value field validation based on type
    $('#constant-value').on('input blur', function() {
        var $valueField = $(this);
        var value = $valueField.val();
        var type = $('#constant-type').val();
        var errorMessage = '';
        
        // Skip validation if field is disabled (null type)
        if ($valueField.prop('disabled')) {
            return;
        }
        
        // Skip validation if value is empty (allowed for most types)
        if (value.trim() === '') {
            $valueField[0].setCustomValidity('');
            return;
        }
        
        // Validate based on type
        switch (type) {
            case 'integer':
                if (!/^-?\d+$/.test(value.trim())) {
                    errorMessage = 'Please enter a valid integer (e.g., 42, -10, 0)';
                }
                break;
                
            case 'float':
                if (!/^-?\d*\.?\d+$/.test(value.trim())) {
                    errorMessage = 'Please enter a valid number (e.g., 3.14, -2.5, 10)';
                }
                break;
                
            case 'boolean':
                var lowerValue = value.trim().toLowerCase();
                var validValues = ['true', 'false', '1', '0', 'yes', 'no', 'on', 'off'];
                if (!validValues.includes(lowerValue)) {
                    errorMessage = 'Please enter: true, false, 1, 0, yes, no, on, or off';
                }
                break;
                
            case 'string':
                // Strings are always valid
                break;
        }
        
        // Set custom validity
        $valueField[0].setCustomValidity(errorMessage);
        
        // Visual feedback
        if (errorMessage) {
            $valueField.addClass('pcm-input-error');
        } else {
            $valueField.removeClass('pcm-input-error');
        }
    });
    
    // Auto-select all text in value field when focused
    $('#constant-value').on('focus', function() {
        $(this).select();
    });
    
    // Add visual feedback for form submission
    $('.pcm-form').on('submit', function() {
        var $submitBtn = $(this).find('button[type="submit"]');
        $submitBtn.prop('disabled', true).addClass('updating-message');
    });
    
    // Import/Export functionality
    $('#csv_file').on('change', function() {
        var $input = $(this);
        var $label = $input.siblings('.pcm-file-label');
        var $selected = $input.siblings('.pcm-file-selected');
        var $submitBtn = $('#import-submit-btn');
        
        if (this.files && this.files[0]) {
            var file = this.files[0];
            var fileName = file.name;
            
            // Validate file type
            if (!fileName.toLowerCase().endsWith('.csv')) {
                alert('Please select a CSV file.');
                $input.val('');
                return;
            }
            
            // Show selected file
            $label.hide();
            $selected.find('.pcm-file-name').text(fileName);
            $selected.show();
            $submitBtn.prop('disabled', false);
        } else {
            // No file selected
            $label.show();
            $selected.hide();
            $submitBtn.prop('disabled', true);
        }
    });
    
    // Remove selected file
    $('.pcm-file-remove').on('click', function() {
        var $input = $('#csv_file');
        var $label = $input.siblings('.pcm-file-label');
        var $selected = $input.siblings('.pcm-file-selected');
        var $submitBtn = $('#import-submit-btn');
        
        $input.val('');
        $label.show();
        $selected.hide();
        $submitBtn.prop('disabled', true);
    });
    
    // Drag and drop functionality
    $('.pcm-file-label').on('dragover dragenter', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass('drag-over');
    });
    
    $('.pcm-file-label').on('dragleave dragend', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('drag-over');
    });
    
    $('.pcm-file-label').on('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('drag-over');
        
        var files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            $('#csv_file')[0].files = files;
            $('#csv_file').trigger('change');
        }
    });
    
    // Handle toggle switch changes
    $(document).on('change', '.pcm-toggle-switch input[type="checkbox"]', function() {
        var $toggle = $(this).closest('.pcm-toggle-switch');
        var id = $toggle.data('id');
        var nonce = $toggle.data('nonce');
        var $checkbox = $(this);
        
        // Disable the toggle while processing
        $checkbox.prop('disabled', true);
        $toggle.addClass('pcm-toggle-loading');
        
        $.ajax({
            url: pcm_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'pcm_toggle_constant',
                id: id,
                nonce: nonce
            },
            success: function(response) {
                if (response.success) {
                    // Update checkbox state
                    $checkbox.prop('checked', response.data.new_status);
                    
                    // Show brief success feedback
                    $toggle.removeClass('pcm-toggle-loading').addClass('pcm-toggle-success');
                    setTimeout(function() {
                        $toggle.removeClass('pcm-toggle-success');
                    }, 1000);
                } else {
                    // Revert checkbox state on error
                    $checkbox.prop('checked', !$checkbox.prop('checked'));
                    alert(response.data || 'An error occurred while toggling the constant.');
                }
            },
            error: function() {
                // Revert checkbox state on error
                $checkbox.prop('checked', !$checkbox.prop('checked'));
                alert('An error occurred while toggling the constant.');
            },
            complete: function() {
                // Re-enable the toggle
                $checkbox.prop('disabled', false);
                $toggle.removeClass('pcm-toggle-loading');
            }
        });
    });
});