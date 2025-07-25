<?php
/**
 * Plugin Diagnostics Script
 * 
 * This script helps diagnose installation issues with the Fashion Variation Swatches plugin.
 * Run this script to check if all required files are present and accessible.
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

function fashion_variation_swatches_run_diagnostics() {
    echo '<h1>🔍 Fashion Variation Swatches - Plugin Diagnostics</h1>';
    
    // Check if main plugin file exists
    $main_file = __DIR__ . '/wc-variation-swatches.php';
    echo '<h2>📁 Main Plugin File</h2>';
    echo '<p><strong>File:</strong> ' . esc_html( $main_file ) . '</p>';
    echo '<p><strong>Exists:</strong> ' . ( file_exists( $main_file ) ? '✅ Yes' : '❌ No' ) . '</p>';
    echo '<p><strong>Readable:</strong> ' . ( is_readable( $main_file ) ? '✅ Yes' : '❌ No' ) . '</p>';
    
    if ( ! file_exists( $main_file ) ) {
        echo '<div style="background: #ffebee; border: 1px solid #f44336; padding: 15px; margin: 10px 0; border-radius: 5px;">';
        echo '<h3>❌ Critical Error</h3>';
        echo '<p>The main plugin file is missing. This indicates a serious installation problem.</p>';
        echo '</div>';
        return;
    }
    
    // Check required files
    $required_files = [
        'includes/class-fashion-variation-swatches-core.php',
        'includes/class-fashion-variation-swatches-admin.php',
        'includes/class-fashion-variation-swatches-frontend.php',
        'includes/class-fashion-variation-swatches-attributes.php',
        'includes/class-fashion-variation-swatches-shop-filters.php',
    ];
    
    echo '<h2>📋 Required Files Check</h2>';
    $missing_files = [];
    $all_files_exist = true;
    
    foreach ( $required_files as $file ) {
        $file_path = FASHION_VARIATION_SWATCHES_PLUGIN_DIR . $file;
        $exists = file_exists( $file_path );
        $readable = is_readable( $file_path );
        
        echo '<div style="border: 1px solid #ddd; padding: 10px; margin: 5px 0; border-radius: 5px;">';
        echo '<p><strong>File:</strong> ' . esc_html( $file ) . '</p>';
        echo '<p><strong>Full Path:</strong> ' . esc_html( $file_path ) . '</p>';
        echo '<p><strong>Exists:</strong> ' . ( $exists ? '✅ Yes' : '❌ No' ) . '</p>';
        echo '<p><strong>Readable:</strong> ' . ( $readable ? '✅ Yes' : '❌ No' ) . '</p>';
        echo '</div>';
        
        if ( ! $exists ) {
            $missing_files[] = $file;
            $all_files_exist = false;
        }
    }
    
    // Check includes directory
    echo '<h2>📂 Directory Structure</h2>';
    $includes_dir = FASHION_VARIATION_SWATCHES_PLUGIN_DIR . 'includes';
    echo '<p><strong>Includes Directory:</strong> ' . esc_html( $includes_dir ) . '</p>';
    echo '<p><strong>Exists:</strong> ' . ( is_dir( $includes_dir ) ? '✅ Yes' : '❌ No' ) . '</p>';
    echo '<p><strong>Readable:</strong> ' . ( is_readable( $includes_dir ) ? '✅ Yes' : '❌ No' ) . '</p>';
    
    if ( is_dir( $includes_dir ) ) {
        $files_in_includes = scandir( $includes_dir );
        echo '<p><strong>Files in includes directory:</strong></p>';
        echo '<ul>';
        foreach ( $files_in_includes as $file ) {
            if ( $file !== '.' && $file !== '..' ) {
                echo '<li>' . esc_html( $file ) . '</li>';
            }
        }
        echo '</ul>';
    }
    
    // Check Elementor integration
    echo '<h2>🎨 Elementor Integration</h2>';
    $elementor_file = FASHION_VARIATION_SWATCHES_PLUGIN_DIR . 'includes/elementor/class-fashion-variation-swatches-elementor.php';
    echo '<p><strong>Elementor File:</strong> ' . esc_html( $elementor_file ) . '</p>';
    echo '<p><strong>Exists:</strong> ' . ( file_exists( $elementor_file ) ? '✅ Yes' : '❌ No' ) . '</p>';
    
    // Check assets directory
    echo '<h2>🎨 Assets Directory</h2>';
    $assets_dir = FASHION_VARIATION_SWATCHES_PLUGIN_DIR . 'assets';
    echo '<p><strong>Assets Directory:</strong> ' . esc_html( $assets_dir ) . '</p>';
    echo '<p><strong>Exists:</strong> ' . ( is_dir( $assets_dir ) ? '✅ Yes' : '❌ No' ) . '</p>';
    
    // Summary
    echo '<h2>📊 Summary</h2>';
    if ( $all_files_exist ) {
        echo '<div style="background: #e8f5e8; border: 1px solid #4caf50; padding: 15px; margin: 10px 0; border-radius: 5px;">';
        echo '<h3>✅ Plugin Integrity Check Passed</h3>';
        echo '<p>All required files are present and accessible. The plugin should work correctly.</p>';
        echo '</div>';
    } else {
        echo '<div style="background: #ffebee; border: 1px solid #f44336; padding: 15px; margin: 10px 0; border-radius: 5px;">';
        echo '<h3>❌ Plugin Integrity Check Failed</h3>';
        echo '<p>The following required files are missing:</p>';
        echo '<ul>';
        foreach ( $missing_files as $file ) {
            echo '<li>' . esc_html( $file ) . '</li>';
        }
        echo '</ul>';
        echo '<p><strong>Recommendation:</strong> Please reinstall the plugin or contact support.</p>';
        echo '</div>';
    }
    
    // System information
    echo '<h2>💻 System Information</h2>';
    echo '<p><strong>PHP Version:</strong> ' . esc_html( PHP_VERSION ) . '</p>';
    echo '<p><strong>WordPress Version:</strong> ' . esc_html( get_bloginfo( 'version' ) ) . '</p>';
    echo '<p><strong>Plugin Directory:</strong> ' . esc_html( FASHION_VARIATION_SWATCHES_PLUGIN_DIR ) . '</p>';
    echo '<p><strong>Current Working Directory:</strong> ' . esc_html( getcwd() ) . '</p>';
    
    // File permissions
    echo '<h2>🔐 File Permissions</h2>';
    $plugin_dir_perms = substr( sprintf( '%o', fileperms( __DIR__ ) ), -4 );
    echo '<p><strong>Plugin Directory Permissions:</strong> ' . esc_html( $plugin_dir_perms ) . '</p>';
    
    if ( is_dir( $includes_dir ) ) {
        $includes_perms = substr( sprintf( '%o', fileperms( $includes_dir ) ), -4 );
        echo '<p><strong>Includes Directory Permissions:</strong> ' . esc_html( $includes_perms ) . '</p>';
    }
}

// Run diagnostics
fashion_variation_swatches_run_diagnostics(); 