<?php
/**
 * Plugin Name:       Fashion Variation Swatches for WooCommerce and Elementor
 * Plugin URI:        https://github.com/udi17live/fashion-variation-swatches-for-woocommerce-elementor
 * Description:       A beautiful and professional WordPress plugin that transforms WooCommerce product variations into stunning, user-friendly swatches.
 * Version:           1.0.6
 * Author:            Uditha Mahindarathna
 * Author URI:        https://github.com/udi17live
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       fashion-variation-swatches
 * Domain Path:       /languages
 *
 * WC requires at least: 5.0
 * WC tested up to: 8.0
 * WC requires at least: 5.0
 * WC tested up to: 8.0
 * Requires at least: 5.0
 * Tested up to: 6.8
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 8.0
 * HPOS Compatible: true
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Check if we are in a versioned directory and handle it.
 * This prevents activation errors when the plugin is installed from a ZIP
 * that extracts into a directory like `plugin-name-v1.0.5`.
 */
function fvs_activation_directory_check() {
    $base_plugin_name = 'fashion-variation-swatches-for-woocommerce-elementor';
    $current_dir_name = basename( dirname( __FILE__ ) );

    if ( $current_dir_name !== $base_plugin_name && strpos( $current_dir_name, $base_plugin_name ) === 0 ) {
        if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
        }

        $correct_dir_link = admin_url( 'plugins.php' );
        $error_message = sprintf(
            // translators: 1: Base plugin name, 2: Current directory name, 3: Link to plugins page.
            __( '<b>Fashion Variation Swatches: Installation Issue Detected.</b><br>The plugin is in the wrong directory: <code>%2$s</code>.<br>Please rename the plugin directory to <code>%1$s</code> and <a href="%3$s">try activating it again</a>.', 'fashion-variation-swatches' ),
            esc_html( $base_plugin_name ),
            esc_html( $current_dir_name ),
            esc_url( $correct_dir_link )
        );

        wp_die( $error_message ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}
register_activation_hook( __FILE__, 'fvs_activation_directory_check' );

/**
 * Plugin activation hook
 */
function fvs_activate_plugin() {
    // Check if WooCommerce is active
    if ( ! class_exists( 'WooCommerce' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        wp_die( 
            esc_html__( 'Fashion Variation Swatches requires WooCommerce to be installed and activated.', 'fashion-variation-swatches' ),
            esc_html__( 'Plugin Activation Error', 'fashion-variation-swatches' ),
            [ 'back_link' => true ]
        );
    }

    // Set default options
    add_option( 'fashion_variation_swatches_size_attribute', 'pa_size' );
    add_option( 'fashion_variation_swatches_color_attribute', 'pa_color' );
    add_option( 'fashion_variation_swatches_size_style', 'circle' );
    add_option( 'fashion_variation_swatches_color_style', 'circle' );
    add_option( 'fashion_variation_swatches_enable_tooltip', 'yes' );
    add_option( 'fashion_variation_swatches_enable_shop_filters', 'yes' );

    // Flush rewrite rules
    flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'fvs_activate_plugin' );

/**
 * Plugin deactivation hook
 */
function fvs_deactivate_plugin() {
    // Flush rewrite rules
    flush_rewrite_rules();
}

register_deactivation_hook( __FILE__, 'fvs_deactivate_plugin' );


/**
 * Define plugin constants for paths and URLs.
 * Using plugin_dir_path() and plugin_dir_url() makes them robust
 * regardless of the installation directory name.
 */
if ( ! defined( 'FVS_PLUGIN_FILE' ) ) {
    define( 'FVS_PLUGIN_FILE', __FILE__ );
}
if ( ! defined( 'FVS_PLUGIN_PATH' ) ) {
    define( 'FVS_PLUGIN_PATH', plugin_dir_path( FVS_PLUGIN_FILE ) );
}
if ( ! defined( 'FVS_PLUGIN_URL' ) ) {
    define( 'FVS_PLUGIN_URL', plugin_dir_url( FVS_PLUGIN_FILE ) );
}
if ( ! defined( 'FVS_PLUGIN_BASENAME' ) ) {
    define( 'FVS_PLUGIN_BASENAME', plugin_basename( FVS_PLUGIN_FILE ) );
}

// Define the constant that the admin class expects
if ( ! defined( 'FASHION_VARIATION_SWATCHES_PLUGIN_BASENAME' ) ) {
    define( 'FASHION_VARIATION_SWATCHES_PLUGIN_BASENAME', FVS_PLUGIN_BASENAME );
}

// Define additional constants that the admin class expects
if ( ! defined( 'FASHION_VARIATION_SWATCHES_PLUGIN_URL' ) ) {
    define( 'FASHION_VARIATION_SWATCHES_PLUGIN_URL', FVS_PLUGIN_URL );
}

if ( ! defined( 'FASHION_VARIATION_SWATCHES_VERSION' ) ) {
    define( 'FASHION_VARIATION_SWATCHES_VERSION', '1.0.6' );
}

// Define plugin directory constant for utility files
if ( ! defined( 'FASHION_VARIATION_SWATCHES_PLUGIN_DIR' ) ) {
    define( 'FASHION_VARIATION_SWATCHES_PLUGIN_DIR', FVS_PLUGIN_PATH );
}


/**
 * Include required files.
 *
 * This function checks for file existence before including them to prevent fatal errors.
 * If a critical file is missing, it will deactivate the plugin and show an admin notice.
 */
function fvs_include_required_files() {
    $required_files = [
        'includes/class-fashion-variation-swatches-core.php',
        'includes/class-fashion-variation-swatches-admin.php',
        'includes/class-fashion-variation-swatches-frontend.php',
        'includes/class-fashion-variation-swatches-attributes.php',
        'includes/class-fashion-variation-swatches-shop-filters.php',
    ];

    foreach ( $required_files as $file ) {
        $path = FVS_PLUGIN_PATH . $file;
        if ( file_exists( $path ) ) {
            require_once $path;
        } else {
            // If a file is missing, deactivate and show an error.
            deactivate_plugins( FVS_PLUGIN_BASENAME );
            add_action( 'admin_notices', function() use ( $file ) {
                $message = sprintf(
                    // translators: %s: file path
                    __( 'Fashion Variation Swatches plugin has been deactivated. A required file is missing: <code>%s</code>. Please reinstall the plugin.', 'fashion-variation-swatches' ),
                    esc_html( $file )
                );
                echo '<div class="error"><p>' . wp_kses_post( $message ) . '</p></div>';
            } );
            return; // Stop including files.
        }
    }
}

add_action( 'plugins_loaded', 'fvs_include_required_files' );

/**
 * Initialize the plugin classes after all files are loaded.
 */
function fvs_init_plugin() {
    // Check if WooCommerce is active
    if ( ! class_exists( 'WooCommerce' ) ) {
        add_action( 'admin_notices', function() {
            echo '<div class="error"><p>' . 
                 esc_html__( 'Fashion Variation Swatches requires WooCommerce to be installed and activated.', 'fashion-variation-swatches' ) . 
                 '</p></div>';
        } );
        return;
    }

    // Initialize the core plugin class
    Fashion_Variation_Swatches_Core::instance();
    
    // Initialize the admin class only in admin area
    if ( is_admin() ) {
        Fashion_Variation_Swatches_Admin::instance();
    }
    
    // Initialize the frontend class
    Fashion_Variation_Swatches_Frontend::instance();
    
    // Initialize the attributes class
    Fashion_Variation_Swatches_Attributes::instance();
    
    // Initialize the shop filters class
    Fashion_Variation_Swatches_Shop_Filters::instance();
}

add_action( 'plugins_loaded', 'fvs_init_plugin', 20 );

/**
 * Declare HPOS (High-Performance Order Storage) compatibility
 */
function fvs_declare_hpos_compatibility() {
    if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
}

add_action( 'before_woocommerce_init', 'fvs_declare_hpos_compatibility' );

