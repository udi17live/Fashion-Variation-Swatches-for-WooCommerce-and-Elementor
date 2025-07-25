<?php
/**
 * Installation Verification Script
 * 
 * Quick check to verify that the Fashion Variation Swatches plugin is properly installed.
 * Run this script after installation to ensure everything is working correctly.
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

// Define plugin constants if not already defined
if ( ! defined( 'FASHION_VARIATION_SWATCHES_PLUGIN_DIR' ) ) {
    define( 'FASHION_VARIATION_SWATCHES_PLUGIN_DIR', __DIR__ . '/' );
}

function fashion_variation_swatches_verify_installation() {
    echo '<h1>✅ Fashion Variation Swatches - Installation Verification</h1>';
    
    $all_checks_passed = true;
    $results = [];
    
    // Check 1: Main plugin file
    $main_file = __DIR__ . '/wc-variation-swatches.php';
    $results['main_file'] = file_exists( $main_file );
    if ( ! $results['main_file'] ) {
        $all_checks_passed = false;
    }
    
    // Check 2: Required files
    $required_files = [
        'includes/class-fashion-variation-swatches-core.php',
        'includes/class-fashion-variation-swatches-admin.php',
        'includes/class-fashion-variation-swatches-frontend.php',
        'includes/class-fashion-variation-swatches-attributes.php',
        'includes/class-fashion-variation-swatches-shop-filters.php',
    ];
    
    foreach ( $required_files as $file ) {
        $file_path = FASHION_VARIATION_SWATCHES_PLUGIN_DIR . $file;
        $results[ $file ] = file_exists( $file_path );
        if ( ! $results[ $file ] ) {
            $all_checks_passed = false;
        }
    }
    
    // Check 3: Directories
    $required_dirs = [
        'includes/',
        'assets/',
        'languages/',
    ];
    
    foreach ( $required_dirs as $dir ) {
        $dir_path = FASHION_VARIATION_SWATCHES_PLUGIN_DIR . $dir;
        $results[ $dir ] = is_dir( $dir_path );
        if ( ! $results[ $dir ] ) {
            $all_checks_passed = false;
        }
    }
    
    // Check 4: Elementor integration
    $elementor_file = FASHION_VARIATION_SWATCHES_PLUGIN_DIR . 'includes/elementor/class-fashion-variation-swatches-elementor.php';
    $results['elementor_integration'] = file_exists( $elementor_file );
    
    // Display results
    echo '<div style="max-width: 800px; margin: 20px auto; font-family: Arial, sans-serif;">';
    
    // Summary
    if ( $all_checks_passed ) {
        echo '<div style="background: #e8f5e8; border: 2px solid #4caf50; padding: 20px; margin: 20px 0; border-radius: 8px; text-align: center;">';
        echo '<h2 style="color: #2e7d32; margin: 0 0 10px 0;">🎉 Installation Successful!</h2>';
        echo '<p style="margin: 0; font-size: 16px;">All required files are present and the plugin is ready to use.</p>';
        echo '</div>';
    } else {
        echo '<div style="background: #ffebee; border: 2px solid #f44336; padding: 20px; margin: 20px 0; border-radius: 8px; text-align: center;">';
        echo '<h2 style="color: #c62828; margin: 0 0 10px 0;">❌ Installation Issues Detected</h2>';
        echo '<p style="margin: 0; font-size: 16px;">Some required files are missing. Please reinstall the plugin.</p>';
        echo '</div>';
    }
    
    // Detailed results
    echo '<h3>📋 Verification Results</h3>';
    echo '<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">';
    echo '<thead>';
    echo '<tr style="background: #f5f5f5;">';
    echo '<th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Component</th>';
    echo '<th style="padding: 12px; text-align: center; border: 1px solid #ddd;">Status</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    // Main file
    echo '<tr>';
    echo '<td style="padding: 12px; border: 1px solid #ddd;"><strong>Main Plugin File</strong></td>';
    echo '<td style="padding: 12px; text-align: center; border: 1px solid #ddd;">';
    echo $results['main_file'] ? '✅ OK' : '❌ Missing';
    echo '</td>';
    echo '</tr>';
    
    // Required files
    foreach ( $required_files as $file ) {
        echo '<tr>';
        echo '<td style="padding: 12px; border: 1px solid #ddd;">' . esc_html( $file ) . '</td>';
        echo '<td style="padding: 12px; text-align: center; border: 1px solid #ddd;">';
        echo $results[ $file ] ? '✅ OK' : '❌ Missing';
        echo '</td>';
        echo '</tr>';
    }
    
    // Directories
    foreach ( $required_dirs as $dir ) {
        echo '<tr>';
        echo '<td style="padding: 12px; border: 1px solid #ddd;">' . esc_html( $dir ) . '</td>';
        echo '<td style="padding: 12px; text-align: center; border: 1px solid #ddd;">';
        echo $results[ $dir ] ? '✅ OK' : '❌ Missing';
        echo '</td>';
        echo '</tr>';
    }
    
    // Elementor integration
    echo '<tr>';
    echo '<td style="padding: 12px; border: 1px solid #ddd;">Elementor Integration</td>';
    echo '<td style="padding: 12px; text-align: center; border: 1px solid #ddd;">';
    echo $results['elementor_integration'] ? '✅ Available' : '⚠️ Not Found';
    echo '</td>';
    echo '</tr>';
    
    echo '</tbody>';
    echo '</table>';
    
    // Next steps
    echo '<h3>🚀 Next Steps</h3>';
    if ( $all_checks_passed ) {
        echo '<div style="background: #f0f8ff; border: 1px solid #2196f3; padding: 15px; border-radius: 5px;">';
        echo '<h4 style="margin: 0 0 10px 0; color: #1976d2;">Your plugin is ready to use!</h4>';
        echo '<ol style="margin: 0; padding-left: 20px;">';
        echo '<li>Go to your WordPress admin panel</li>';
        echo '<li>Navigate to <strong>Plugins</strong> and activate the Fashion Variation Swatches plugin</li>';
        echo '<li>Go to <strong>WooCommerce > Settings > Fashion Swatches</strong> to configure the plugin</li>';
        echo '<li>Create or edit a product with variations to see the swatches in action</li>';
        echo '</ol>';
        echo '</div>';
    } else {
        echo '<div style="background: #fff3e0; border: 1px solid #ff9800; padding: 15px; border-radius: 5px;">';
        echo '<h4 style="margin: 0 0 10px 0; color: #e65100;">Installation needs attention</h4>';
        echo '<ol style="margin: 0; padding-left: 20px;">';
        echo '<li>Delete the current plugin installation</li>';
        echo '<li>Download a fresh copy of the plugin</li>';
        echo '<li>Reinstall the plugin following the installation guide</li>';
        echo '<li>Run this verification script again</li>';
        echo '</ol>';
        echo '<p style="margin: 10px 0 0 0;"><strong>Need help?</strong> Check the <a href="TROUBLESHOOTING.md" style="color: #1976d2;">Troubleshooting Guide</a> or run the <a href="plugin-diagnostics.php" style="color: #1976d2;">Diagnostic Script</a> for detailed information.</p>';
        echo '</div>';
    }
    
    // Quick links
    echo '<h3>🔗 Quick Links</h3>';
    echo '<div style="display: flex; gap: 10px; flex-wrap: wrap; margin: 20px 0;">';
    echo '<a href="plugin-diagnostics.php" style="background: #2196f3; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">🔍 Run Full Diagnostics</a>';
    echo '<a href="TROUBLESHOOTING.md" style="background: #ff9800; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">📖 Troubleshooting Guide</a>';
    echo '<a href="USER-GUIDE.md" style="background: #4caf50; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">📚 User Guide</a>';
    echo '<a href="QUICK-START.md" style="background: #9c27b0; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">⚡ Quick Start</a>';
    echo '</div>';
    
    echo '</div>';
}

// Run verification
fashion_variation_swatches_verify_installation(); 