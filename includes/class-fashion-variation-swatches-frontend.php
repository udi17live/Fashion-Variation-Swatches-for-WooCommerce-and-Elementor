<?php
/**
 * Frontend functionality class
 *
 * @package fashion_variation_swatches
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Frontend class
 */
class Fashion_Variation_Swatches_Frontend {

    /**
     * Instance
     *
     * @var Fashion_Variation_Swatches_Frontend
     */
    private static $instance = null;

    /**
     * Get instance
     *
     * @return Fashion_Variation_Swatches_Frontend
     */
    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Add body classes
        add_filter( 'body_class', [ $this, 'add_body_classes' ] );
        
        // Add schema markup
        add_action( 'woocommerce_single_product_summary', [ $this, 'add_schema_markup' ], 30 );
        
        // Handle cart variations display
        add_filter( 'woocommerce_get_item_data', [ $this, 'cart_item_data' ], 10, 2 );
        
        // Add inline styles for dynamic colors
        add_action( 'wp_head', [ $this, 'add_inline_styles' ] );
    }

    /**
     * Add body classes
     *
     * @param array $classes
     * @return array
     */
    public function add_body_classes( $classes ) {
        if ( is_product() || is_shop() || is_product_category() || is_product_tag() ) {
            $classes[] = 'fashion-variation-swatches-enabled';
            
            if ( get_option( 'fashion_variation_swatches_enable_tooltip', 'yes' ) === 'yes' ) {
                $classes[] = 'fashion-variation-swatches-tooltips-enabled';
            }
            
            if ( get_option( 'fashion_variation_swatches_enable_shop_filters', 'yes' ) === 'yes' ) {
                $classes[] = 'fashion-variation-swatches-filters-enabled';
            }
        }
        return $classes;
    }

    /**
     * Add schema markup for variations
     */
    public function add_schema_markup() {
        global $product;
        
        if ( ! $product instanceof WC_Product_Variable ) {
            return;
        }
        
        $variations = $product->get_available_variations();
        if ( empty( $variations ) ) {
            return;
        }
        
        $schema_data = [];
        foreach ( $variations as $variation ) {
            $variation_obj = wc_get_product( $variation['variation_id'] );
            if ( ! $variation_obj ) {
                continue;
            }
            
            $schema_data[] = [
                '@type' => 'Product',
                'name' => $variation_obj->get_name(),
                'sku' => $variation_obj->get_sku(),
                'offers' => [
                    '@type' => 'Offer',
                    'price' => $variation_obj->get_price(),
                    'priceCurrency' => get_woocommerce_currency(),
                    'availability' => $variation_obj->is_in_stock() ? 'InStock' : 'OutOfStock',
                ],
            ];
        }
        
        if ( ! empty( $schema_data ) ) {
            ?>
            <script type="application/ld+json">
            {
                "@context": "https://schema.org",
                "@type": "ProductGroup",
                "hasVariant": <?php echo wp_json_encode( $schema_data ); ?>
            }
            </script>
            <?php
        }
    }

    /**
     * Modify cart item data display
     *
     * @param array $item_data
     * @param array $cart_item
     * @return array
     */
    public function cart_item_data( $item_data, $cart_item ) {
        if ( ! isset( $cart_item['variation'] ) || empty( $cart_item['variation'] ) ) {
            return $item_data;
        }
        
        $size_attribute = get_option( 'fashion_variation_swatches_size_attribute', 'pa_size' );
        $color_attribute = get_option( 'fashion_variation_swatches_color_attribute', 'pa_color' );
        
        foreach ( $item_data as $key => $data ) {
            $attribute_name = str_replace( 'attribute_', '', $data['key'] );
            
            // Add color preview for color attributes
            if ( $attribute_name === $color_attribute ) {
                $term = get_term_by( 'slug', $data['value'], $color_attribute );
                if ( $term ) {
                    $color_value = get_term_meta( $term->term_id, 'fashion_variation_swatches_color', true );
                    if ( $color_value ) {
                        $item_data[ $key ]['display'] = '<span class="wc-cart-color-preview" style="background-color: ' . esc_attr( $color_value ) . '; width: 15px; height: 15px; display: inline-block; border-radius: 50%; border: 1px solid #ddd; margin-right: 5px; vertical-align: middle;"></span>' . $data['display'];
                    }
                }
            }
        }
        
        return $item_data;
    }

    /**
     * Add inline styles for dynamic elements
     */
    public function add_inline_styles() {
        if ( ! ( is_product() || is_shop() || is_product_category() || is_product_tag() ) ) {
            return;
        }
        
        $size_style = get_option( 'fashion_variation_swatches_size_style', 'square' );
        $color_style = get_option( 'fashion_variation_swatches_color_style', 'circle' );
        
        ?>
        <style type="text/css">
        :root {
            --wc-swatch-size-style: <?php echo esc_attr( $size_style ); ?>;
            --wc-swatch-color-style: <?php echo esc_attr( $color_style ); ?>;
        }
        
        <?php if ( $size_style === 'circle' ) : ?>
        .wc-size-swatch.circle {
            border-radius: 50% !important;
        }
        <?php elseif ( $size_style === 'rounded' ) : ?>
        .wc-size-swatch.rounded {
            border-radius: 8px !important;
        }
        <?php endif; ?>
        
        <?php if ( $color_style === 'square' ) : ?>
        .wc-color-swatch.square {
            border-radius: 0 !important;
        }
        <?php elseif ( $color_style === 'rounded' ) : ?>
        .wc-color-swatch.rounded {
            border-radius: 8px !important;
        }
        <?php endif; ?>
        </style>
        <?php
    }

    /**
     * Get swatch HTML for external use
     *
     * @param string $attribute
     * @param array $options
     * @param string $selected
     * @return string
     */
    public function get_swatch_html( $attribute, $options = [], $selected = '' ) {
        $size_attribute = get_option( 'fashion_variation_swatches_size_attribute', 'pa_size' );
        $color_attribute = get_option( 'fashion_variation_swatches_color_attribute', 'pa_color' );
        
        if ( empty( $options ) ) {
            $terms = get_terms( [
                'taxonomy' => $attribute,
                'hide_empty' => false,
            ] );
            
            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                $options = wp_list_pluck( $terms, 'slug' );
            }
        }
        
        if ( empty( $options ) ) {
            return '';
        }
        
        $html = '';
        $enable_tooltip = get_option( 'fashion_variation_swatches_enable_tooltip', 'yes' );
        
        if ( $attribute === $size_attribute ) {
            $style = get_option( 'fashion_variation_swatches_size_style', 'square' );
            $html .= '<div class="fashion-variation-swatches wc-size-swatches">';
            
            foreach ( $options as $option ) {
                $term = get_term_by( 'slug', $option, $attribute );
                if ( ! $term ) continue;
                
                $selected_class = ( $selected === $option ) ? ' selected' : '';
                $tooltip = $enable_tooltip === 'yes' ? 'title="' . esc_attr( $term->name ) . '"' : '';
                
                $html .= sprintf(
                    '<span class="wc-swatch wc-size-swatch %s%s" data-value="%s" %s>%s</span>',
                    esc_attr( $style ),
                    $selected_class,
                    esc_attr( $option ),
                    $tooltip,
                    esc_html( $term->name )
                );
            }
            
            $html .= '</div>';
            
        } elseif ( $attribute === $color_attribute ) {
            $style = get_option( 'fashion_variation_swatches_color_style', 'circle' );
            $html .= '<div class="fashion-variation-swatches wc-color-swatches">';
            
            foreach ( $options as $option ) {
                $term = get_term_by( 'slug', $option, $attribute );
                if ( ! $term ) continue;
                
                $color_value = get_term_meta( $term->term_id, 'fashion_variation_swatches_color', true );
                if ( ! $color_value ) {
                    $color_value = '#cccccc';
                }
                
                $selected_class = ( $selected === $option ) ? ' selected' : '';
                $tooltip = $enable_tooltip === 'yes' ? 'title="' . esc_attr( $term->name ) . '"' : '';
                
                $html .= sprintf(
                    '<span class="wc-swatch wc-color-swatch %s%s" data-value="%s" style="background-color: %s;" %s></span>',
                    esc_attr( $style ),
                    $selected_class,
                    esc_attr( $option ),
                    esc_attr( $color_value ),
                    $tooltip
                );
            }
            
            $html .= '</div>';
        }
        
        return $html;
    }
} 