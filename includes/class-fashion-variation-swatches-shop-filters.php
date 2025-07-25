<?php
/**
 * Shop filters class
 *
 * @package fashion_variation_swatches
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Shop filters class
 */
class Fashion_Variation_Swatches_Shop_Filters {

    /**
     * Instance
     *
     * @var Fashion_Variation_Swatches_Shop_Filters
     */
    private static $instance = null;

    /**
     * Get instance
     *
     * @return Fashion_Variation_Swatches_Shop_Filters
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
        if ( get_option( 'fashion_variation_swatches_enable_shop_filters', 'yes' ) === 'yes' ) {
            // Add filter widgets
            add_action( 'woocommerce_sidebar', [ $this, 'add_filter_widgets' ] );
            
            // Handle filter queries
            add_action( 'pre_get_posts', [ $this, 'filter_products_by_attributes' ] );
            
            // Add filter scripts
            add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_filter_scripts' ] );
            
            // AJAX handlers
            add_action( 'wp_ajax_fashion_variation_swatches_filter', [ $this, 'ajax_filter_products' ] );
            add_action( 'wp_ajax_nopriv_fashion_variation_swatches_filter', [ $this, 'ajax_filter_products' ] );
        }
    }

    /**
     * Add filter widgets to shop sidebar
     */
    public function add_filter_widgets() {
        if ( is_shop() || is_product_category() || is_product_tag() ) {
            echo '<div class="fashion-variation-swatches-filters">';
            $this->render_size_filter();
            $this->render_color_filter();
            echo '</div>';
        }
    }

    /**
     * Render size filter widget
     */
    private function render_size_filter() {
        $size_attribute = get_option( 'fashion_variation_swatches_size_attribute', 'pa_size' );
        $terms = get_terms( [
            'taxonomy' => $size_attribute,
            'hide_empty' => true,
        ] );

        if ( empty( $terms ) || is_wp_error( $terms ) ) {
            return;
        }

        $selected_sizes = isset( $_GET['filter_size'] ) ? explode( ',', sanitize_text_field( $_GET['filter_size'] ) ) : [];
        ?>
        <div class="fashion-variation-swatches-filter-widget wc-size-filter">
            <h3 class="widget-title"><?php esc_html_e( 'Filter by Size', 'fashion-variation-swatches' ); ?></h3>
            <div class="fashion-variation-swatches-filter-content">
                <?php foreach ( $terms as $term ) : ?>
                    <label class="wc-filter-item wc-size-filter-item">
                        <input type="checkbox" 
                               name="filter_size[]" 
                               value="<?php echo esc_attr( $term->slug ); ?>"
                               <?php checked( in_array( $term->slug, $selected_sizes ) ); ?>
                               class="wc-filter-checkbox">
                        <span class="wc-size-swatch square"><?php echo esc_html( $term->name ); ?></span>
                        <span class="wc-filter-count">(<?php echo $term->count; ?>)</span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render color filter widget
     */
    private function render_color_filter() {
        $color_attribute = get_option( 'fashion_variation_swatches_color_attribute', 'pa_color' );
        $terms = get_terms( [
            'taxonomy' => $color_attribute,
            'hide_empty' => true,
        ] );

        if ( empty( $terms ) || is_wp_error( $terms ) ) {
            return;
        }

        $selected_colors = isset( $_GET['filter_color'] ) ? explode( ',', sanitize_text_field( $_GET['filter_color'] ) ) : [];
        ?>
        <div class="fashion-variation-swatches-filter-widget wc-color-filter">
            <h3 class="widget-title"><?php esc_html_e( 'Filter by Color', 'fashion-variation-swatches' ); ?></h3>
            <div class="fashion-variation-swatches-filter-content">
                <?php foreach ( $terms as $term ) : ?>
                    <?php
                    $color_value = get_term_meta( $term->term_id, 'fashion_variation_swatches_color', true );
                    if ( ! $color_value ) {
                        $color_value = '#cccccc';
                    }
                    ?>
                    <label class="wc-filter-item wc-color-filter-item">
                        <input type="checkbox" 
                               name="filter_color[]" 
                               value="<?php echo esc_attr( $term->slug ); ?>"
                               <?php checked( in_array( $term->slug, $selected_colors ) ); ?>
                               class="wc-filter-checkbox">
                        <span class="wc-color-swatch circle" 
                              style="background-color: <?php echo esc_attr( $color_value ); ?>;"
                              title="<?php echo esc_attr( $term->name ); ?>"></span>
                        <span class="wc-filter-label"><?php echo esc_html( $term->name ); ?></span>
                        <span class="wc-filter-count">(<?php echo $term->count; ?>)</span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Filter products by selected attributes
     *
     * @param WP_Query $query
     */
    public function filter_products_by_attributes( $query ) {
        if ( ! is_admin() && $query->is_main_query() && ( is_shop() || is_product_category() || is_product_tag() ) ) {
            $meta_query = $query->get( 'meta_query' );
            $tax_query = $query->get( 'tax_query' );

            if ( ! is_array( $meta_query ) ) {
                $meta_query = [];
            }
            if ( ! is_array( $tax_query ) ) {
                $tax_query = [];
            }

            // Size filter
            if ( isset( $_GET['filter_size'] ) && ! empty( $_GET['filter_size'] ) ) {
                $size_attribute = get_option( 'fashion_variation_swatches_size_attribute', 'pa_size' );
                $sizes = explode( ',', sanitize_text_field( $_GET['filter_size'] ) );
                $sizes = array_filter( array_map( 'trim', $sizes ) );

                if ( ! empty( $sizes ) ) {
                    $tax_query[] = [
                        'taxonomy' => $size_attribute,
                        'field'    => 'slug',
                        'terms'    => $sizes,
                        'operator' => 'IN',
                    ];
                }
            }

            // Color filter
            if ( isset( $_GET['filter_color'] ) && ! empty( $_GET['filter_color'] ) ) {
                $color_attribute = get_option( 'fashion_variation_swatches_color_attribute', 'pa_color' );
                $colors = explode( ',', sanitize_text_field( $_GET['filter_color'] ) );
                $colors = array_filter( array_map( 'trim', $colors ) );

                if ( ! empty( $colors ) ) {
                    $tax_query[] = [
                        'taxonomy' => $color_attribute,
                        'field'    => 'slug',
                        'terms'    => $colors,
                        'operator' => 'IN',
                    ];
                }
            }

            if ( count( $tax_query ) > 1 ) {
                $tax_query['relation'] = 'AND';
            }

            $query->set( 'meta_query', $meta_query );
            $query->set( 'tax_query', $tax_query );
        }
    }

    /**
     * Enqueue filter scripts
     */
    public function enqueue_filter_scripts() {
        if ( is_shop() || is_product_category() || is_product_tag() ) {
            wp_enqueue_script(
                'fashion-variation-swatches-filters',
                FASHION_VARIATION_SWATCHES_PLUGIN_URL . 'assets/js/shop-filters.js',
                [ 'jquery' ],
                FASHION_VARIATION_SWATCHES_VERSION,
                true
            );

            wp_localize_script( 'fashion-variation-swatches-filters', 'wcVariationSwatchesFilters', [
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'fashion_variation_swatches_filter_nonce' ),
            ] );
        }
    }

    /**
     * AJAX filter products
     */
    public function ajax_filter_products() {
        if ( ! wp_verify_nonce( $_POST['nonce'], 'fashion_variation_swatches_filter_nonce' ) ) {
            wp_die( esc_html__( 'Security check failed', 'fashion-variation-swatches' ) );
        }

        $filters = [
            'size' => isset( $_POST['sizes'] ) ? array_map( 'sanitize_text_field', $_POST['sizes'] ) : [],
            'color' => isset( $_POST['colors'] ) ? array_map( 'sanitize_text_field', $_POST['colors'] ) : [],
        ];

        // Build query args
        $args = [
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => get_option( 'posts_per_page' ),
            'paged' => isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1,
        ];

        $tax_query = [];

        if ( ! empty( $filters['size'] ) ) {
            $size_attribute = get_option( 'fashion_variation_swatches_size_attribute', 'pa_size' );
            $tax_query[] = [
                'taxonomy' => $size_attribute,
                'field'    => 'slug',
                'terms'    => $filters['size'],
                'operator' => 'IN',
            ];
        }

        if ( ! empty( $filters['color'] ) ) {
            $color_attribute = get_option( 'fashion_variation_swatches_color_attribute', 'pa_color' );
            $tax_query[] = [
                'taxonomy' => $color_attribute,
                'field'    => 'slug',
                'terms'    => $filters['color'],
                'operator' => 'IN',
            ];
        }

        if ( count( $tax_query ) > 1 ) {
            $tax_query['relation'] = 'AND';
        }

        if ( ! empty( $tax_query ) ) {
            $args['tax_query'] = $tax_query;
        }

        $query = new WP_Query( $args );

        ob_start();
        if ( $query->have_posts() ) {
            woocommerce_product_loop_start();
            while ( $query->have_posts() ) {
                $query->the_post();
                wc_get_template_part( 'content', 'product' );
            }
            woocommerce_product_loop_end();
        } else {
            echo '<p class="woocommerce-info">' . esc_html__( 'No products found matching your selection.', 'fashion-variation-swatches' ) . '</p>';
        }
        $content = ob_get_clean();

        wp_reset_postdata();

        wp_send_json_success( [
            'content' => $content,
            'found_posts' => $query->found_posts,
            'max_pages' => $query->max_num_pages,
        ] );
    }
} 