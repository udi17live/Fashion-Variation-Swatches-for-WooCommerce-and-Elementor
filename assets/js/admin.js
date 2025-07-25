/**
 * Fashion Variation Swatches - Admin JavaScript
 * Version: 1.0.0
 */

(function($) {
    'use strict';

    var FashionVariationSwatchesAdmin = {
        init: function() {
            this.initColorPickers();
            this.bindEvents();
            this.initTooltips();
        },

        initColorPickers: function() {
            // Initialize WordPress color picker
            if ($.fn.wpColorPicker) {
                $('.fashion-variation-swatches-color-picker').wpColorPicker({
                    change: function(event, ui) {
                        FashionVariationSwatchesAdmin.updateColorPreview($(this), ui.color.toString());
                    },
                    clear: function(event) {
                        FashionVariationSwatchesAdmin.updateColorPreview($(this), '#ffffff');
                    }
                });
            }
        },

        bindEvents: function() {
            $(document)
                .on('click', '.color-preview', this.onColorPreviewClick)
                .on('change', '.fashion-variation-swatches-color-picker', this.onColorChange)
                .on('ajaxComplete', this.onAjaxComplete);
        },

        onColorPreviewClick: function(e) {
            e.preventDefault();
            var $preview = $(this);
            var $input = $preview.siblings('.fashion-variation-swatches-color-picker');
            
            if ($input.length) {
                $input.wpColorPicker('open');
            }
        },

        onColorChange: function() {
            var $input = $(this);
            var color = $input.val();
            FashionVariationSwatchesAdmin.updateColorPreview($input, color);
        },

        onAjaxComplete: function() {
            // Reinitialize color pickers after AJAX requests
            setTimeout(function() {
                FashionVariationSwatchesAdmin.initColorPickers();
            }, 100);
        },

        updateColorPreview: function($input, color) {
            var $preview = $input.siblings('.color-preview');
            var $tablePreview = $input.closest('tr').find('.fashion-variation-swatches-color-preview');
            
            if ($preview.length) {
                $preview.css('background-color', color);
            }
            
            if ($tablePreview.length) {
                $tablePreview.css('background-color', color);
            }
        },

        initTooltips: function() {
            // Add tooltips to help text
            $('.description').each(function() {
                var $desc = $(this);
                if ($desc.text().length > 50) {
                    $desc.attr('title', $desc.text());
                }
            });
        },

        // Settings page functionality
        validateSettings: function() {
            var isValid = true;
            var errors = [];

            // Validate required fields
            $('input[required], select[required]').each(function() {
                var $field = $(this);
                if (!$field.val()) {
                    errors.push('Please fill in all required fields.');
                    $field.addClass('error');
                    isValid = false;
                } else {
                    $field.removeClass('error');
                }
            });

            if (!isValid) {
                this.showNotice(errors.join(' '), 'error');
            }

            return isValid;
        },

        showNotice: function(message, type) {
            type = type || 'info';
            var $notice = $('<div class="notice notice-' + type + ' is-dismissible wc-variation-swatches-notice"><p>' + message + '</p></div>');
            $('.wrap h1').after($notice);
            
            // Auto-dismiss after 5 seconds
            setTimeout(function() {
                $notice.fadeOut();
            }, 5000);
        },

        // Enhanced color picker with live preview
        enhanceColorPicker: function($input) {
            var $wrapper = $('<div class="color-field-wrapper"></div>');
            var $preview = $('<div class="color-preview"></div>');
            
            $input.wrap($wrapper);
            $input.after($preview);
            
            // Set initial color
            var initialColor = $input.val() || '#ffffff';
            $preview.css('background-color', initialColor);
            
            // Initialize WordPress color picker with custom options
            $input.wpColorPicker({
                defaultColor: '#ffffff',
                change: function(event, ui) {
                    $preview.css('background-color', ui.color.toString());
                },
                clear: function() {
                    $preview.css('background-color', '#ffffff');
                },
                hide: true,
                palettes: [
                    '#000000', '#ffffff', '#ff0000', '#00ff00', '#0000ff',
                    '#ffff00', '#ff00ff', '#00ffff', '#ffa500', '#800080',
                    '#008000', '#000080', '#808080', '#c0c0c0', '#800000',
                    '#808000'
                ]
            });
        }
    };

    // Settings form enhancements
    var SettingsPage = {
        init: function() {
            this.bindEvents();
            this.initDependencies();
        },

        bindEvents: function() {
            $(document)
                .on('change', '#fashion_variation_swatches_enable_tooltip', this.toggleTooltipOptions)
                .on('change', '#fashion_variation_swatches_enable_shop_filters', this.toggleFilterOptions)
                .on('submit', '.fashion-variation-swatches-settings-form', this.onFormSubmit);
        },

        toggleTooltipOptions: function() {
            var $checkbox = $(this);
            var $options = $('.tooltip-options');
            
            if ($checkbox.is(':checked')) {
                $options.slideDown();
            } else {
                $options.slideUp();
            }
        },

        toggleFilterOptions: function() {
            var $checkbox = $(this);
            var $options = $('.filter-options');
            
            if ($checkbox.is(':checked')) {
                $options.slideDown();
            } else {
                $options.slideUp();
            }
        },

        onFormSubmit: function(e) {
            if (!WCVariationSwatchesAdmin.validateSettings()) {
                e.preventDefault();
                return false;
            }
            
            // Show loading state
            var $form = $(this);
            var $submitButton = $form.find('input[type="submit"]');
            
            $submitButton.prop('disabled', true);
            $form.addClass('loading');
            
            // Add spinner
            $submitButton.after('<span class="settings-spinner"></span>');
        },

        initDependencies: function() {
            // Initialize dependent field visibility
            this.toggleTooltipOptions.call($('#fashion_variation_swatches_enable_tooltip'));
            this.toggleFilterOptions.call($('#fashion_variation_swatches_enable_shop_filters'));
        }
    };

    // Initialize when DOM is ready
    $(document).ready(function() {
        FashionVariationSwatchesAdmin.init();
        
        // Initialize settings page if we're on it
        if ($('body').hasClass('woocommerce_page_fashion-variation-swatches')) {
            SettingsPage.init();
        }
    });

    // Handle dynamic content (AJAX)
    $(document).ajaxComplete(function(event, xhr, settings) {
        // Reinitialize color pickers after AJAX
        if (settings.url && settings.url.indexOf('edit-tags.php') > -1) {
            setTimeout(function() {
                FashionVariationSwatchesAdmin.initColorPickers();
            }, 500);
        }
    });

    // Enhanced attribute term management
    var AttributeTerms = {
        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            $(document)
                .on('click', '.add-color-swatch', this.addColorSwatch)
                .on('click', '.remove-color-swatch', this.removeColorSwatch);
        },

        addColorSwatch: function(e) {
            e.preventDefault();
            // Implementation for dynamically adding color swatches
        },

        removeColorSwatch: function(e) {
            e.preventDefault();
            // Implementation for removing color swatches
        }
    };

    // Initialize attribute terms management
    if ($('body').hasClass('edit-tags-php') || $('body').hasClass('term-php')) {
        AttributeTerms.init();
    }

    // Expose to global scope for external access
    window.FashionVariationSwatchesAdmin = FashionVariationSwatchesAdmin;

})(jQuery);

// Additional utility functions
(function($) {
    'use strict';

    // Helper function to validate hex colors
    window.isValidHexColor = function(color) {
        return /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(color);
    };

    // Helper function to get contrast color
    window.getContrastColor = function(hexColor) {
        // Remove # if present
        hexColor = hexColor.replace('#', '');
        
        // Convert to RGB
        var r = parseInt(hexColor.substr(0, 2), 16);
        var g = parseInt(hexColor.substr(2, 2), 16);
        var b = parseInt(hexColor.substr(4, 2), 16);
        
        // Calculate luminance
        var luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
        
        return luminance > 0.5 ? '#000000' : '#ffffff';
    };

})(jQuery); 