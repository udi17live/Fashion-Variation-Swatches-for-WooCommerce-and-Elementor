<?php
/**
 * Core functionality class
 *
 * @package fashion_variation_swatches
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Core class
 */
class Fashion_Variation_Swatches_Core {

    /**
     * Instance
     *
     * @var Fashion_Variation_Swatches_Core
     */
    private static $instance = null;

    /**
     * Get instance
     *
     * @return Fashion_Variation_Swatches_Core
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
        // WooCommerce variation display hooks
        add_filter( 'woocommerce_dropdown_variation_attribute_options_html', [ $this, 'variation_attribute_options_html' ], 100, 2 );
        add_action( 'woocommerce_single_product_summary', [ $this, 'add_variation_swatches_script' ], 25 );
        
        // Enqueue assets
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_assets' ] );
    }

    /**
     * Replace dropdown with swatches for supported attributes
     *
     * @param string $html
     * @param array $args
     * @return string
     */
    public function variation_attribute_options_html( $html, $args ) {
        global $product;

        if ( empty( $args['options'] ) || ! $product instanceof WC_Product_Variable ) {
            return $html;
        }

        $attribute = $args['attribute'];
        $options = $args['options'];
        $selected = $args['selected'];
        $name = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
        $id = $args['id'] ? $args['id'] : sanitize_title( $attribute );

        // Get attribute settings
        $size_attribute = get_option( 'fashion_variation_swatches_size_attribute', 'pa_size' );
        $color_attribute = get_option( 'fashion_variation_swatches_color_attribute', 'pa_color' );

        if ( $attribute === $size_attribute ) {
            return $this->render_size_swatches( $args, $options, $selected, $name, $id );
        } elseif ( $attribute === $color_attribute ) {
            return $this->render_color_swatches( $args, $options, $selected, $name, $id );
        }

        return $html;
    }

    /**
     * Render size swatches
     *
     * @param array $args
     * @param array $options
     * @param string $selected
     * @param string $name
     * @param string $id
     * @return string
     */
    private function render_size_swatches( $args, $options, $selected, $name, $id ) {
        $swatches = '';
        $style = get_option( 'fashion_variation_swatches_size_style', 'square' );
        $enable_tooltip = get_option( 'fashion_variation_swatches_enable_tooltip', 'yes' );

        $swatches .= '<div class="fashion-variation-swatches wc-size-swatches" data-attribute="' . esc_attr( $name ) . '">';

        foreach ( $options as $option ) {
            if ( in_array( $option, $args['options'], true ) ) {
                $term = get_term_by( 'slug', $option, str_replace( 'attribute_', '', $name ) );
                $term_name = $term ? $term->name : $option;
                
                $selected_class = ( $selected === $option ) ? ' selected' : '';
                $tooltip = $enable_tooltip === 'yes' ? 'title="' . esc_attr( $term_name ) . '"' : '';
                
                $swatches .= sprintf(
                    '<span class="wc-swatch wc-size-swatch %s%s" data-value="%s" %s>%s</span>',
                    esc_attr( $style ),
                    $selected_class,
                    esc_attr( $option ),
                    $tooltip,
                    esc_html( $term_name )
                );
            }
        }

        $swatches .= '</div>';
        
        // Add hidden select for form submission
        $swatches .= '<select name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" class="fashion-variation-swatches-select" style="display: none !important;">';
        $swatches .= '<option value="">' . __( 'Choose an option', 'fashion-variation-swatches' ) . '</option>';
        
        foreach ( $options as $option ) {
            if ( in_array( $option, $args['options'], true ) ) {
                $term = get_term_by( 'slug', $option, str_replace( 'attribute_', '', $name ) );
                $term_name = $term ? $term->name : $option;
                $selected_attr = ( $selected === $option ) ? 'selected="selected"' : '';
                
                $swatches .= '<option value="' . esc_attr( $option ) . '" ' . $selected_attr . '>' . esc_html( $term_name ) . '</option>';
            }
        }
        
        $swatches .= '</select>';

        return $swatches;
    }

    /**
     * Render color swatches
     *
     * @param array $args
     * @param array $options
     * @param string $selected
     * @param string $name
     * @param string $id
     * @return string
     */
    private function render_color_swatches( $args, $options, $selected, $name, $id ) {
        $swatches = '';
        $style = get_option( 'fashion_variation_swatches_color_style', 'circle' );
        $enable_tooltip = get_option( 'fashion_variation_swatches_enable_tooltip', 'yes' );

        $swatches .= '<div class="fashion-variation-swatches wc-color-swatches" data-attribute="' . esc_attr( $name ) . '">';

        foreach ( $options as $option ) {
            if ( in_array( $option, $args['options'], true ) ) {
                $term = get_term_by( 'slug', $option, str_replace( 'attribute_', '', $name ) );
                $term_name = $term ? $term->name : $option;
                
                // Get color value from term meta
                $color_value = get_term_meta( $term->term_id, 'fashion_variation_swatches_color', true );
                if ( ! $color_value ) {
                    $color_value = '#cccccc'; // Default color
                }
                
                $selected_class = ( $selected === $option ) ? ' selected' : '';
                $tooltip = $enable_tooltip === 'yes' ? 'title="' . esc_attr( $term_name ) . '"' : '';
                
                $swatches .= sprintf(
                    '<span class="wc-swatch wc-color-swatch %s%s" data-value="%s" style="background-color: %s;" %s></span>',
                    esc_attr( $style ),
                    $selected_class,
                    esc_attr( $option ),
                    esc_attr( $color_value ),
                    $tooltip
                );
            }
        }

        $swatches .= '</div>';
        
        // Add hidden select for form submission
        $swatches .= '<select name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" class="fashion-variation-swatches-select" style="display: none !important;">';
        $swatches .= '<option value="">' . __( 'Choose an option', 'fashion-variation-swatches' ) . '</option>';
        
        foreach ( $options as $option ) {
            if ( in_array( $option, $args['options'], true ) ) {
                $term = get_term_by( 'slug', $option, str_replace( 'attribute_', '', $name ) );
                $term_name = $term ? $term->name : $option;
                $selected_attr = ( $selected === $option ) ? 'selected="selected"' : '';
                
                $swatches .= '<option value="' . esc_attr( $option ) . '" ' . $selected_attr . '>' . esc_html( $term_name ) . '</option>';
            }
        }
        
        $swatches .= '</select>';

        return $swatches;
    }

    /**
     * Add variation swatches JavaScript
     */
    public function add_variation_swatches_script() {
        global $product;
        
        if ( ! $product instanceof WC_Product_Variable ) {
            return;
        }
        
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Handle swatch clicks
            $('.fashion-variation-swatches .wc-swatch').on('click', function() {
                var $swatch = $(this);
                var $swatches = $swatch.closest('.fashion-variation-swatches');
                var $select = $swatches.next('select.fashion-variation-swatches-select');
                var value = $swatch.data('value');
                
                // Update visual selection
                $swatches.find('.wc-swatch').removeClass('selected');
                $swatch.addClass('selected');
                
                // Update hidden select
                $select.val(value).trigger('change');
            });
            
            // Handle variation form reset
            $('form.variations_form').on('reset_data', function() {
                $('.fashion-variation-swatches .wc-swatch').removeClass('selected');
            });
        });
        </script>
        <?php
    }

    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        if ( is_product() || is_shop() || is_product_category() || is_product_tag() ) {
            wp_enqueue_style(
                'fashion-variation-swatches',
                FASHION_VARIATION_SWATCHES_PLUGIN_URL . 'assets/css/frontend.css',
                [],
                FASHION_VARIATION_SWATCHES_VERSION
            );
            
            wp_enqueue_script(
                'fashion-variation-swatches',
                FASHION_VARIATION_SWATCHES_PLUGIN_URL . 'assets/js/frontend.js',
                [ 'jquery', 'wc-add-to-cart-variation' ],
                FASHION_VARIATION_SWATCHES_VERSION,
                true
            );
        }
    }
} 