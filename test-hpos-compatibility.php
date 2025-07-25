<?php
/**
 * HPOS Compatibility Test
 * 
 * This file can be used to test if the plugin is properly declaring HPOS compatibility.
 * You can access this file directly in your browser to see the test results.
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    // If not loaded through WordPress, simulate basic environment
    if ( ! function_exists( 'wp_die' ) ) {
        function wp_die( $message ) {
            echo '<div style="color: red; font-weight: bold;">ERROR: ' . htmlspecialchars( $message ) . '</div>';
            exit;
        }
    }
}

// Check if WooCommerce is active
if ( ! class_exists( 'WooCommerce' ) ) {
    wp_die( 'WooCommerce is not active. Please activate WooCommerce first.' );
}

// Check if HPOS feature controller exists
if ( ! class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
    wp_die( 'WooCommerce HPOS feature controller not found. This might indicate an older version of WooCommerce.' );
}

// Check if the plugin is active
$plugin_file = 'wc-variation-swatches/wc-variation-swatches.php';
if ( ! is_plugin_active( $plugin_file ) ) {
    wp_die( 'Fashion Variation Swatches plugin is not active.' );
}

// Test HPOS compatibility declaration
try {
    // This should work if the plugin properly declares compatibility
    $compatibility_info = \Automattic\WooCommerce\Utilities\FeaturesUtil::get_compatible_plugins_for_feature( 'custom_order_tables' );
    
    $plugin_name = 'wc-variation-swatches/wc-variation-swatches.php';
    $is_compatible = in_array( $plugin_name, $compatibility_info['compatible'] ?? [] );
    
    if ( $is_compatible ) {
        echo '<div style="color: green; font-weight: bold;">✅ SUCCESS: Fashion Variation Swatches is properly declared as HPOS compatible!</div>';
    } else {
        echo '<div style="color: orange; font-weight: bold;">⚠️ WARNING: Fashion Variation Swatches is not found in the compatible plugins list.</div>';
        echo '<div>This might be because the compatibility declaration hasn\'t been triggered yet.</div>';
    }
    
    echo '<h3>HPOS Compatibility Status:</h3>';
    echo '<ul>';
    echo '<li>WooCommerce Version: ' . WC()->version . '</li>';
    echo '<li>Plugin Version: ' . FASHION_VARIATION_SWATCHES_VERSION . '</li>';
    echo '<li>HPOS Feature Available: ✅ Yes</li>';
    echo '<li>Plugin Active: ✅ Yes</li>';
    echo '</ul>';
    
} catch ( Exception $e ) {
    echo '<div style="color: red; font-weight: bold;">❌ ERROR: ' . htmlspecialchars( $e->getMessage() ) . '</div>';
}

// Additional information
echo '<h3>Plugin Information:</h3>';
echo '<ul>';
echo '<li>Plugin File: ' . plugin_dir_path( __FILE__ ) . 'wc-variation-swatches.php</li>';
echo '<li>Plugin URL: ' . plugin_dir_url( __FILE__ ) . '</li>';
echo '<li>Plugin Basename: ' . plugin_basename( __FILE__ ) . '</li>';
echo '</ul>';

echo '<h3>Next Steps:</h3>';
echo '<ol>';
echo '<li>If you see the success message above, the plugin is HPOS compatible.</li>';
echo '<li>You can now enable HPOS in WooCommerce Settings > Advanced > Features.</li>';
echo '<li>Delete this test file after confirming compatibility.</li>';
echo '</ol>'; 