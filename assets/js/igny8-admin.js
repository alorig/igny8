/**
 * Igny8 Admin JavaScript
 * 
 * This file contains additional JavaScript functionality for the admin settings page.
 * The main functionality is handled in the inline script, but this file can be used
 * for more complex interactions, external libraries, or additional features.
 * 
 * @version 2.2
 * @package Igny8
 */

(function($) {
    'use strict';

    /**
     * Initialize admin functionality when DOM is ready
     */
    $(document).ready(function() {
        
        //==================================================
        // ## FIELD MODE TOGGLE FUNCTIONALITY
        //==================================================
        
        /**
         * Handle field mode changes (dynamic vs fixed)
         * Shows/hides the fixed fields configuration table
         */
        function handleFieldModeChange() {
            const fieldMode = $('select[name="igny8_field_mode"]');
            const fixedFieldsRow = $('#igny8-fixed-fields-row');
            
            if (fieldMode.length && fixedFieldsRow.length) {
                fieldMode.on('change', function() {
                    if ($(this).val() === 'fixed') {
                        fixedFieldsRow.show();
                    } else {
                        fixedFieldsRow.hide();
                    }
                });
                
                // Trigger on page load
                fieldMode.trigger('change');
            }
        }

        //==================================================
        // ## COLOR PICKER ENHANCEMENTS
        //==================================================
        
        /**
         * Initialize color pickers for color fields
         * Enhances the color input fields with better UX
         */
        function initColorPickers() {
            const colorInputs = $('input[name="igny8_button_color"], input[name="igny8_content_bg"]');
            
            colorInputs.each(function() {
                const $input = $(this);
                const $wrapper = $('<div class="igny8-color-wrapper"></div>');
                const $preview = $('<div class="igny8-color-preview"></div>');
                
                $wrapper.insertAfter($input);
                $input.appendTo($wrapper);
                $preview.appendTo($wrapper);
                
                // Update preview on input change
                $input.on('input', function() {
                    updateColorPreview($(this), $preview);
                });
                
                // Initial preview
                updateColorPreview($input, $preview);
            });
        }
        
        /**
         * Update color preview element
         */
        function updateColorPreview($input, $preview) {
            const color = $input.val();
            if (color && /^#[0-9A-F]{6}$/i.test(color)) {
                $preview.css({
                    'background-color': color,
                    'border': '1px solid #ddd',
                    'width': '30px',
                    'height': '30px',
                    'display': 'inline-block',
                    'margin-left': '10px',
                    'vertical-align': 'middle'
                });
            } else {
                $preview.css('display', 'none');
            }
        }

        //==================================================
        // ## FORM VALIDATION ENHANCEMENTS
        //==================================================
        
        /**
         * Enhanced form validation
         * Provides real-time validation feedback
         */
        function initFormValidation() {
            const form = $('form');
            const apiKeyInput = $('input[name="igny8_api_key"]');
            
            // API Key validation
            if (apiKeyInput.length) {
                apiKeyInput.on('blur', function() {
                    const value = $(this).val().trim();
                    if (value && !value.startsWith('sk-')) {
                        showFieldError($(this), 'API key should start with "sk-"');
                    } else {
                        clearFieldError($(this));
                    }
                });
            }
            
            // Required field validation
            form.find('input[required], textarea[required]').on('blur', function() {
                const $field = $(this);
                const value = $field.val().trim();
                
                if (!value) {
                    showFieldError($field, 'This field is required');
                } else {
                    clearFieldError($field);
                }
            });
        }
        
        /**
         * Show field validation error
         */
        function showFieldError($field, message) {
            clearFieldError($field);
            
            const $error = $('<span class="igny8-field-error" style="color: red; font-size: 12px; display: block; margin-top: 5px;"></span>');
            $error.text(message);
            $field.after($error);
            $field.addClass('error');
        }
        
        /**
         * Clear field validation error
         */
        function clearFieldError($field) {
            $field.siblings('.igny8-field-error').remove();
            $field.removeClass('error');
        }

        //==================================================
        // ## INITIALIZATION
        //==================================================
        
        // Only initialize on our settings page
        if ($('body').hasClass('settings_page_igny8')) {
            handleFieldModeChange();
            initColorPickers();
            initFormValidation();
        }

    });

})(jQuery); 