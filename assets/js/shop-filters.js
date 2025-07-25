/**
 * Fashion Variation Swatches - Shop Filters JavaScript
 * Version: 1.0.0
 */

(function($) {
    'use strict';

    var FashionShopFilters = {
        init: function() {
            this.bindEvents();
            this.initFilters();
        },

        bindEvents: function() {
            $(document)
                .on('change', '.fashion-filter-checkbox', this.onFilterChange)
                .on('click', '.fashion-clear-filters', this.clearFilters);
        },

        initFilters: function() {
            // Initialize filter state from URL
            this.updateFilterStateFromURL();
        },

        onFilterChange: function() {
            var $checkbox = $(this);
            var $filterWidget = $checkbox.closest('.fashion-variation-swatches-filter-widget');
            
            // Add loading state
            $filterWidget.addClass('loading');
            
            // Debounce the filter update
            clearTimeout(FashionShopFilters.filterTimeout);
            FashionShopFilters.filterTimeout = setTimeout(function() {
                FashionShopFilters.updateFilters();
            }, 300);
        },

        updateFilters: function() {
            var filters = this.getSelectedFilters();
            var url = this.buildFilterURL(filters);
            
            // Update browser URL
            if (window.history && window.history.pushState) {
                window.history.pushState({}, '', url);
            }
            
            // Load filtered products
            this.loadFilteredProducts(filters);
        },

        getSelectedFilters: function() {
            var filters = {
                sizes: [],
                colors: []
            };
            
            // Get selected sizes
            $('.fashion-size-filter input[type="checkbox"]:checked').each(function() {
                filters.sizes.push($(this).val());
            });
            
            // Get selected colors
            $('.fashion-color-filter input[type="checkbox"]:checked').each(function() {
                filters.colors.push($(this).val());
            });
            
            return filters;
        },

        buildFilterURL: function(filters) {
            var url = new URL(window.location);
            
            // Remove existing filter parameters
            url.searchParams.delete('filter_size');
            url.searchParams.delete('filter_color');
            
            // Add new filter parameters
            if (filters.sizes.length > 0) {
                url.searchParams.set('filter_size', filters.sizes.join(','));
            }
            
            if (filters.colors.length > 0) {
                url.searchParams.set('filter_color', filters.colors.join(','));
            }
            
            // Reset to page 1
            url.searchParams.delete('paged');
            
            return url.toString();
        },

        loadFilteredProducts: function(filters) {
            if (typeof fashionVariationSwatchesFilters === 'undefined') {
                // Fallback to page reload if AJAX is not available
                window.location.reload();
                return;
            }
            
            var $productsContainer = $('.products, ul.products, .woocommerce-loop-product__title').closest('.products, ul.products');
            
            if (!$productsContainer.length) {
                $productsContainer = $('.woocommerce-shop-loop, .woocommerce-loop');
            }
            
            // Add loading overlay
            this.showLoadingOverlay($productsContainer);
            
            $.ajax({
                url: fashionVariationSwatchesFilters.ajax_url,
                type: 'POST',
                data: {
                    action: 'fashion_variation_swatches_filter',
                    nonce: fashionVariationSwatchesFilters.nonce,
                    sizes: filters.sizes,
                    colors: filters.colors,
                    page: 1
                },
                success: function(response) {
                    if (response.success) {
                        $productsContainer.html(response.data.content);
                        WCShopFilters.updateResultsCount(response.data.found_posts);
                        
                        // Trigger WooCommerce events
                        $(document.body).trigger('wc_fragments_loaded');
                        $(document.body).trigger('wc_update_cart');
                    } else {
                        console.error('Filter request failed:', response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                    // Fallback to page reload
                    window.location.reload();
                },
                complete: function() {
                    WCShopFilters.hideLoadingOverlay();
                    $('.wc-variation-swatches-filter-widget').removeClass('loading');
                }
            });
        },

        showLoadingOverlay: function($container) {
            if ($container.length) {
                $container.css('position', 'relative').append(
                    '<div class="wc-shop-filter-loading">' +
                    '<div class="wc-shop-filter-spinner"></div>' +
                    '</div>'
                );
            }
        },

        hideLoadingOverlay: function() {
            $('.wc-shop-filter-loading').remove();
        },

        updateResultsCount: function(count) {
            var $resultsCount = $('.woocommerce-result-count');
            if ($resultsCount.length && count !== undefined) {
                var text = count === 1 ? 
                    'Showing the single result' : 
                    'Showing all ' + count + ' results';
                $resultsCount.text(text);
            }
        },

        updateFilterStateFromURL: function() {
            var urlParams = new URLSearchParams(window.location.search);
            
            // Update size filters
            var sizeFilters = urlParams.get('filter_size');
            if (sizeFilters) {
                var sizes = sizeFilters.split(',');
                sizes.forEach(function(size) {
                    $('.wc-size-filter input[value="' + size.trim() + '"]').prop('checked', true);
                });
            }
            
            // Update color filters
            var colorFilters = urlParams.get('filter_color');
            if (colorFilters) {
                var colors = colorFilters.split(',');
                colors.forEach(function(color) {
                    $('.wc-color-filter input[value="' + color.trim() + '"]').prop('checked', true);
                });
            }
        },

        clearFilters: function(e) {
            e.preventDefault();
            
            // Uncheck all filter checkboxes
            $('.wc-filter-checkbox').prop('checked', false);
            
            // Update filters
            WCShopFilters.updateFilters();
        },

        // Add clear filters button if not exists
        addClearFiltersButton: function() {
            if ($('.fashion-clear-filters').length === 0 && $('.fashion-filter-checkbox:checked').length > 0) {
                $('.fashion-variation-swatches-filters').prepend(
                    '<div class="fashion-filter-controls">' +
                    '<button type="button" class="fashion-clear-filters button">' +
                    'Clear All Filters' +
                    '</button>' +
                    '</div>'
                );
            } else if ($('.fashion-filter-checkbox:checked').length === 0) {
                $('.fashion-filter-controls').remove();
            }
        }
    };

    // Initialize when DOM is ready
    $(document).ready(function() {
        if ($('.fashion-variation-swatches-filters').length > 0) {
            FashionShopFilters.init();
        }
    });

    // Handle browser back/forward buttons
    $(window).on('popstate', function() {
        FashionShopFilters.updateFilterStateFromURL();
        FashionShopFilters.updateFilters();
    });

    // Update clear button visibility when filters change
    $(document).on('change', '.fashion-filter-checkbox', function() {
        FashionShopFilters.addClearFiltersButton();
    });

    // Expose to global scope
    window.FashionShopFilters = FashionShopFilters;

})(jQuery);

// Add CSS for loading states
jQuery(document).ready(function($) {
    if ($('.fashion-shop-filter-loading').length === 0) {
        $('head').append(`
            <style>
            .fashion-shop-filter-loading {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(255, 255, 255, 0.8);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 999;
            }
            
            .fashion-shop-filter-spinner {
                width: 40px;
                height: 40px;
                border: 4px solid #f3f3f3;
                border-top: 4px solid #333;
                border-radius: 50%;
                animation: spin 1s linear infinite;
            }
            
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            
            .fashion-variation-swatches-filter-widget.loading {
                opacity: 0.7;
                pointer-events: none;
            }
            
            .fashion-filter-controls {
                margin-bottom: 20px;
                text-align: center;
            }
            
            .fashion-clear-filters {
                background: #f8f8f8;
                border: 1px solid #ddd;
                padding: 8px 16px;
                border-radius: 4px;
                cursor: pointer;
                font-size: 14px;
            }
            
            .fashion-clear-filters:hover {
                background: #e8e8e8;
                border-color: #ccc;
            }
            </style>
        `);
    }
}); 