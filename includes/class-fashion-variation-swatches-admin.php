<?php
/**
 * Admin functionality class
 *
 * @package fashion_variation_swatches
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Admin class
 */
class Fashion_Variation_Swatches_Admin {

    /**
     * Instance
     *
     * @var Fashion_Variation_Swatches_Admin
     */
    private static $instance = null;

    /**
     * Get instance
     *
     * @return Fashion_Variation_Swatches_Admin
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
        add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
        add_action( 'admin_init', [ $this, 'admin_init' ] );
        add_filter( 'plugin_action_links_' . FASHION_VARIATION_SWATCHES_PLUGIN_BASENAME, [ $this, 'add_action_links' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_submenu_page(
            'woocommerce',
            __( 'Variation Swatches', 'fashion-variation-swatches' ),
            __( 'Variation Swatches', 'fashion-variation-swatches' ),
            'manage_woocommerce',
            'fashion-variation-swatches',
            [ $this, 'admin_page' ]
        );
    }

    /**
     * Admin init
     */
    public function admin_init() {
        register_setting( 'fashion_variation_swatches_settings', 'fashion_variation_swatches_size_style' );
        register_setting( 'fashion_variation_swatches_settings', 'fashion_variation_swatches_color_style' );
        register_setting( 'fashion_variation_swatches_settings', 'fashion_variation_swatches_enable_tooltip' );
        register_setting( 'fashion_variation_swatches_settings', 'fashion_variation_swatches_enable_shop_filters' );
        register_setting( 'fashion_variation_swatches_settings', 'fashion_variation_swatches_size_attribute' );
        register_setting( 'fashion_variation_swatches_settings', 'fashion_variation_swatches_color_attribute' );

        add_settings_section(
            'fashion_variation_swatches_general',
            __( 'General Settings', 'fashion-variation-swatches' ),
            [ $this, 'general_section_callback' ],
            'fashion_variation_swatches_settings'
        );

        add_settings_field(
            'fashion_variation_swatches_size_attribute',
            __( 'Size Attribute', 'fashion-variation-swatches' ),
            [ $this, 'size_attribute_callback' ],
            'fashion_variation_swatches_settings',
            'fashion_variation_swatches_general'
        );

        add_settings_field(
            'fashion_variation_swatches_color_attribute',
            __( 'Color Attribute', 'fashion-variation-swatches' ),
            [ $this, 'color_attribute_callback' ],
            'fashion_variation_swatches_settings',
            'fashion_variation_swatches_general'
        );

        add_settings_field(
            'fashion_variation_swatches_size_style',
            __( 'Size Swatch Style', 'fashion-variation-swatches' ),
            [ $this, 'size_style_callback' ],
            'fashion_variation_swatches_settings',
            'fashion_variation_swatches_general'
        );

        add_settings_field(
            'fashion_variation_swatches_color_style',
            __( 'Color Swatch Style', 'fashion-variation-swatches' ),
            [ $this, 'color_style_callback' ],
            'fashion_variation_swatches_settings',
            'fashion_variation_swatches_general'
        );

        add_settings_field(
            'fashion_variation_swatches_enable_tooltip',
            __( 'Enable Tooltips', 'fashion-variation-swatches' ),
            [ $this, 'enable_tooltip_callback' ],
            'fashion_variation_swatches_settings',
            'fashion_variation_swatches_general'
        );

        add_settings_field(
            'fashion_variation_swatches_enable_shop_filters',
            __( 'Enable Shop Filters', 'fashion-variation-swatches' ),
            [ $this, 'enable_shop_filters_callback' ],
            'fashion_variation_swatches_settings',
            'fashion_variation_swatches_general'
        );
    }

    /**
     * Admin page
     */
    public function admin_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'WooCommerce Variation Swatches Settings', 'fashion-variation-swatches' ); ?></h1>
            
            <div class="fashion-variation-swatches-admin-header">
                <p><?php esc_html_e( 'Configure your variation swatches settings below. You can customize the appearance and behavior of size and color swatches for your WooCommerce products.', 'fashion-variation-swatches' ); ?></p>
            </div>

            <form method="post" action="options.php">
                <?php
                settings_fields( 'fashion_variation_swatches_settings' );
                do_settings_sections( 'fashion_variation_swatches_settings' );
                submit_button();
                ?>
            </form>

            <div class="fashion-variation-swatches-admin-help">
                <h2><?php esc_html_e( 'Help & Documentation', 'fashion-variation-swatches' ); ?></h2>
                <div class="fashion-variation-swatches-help-content">
                    <div class="help-section">
                        <h3><?php esc_html_e( 'Setting up Attributes', 'fashion-variation-swatches' ); ?></h3>
                        <p><?php esc_html_e( 'Go to Products > Attributes to manage your size and color attributes. For color attributes, you can set individual colors for each term.', 'fashion-variation-swatches' ); ?></p>
                    </div>
                    <div class="help-section">
                        <h3><?php esc_html_e( 'Elementor Widgets', 'fashion-variation-swatches' ); ?></h3>
                        <p><?php esc_html_e( 'When editing with Elementor, look for "WC Size Swatches" and "WC Color Swatches" widgets in the WooCommerce category.', 'fashion-variation-swatches' ); ?></p>
                    </div>
                    <div class="help-section">
                        <h3><?php esc_html_e( 'Shop Filters', 'fashion-variation-swatches' ); ?></h3>
                        <p><?php esc_html_e( 'Enable shop filters to add size and color filtering widgets to your shop page sidebar automatically.', 'fashion-variation-swatches' ); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * General section callback
     */
    public function general_section_callback() {
        echo '<p>' . esc_html__( 'Configure the basic settings for your variation swatches.', 'fashion-variation-swatches' ) . '</p>';
    }

    /**
     * Size attribute callback
     */
    public function size_attribute_callback() {
        $value = get_option( 'fashion_variation_swatches_size_attribute', 'pa_size' );
        $attributes = wc_get_attribute_taxonomies();
        ?>
        <select name="fashion_variation_swatches_size_attribute">
            <?php foreach ( $attributes as $attribute ) : ?>
                <option value="pa_<?php echo esc_attr( $attribute->attribute_name ); ?>" <?php selected( $value, 'pa_' . $attribute->attribute_name ); ?>>
                    <?php echo esc_html( $attribute->attribute_label ); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="description"><?php esc_html_e( 'Select which attribute should be used for size swatches.', 'fashion-variation-swatches' ); ?></p>
        <?php
    }

    /**
     * Color attribute callback
     */
    public function color_attribute_callback() {
        $value = get_option( 'fashion_variation_swatches_color_attribute', 'pa_color' );
        $attributes = wc_get_attribute_taxonomies();
        ?>
        <select name="fashion_variation_swatches_color_attribute">
            <?php foreach ( $attributes as $attribute ) : ?>
                <option value="pa_<?php echo esc_attr( $attribute->attribute_name ); ?>" <?php selected( $value, 'pa_' . $attribute->attribute_name ); ?>>
                    <?php echo esc_html( $attribute->attribute_label ); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="description"><?php esc_html_e( 'Select which attribute should be used for color swatches.', 'fashion-variation-swatches' ); ?></p>
        <?php
    }

    /**
     * Size style callback
     */
    public function size_style_callback() {
        $value = get_option( 'fashion_variation_swatches_size_style', 'square' );
        ?>
        <select name="fashion_variation_swatches_size_style">
            <option value="square" <?php selected( $value, 'square' ); ?>><?php esc_html_e( 'Square', 'fashion-variation-swatches' ); ?></option>
            <option value="rounded" <?php selected( $value, 'rounded' ); ?>><?php esc_html_e( 'Rounded', 'fashion-variation-swatches' ); ?></option>
            <option value="circle" <?php selected( $value, 'circle' ); ?>><?php esc_html_e( 'Circle', 'fashion-variation-swatches' ); ?></option>
        </select>
        <p class="description"><?php esc_html_e( 'Choose the visual style for size swatches.', 'fashion-variation-swatches' ); ?></p>
        <?php
    }

    /**
     * Color style callback
     */
    public function color_style_callback() {
        $value = get_option( 'fashion_variation_swatches_color_style', 'circle' );
        ?>
        <select name="fashion_variation_swatches_color_style">
            <option value="square" <?php selected( $value, 'square' ); ?>><?php esc_html_e( 'Square', 'fashion-variation-swatches' ); ?></option>
            <option value="rounded" <?php selected( $value, 'rounded' ); ?>><?php esc_html_e( 'Rounded', 'fashion-variation-swatches' ); ?></option>
            <option value="circle" <?php selected( $value, 'circle' ); ?>><?php esc_html_e( 'Circle', 'fashion-variation-swatches' ); ?></option>
        </select>
        <p class="description"><?php esc_html_e( 'Choose the visual style for color swatches.', 'fashion-variation-swatches' ); ?></p>
        <?php
    }

    /**
     * Enable tooltip callback
     */
    public function enable_tooltip_callback() {
        $value = get_option( 'fashion_variation_swatches_enable_tooltip', 'yes' );
        ?>
        <label>
            <input type="checkbox" name="fashion_variation_swatches_enable_tooltip" value="yes" <?php checked( $value, 'yes' ); ?>>
            <?php esc_html_e( 'Show tooltips on hover', 'fashion-variation-swatches' ); ?>
        </label>
        <p class="description"><?php esc_html_e( 'Display attribute names as tooltips when hovering over swatches.', 'fashion-variation-swatches' ); ?></p>
        <?php
    }

    /**
     * Enable shop filters callback
     */
    public function enable_shop_filters_callback() {
        $value = get_option( 'fashion_variation_swatches_enable_shop_filters', 'yes' );
        ?>
        <label>
            <input type="checkbox" name="fashion_variation_swatches_enable_shop_filters" value="yes" <?php checked( $value, 'yes' ); ?>>
            <?php esc_html_e( 'Enable filtering on shop page', 'fashion-variation-swatches' ); ?>
        </label>
        <p class="description"><?php esc_html_e( 'Add size and color filter widgets to the shop page sidebar.', 'fashion-variation-swatches' ); ?></p>
        <?php
    }

    /**
     * Add action links
     *
     * @param array $links
     * @return array
     */
    public function add_action_links( $links ) {
        $settings_link = '<a href="' . admin_url( 'admin.php?page=fashion-variation-swatches' ) . '">' . __( 'Settings', 'fashion-variation-swatches' ) . '</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }

    /**
     * Enqueue admin assets
     *
     * @param string $hook
     */
    public function enqueue_admin_assets( $hook ) {
        if ( strpos( $hook, 'fashion-variation-swatches' ) !== false ) {
            wp_enqueue_style(
                'fashion-variation-swatches-admin',
                FASHION_VARIATION_SWATCHES_PLUGIN_URL . 'assets/css/admin.css',
                [],
                FASHION_VARIATION_SWATCHES_VERSION
            );
        }
    }
} 