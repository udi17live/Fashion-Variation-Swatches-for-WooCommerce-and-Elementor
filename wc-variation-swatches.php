<?php
/**
 * Plugin Name:       Fashion Variation Swatches for WooCommerce and Elementor
 * Plugin URI:        https://github.com/udi17live/fashion-variation-swatches-for-woocommerce-elementor
 * Description:       A beautiful and professional WordPress plugin that transforms WooCommerce product variations into stunning, user-friendly swatches.
 * Version:           1.0.5
 * Author:            Uditha Mahindarathna
 * Author URI:        https://github.com/udi17live
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       fashion-variation-swatches
 * Domain Path:       /languages
 *
 * WC requires at least: 5.0
 * WC tested up to: 8.0
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

// The rest of your plugin initialization code would go here.

