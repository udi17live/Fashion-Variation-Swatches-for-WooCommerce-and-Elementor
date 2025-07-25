/**
 * Fashion Variation Swatches - Frontend JavaScript
 * Version: 1.0.0
 */

(function($) {
    'use strict';

    var FashionVariationSwatches = {
        init: function() {
            this.bindEvents();
            this.initVariationForms();
        },

        bindEvents: function() {
            $(document)
                .on('click', '.fashion-variation-swatches .fashion-swatch', this.onSwatchClick)
                .on('found_variation', 'form.variations_form', this.onVariationFound)
                .on('reset_data', 'form.variations_form', this.onVariationReset)
                .on('wc_variation_form', this.onVariationFormInit);
        },

        initVariationForms: function() {
            $('form.variations_form').each(function() {
                var $form = $(this);
                FashionVariationSwatches.syncSwatchesWithSelects($form);
            });
        },

        onSwatchClick: function(e) {
            e.preventDefault();
            
            var $swatch = $(this);
            var $swatches = $swatch.closest('.fashion-variation-swatches');
            var $select = $swatches.next('select.fashion-variation-swatches-select');
            var value = $swatch.data('value');
            var $form = $swatch.closest('form.variations_form');
            
            // If clicking on already selected swatch, deselect it
            if ($swatch.hasClass('selected')) {
                value = '';
            }
            
            // Update visual selection
            $swatches.find('.fashion-swatch').removeClass('selected');
            if (value !== '') {
                $swatch.addClass('selected');
            }
            
            // Update hidden select
            $select.val(value).trigger('change');
            
            // Trigger WooCommerce variation events
            if ($form.length) {
                $form.trigger('woocommerce_variation_select_change');
                $form.trigger('check_variations');
            }
            
            // Add loading state
            $swatches.addClass('loading');
            setTimeout(function() {
                $swatches.removeClass('loading');
            }, 500);
        },

        onVariationFound: function(event, variation) {
            var $form = $(this);
            FashionVariationSwatches.updateSwatchAvailability($form, variation);
        },

        onVariationReset: function() {
            var $form = $(this);
            $form.find('.fashion-variation-swatches .fashion-swatch').removeClass('selected disabled out-of-stock');
            FashionVariationSwatches.syncSwatchesWithSelects($form);
        },

        onVariationFormInit: function() {
            var $form = $(this);
            FashionVariationSwatches.syncSwatchesWithSelects($form);
        },

        syncSwatchesWithSelects: function($form) {
            $form.find('.fashion-variation-swatches').each(function() {
                var $swatches = $(this);
                var $select = $swatches.next('select.fashion-variation-swatches-select');
                var selectedValue = $select.val();
                
                // Update visual selection
                $swatches.find('.fashion-swatch').removeClass('selected');
                if (selectedValue) {
                    $swatches.find('.fashion-swatch[data-value="' + selectedValue + '"]').addClass('selected');
                }
            });
        },

        updateSwatchAvailability: function($form, variation) {
            var variationData = $form.data('product_variations') || [];
            
            $form.find('.wc-variation-swatches').each(function() {
                var $swatches = $(this);
                var attributeName = $swatches.data('attribute');
                
                $swatches.find('.wc-swatch').each(function() {
                    var $swatch = $(this);
                    var swatchValue = $swatch.data('value');
                    var isAvailable = WCVariationSwatches.isVariationAvailable(
                        variationData, 
                        attributeName, 
                        swatchValue, 
                        WCVariationSwatches.getSelectedAttributes($form)
                    );
                    
                    $swatch.toggleClass('disabled', !isAvailable);
                    $swatch.toggleClass('out-of-stock', !isAvailable);
                });
            });
        },

        isVariationAvailable: function(variations, attributeName, attributeValue, selectedAttributes) {
            return variations.some(function(variation) {
                if (!variation.variation_is_active || !variation.variation_is_visible) {
                    return false;
                }
                
                var variationAttributes = variation.attributes;
                var isMatch = true;
                
                // Check if this variation matches the attribute value we're testing
                if (variationAttributes[attributeName] !== '' && 
                    variationAttributes[attributeName] !== attributeValue) {
                    return false;
                }
                
                // Check if this variation matches other selected attributes
                for (var attr in selectedAttributes) {
                    if (selectedAttributes[attr] !== '' && 
                        variationAttributes[attr] !== '' && 
                        variationAttributes[attr] !== selectedAttributes[attr]) {
                        isMatch = false;
                        break;
                    }
                }
                
                return isMatch;
            });
        },

        getSelectedAttributes: function($form) {
            var attributes = {};
            
            $form.find('.fashion-variation-swatches').each(function() {
                var $swatches = $(this);
                var $select = $swatches.next('select.fashion-variation-swatches-select');
                var attributeName = $swatches.data('attribute');
                var selectedValue = $select.val();
                
                attributes[attributeName] = selectedValue || '';
            });
            
            // Also get values from regular select dropdowns
            $form.find('select[name^="attribute_"]:not(.fashion-variation-swatches-select)').each(function() {
                var $select = $(this);
                var attributeName = $select.attr('name');
                attributes[attributeName] = $select.val() || '';
            });
            
            return attributes;
        },

        // Accessibility helpers
        addKeyboardSupport: function() {
            $(document).on('keydown', '.fashion-swatch', function(e) {
                // Space or Enter key
                if (e.keyCode === 32 || e.keyCode === 13) {
                    e.preventDefault();
                    $(this).trigger('click');
                }
                
                // Arrow key navigation
                var $swatches = $(this).siblings('.fashion-swatch');
                var currentIndex = $swatches.index(this);
                var $target = null;
                
                switch (e.keyCode) {
                    case 37: // Left arrow
                    case 38: // Up arrow
                        $target = $swatches.eq(currentIndex - 1);
                        break;
                    case 39: // Right arrow
                    case 40: // Down arrow
                        $target = $swatches.eq(currentIndex + 1);
                        break;
                }
                
                if ($target && $target.length) {
                    e.preventDefault();
                    $target.focus();
                }
            });
        },

        // Tooltip enhancement
        enhanceTooltips: function() {
            if (!$('body').hasClass('fashion-variation-swatches-tooltips-enabled')) {
                return;
            }
            
            // Add ARIA labels for screen readers
            $('.fashion-swatch[title]').each(function() {
                var $swatch = $(this);
                var title = $swatch.attr('title');
                $swatch.attr('aria-label', title);
            });
        }
    };

    // Initialize when DOM is ready
    $(document).ready(function() {
        FashionVariationSwatches.init();
        FashionVariationSwatches.addKeyboardSupport();
        FashionVariationSwatches.enhanceTooltips();
    });

    // Re-initialize on AJAX complete (for dynamic content)
    $(document).ajaxComplete(function() {
        setTimeout(function() {
            FashionVariationSwatches.initVariationForms();
            FashionVariationSwatches.enhanceTooltips();
        }, 100);
    });

    // Expose to global scope for external access
    window.FashionVariationSwatches = FashionVariationSwatches;

})(jQuery); 