<?php
/**
 * Cleanup Installation Script
 * 
 * This script helps clean up installation issues and prevents versioned directory problems.
 * Run this script to fix common installation issues.
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
    
    // Define WordPress functions if not available
    if ( ! function_exists( 'esc_html' ) ) {
        function esc_html( $text ) {
            return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
        }
    }
    
    if ( ! function_exists( 'admin_url' ) ) {
        function admin_url( $path = '' ) {
            return '/wp-admin/' . $path;
        }
    }
}

function fashion_variation_swatches_cleanup_installation() {
    echo '<h1>🧹 Fashion Variation Swatches - Installation Cleanup</h1>';
    
    $plugin_dir = __DIR__;
    $plugins_dir = dirname( $plugin_dir );
    $base_plugin_name = 'fashion-variation-swatches-for-woocommerce-elementor';
    
    echo '<h2>📁 Current Installation Status</h2>';
    echo '<p><strong>Current Plugin Directory:</strong> ' . esc_html( $plugin_dir ) . '</p>';
    echo '<p><strong>Plugins Directory:</strong> ' . esc_html( $plugins_dir ) . '</p>';
    
    // Check if we're in a versioned directory
    $current_dir = basename( $plugin_dir );
    $is_versioned = preg_match( '/^' . preg_quote( $base_plugin_name, '/' ) . '-v\d+\.\d+\.\d+/', $current_dir );
    
    echo '<p><strong>Current Directory Name:</strong> ' . esc_html( $current_dir ) . '</p>';
    echo '<p><strong>Is Versioned Directory:</strong> ' . ( $is_versioned ? '❌ Yes (Problem)' : '✅ No (Good)' ) . '</p>';
    
    if ( $is_versioned ) {
        echo '<div style="background: #fff3cd; border: 1px solid #ffc107; padding: 15px; margin: 10px 0; border-radius: 5px;">';
        echo '<h3>⚠️ Versioned Directory Detected</h3>';
        echo '<p>You are currently in a versioned directory. This is likely causing the activation issues.</p>';
        echo '</div>';
    }
    
    // Check for ZIP files
    echo '<h2>📦 ZIP Files Check</h2>';
    $zip_files = glob( $plugins_dir . '/*.zip' );
    $plugin_zip_files = array_filter( $zip_files, function( $file ) use ( $base_plugin_name ) {
        return strpos( basename( $file ), $base_plugin_name ) !== false;
    } );
    
    if ( empty( $plugin_zip_files ) ) {
        echo '<p>✅ No plugin ZIP files found in plugins directory.</p>';
    } else {
        echo '<p>❌ Found plugin ZIP files that should be removed:</p>';
        echo '<ul>';
        foreach ( $plugin_zip_files as $zip_file ) {
            echo '<li>' . esc_html( basename( $zip_file ) ) . '</li>';
        }
        echo '</ul>';
        
        echo '<div style="background: #f8d7da; border: 1px solid #dc3545; padding: 15px; margin: 10px 0; border-radius: 5px;">';
        echo '<h3>🚨 Action Required</h3>';
        echo '<p>Please remove these ZIP files to prevent future extraction issues:</p>';
        echo '<ul>';
        foreach ( $plugin_zip_files as $zip_file ) {
            echo '<li><code>' . esc_html( $zip_file ) . '</code></li>';
        }
        echo '</ul>';
        echo '</div>';
    }
    
    // Check for other versioned directories
    echo '<h2>🔍 Other Versioned Directories</h2>';
    $all_dirs = glob( $plugins_dir . '/*', GLOB_ONLYDIR );
    $versioned_dirs = array_filter( $all_dirs, function( $dir ) use ( $base_plugin_name ) {
        $dir_name = basename( $dir );
        return preg_match( '/^' . preg_quote( $base_plugin_name, '/' ) . '-v\d+\.\d+\.\d+/', $dir_name );
    } );
    
    if ( empty( $versioned_dirs ) ) {
        echo '<p>✅ No other versioned directories found.</p>';
    } else {
        echo '<p>❌ Found other versioned directories:</p>';
        echo '<ul>';
        foreach ( $versioned_dirs as $dir ) {
            echo '<li>' . esc_html( basename( $dir ) ) . '</li>';
        }
        echo '</ul>';
        
        echo '<div style="background: #f8d7da; border: 1px solid #dc3545; padding: 15px; margin: 10px 0; border-radius: 5px;">';
        echo '<h3>🚨 Action Required</h3>';
        echo '<p>These directories should be removed to prevent conflicts:</p>';
        echo '<ul>';
        foreach ( $versioned_dirs as $dir ) {
            echo '<li><code>' . esc_html( $dir ) . '</code></li>';
        }
        echo '</ul>';
        echo '</div>';
    }
    
    // Check if main plugin directory exists
    echo '<h2>📂 Main Plugin Directory</h2>';
    $main_plugin_dir = $plugins_dir . '/' . $base_plugin_name;
    echo '<p><strong>Main Plugin Directory:</strong> ' . esc_html( $main_plugin_dir ) . '</p>';
    echo '<p><strong>Exists:</strong> ' . ( is_dir( $main_plugin_dir ) ? '✅ Yes' : '❌ No' ) . '</p>';
    
    if ( is_dir( $main_plugin_dir ) ) {
        $main_includes_dir = $main_plugin_dir . '/includes';
        echo '<p><strong>Includes Directory:</strong> ' . ( is_dir( $main_includes_dir ) ? '✅ Yes' : '❌ No' ) . '</p>';
        
        if ( is_dir( $main_includes_dir ) ) {
            $required_files = [
                'class-fashion-variation-swatches-core.php',
                'class-fashion-variation-swatches-admin.php',
                'class-fashion-variation-swatches-frontend.php',
                'class-fashion-variation-swatches-attributes.php',
                'class-fashion-variation-swatches-shop-filters.php',
            ];
            
            echo '<p><strong>Required Files:</strong></p>';
            echo '<ul>';
            foreach ( $required_files as $file ) {
                $file_path = $main_includes_dir . '/' . $file;
                echo '<li>' . esc_html( $file ) . ': ' . ( file_exists( $file_path ) ? '✅ Yes' : '❌ No' ) . '</li>';
            }
            echo '</ul>';
        }
    }
    
    // Recommendations
    echo '<h2>💡 Recommendations</h2>';
    
    if ( $is_versioned ) {
        echo '<div style="background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; margin: 10px 0; border-radius: 5px;">';
        echo '<h3>🔧 Fix Versioned Directory Issue</h3>';
        echo '<ol>';
        echo '<li>Deactivate the plugin if it\'s currently active</li>';
        echo '<li>Delete the current versioned directory: <code>' . esc_html( $plugin_dir ) . '</code></li>';
        echo '<li>Remove any ZIP files from the plugins directory</li>';
        echo '<li>Install the plugin properly from the main plugin directory</li>';
        echo '</ol>';
        echo '</div>';
    }
    
    if ( ! empty( $plugin_zip_files ) ) {
        echo '<div style="background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; margin: 10px 0; border-radius: 5px;">';
        echo '<h3>🗑️ Remove ZIP Files</h3>';
        echo '<p>Remove these ZIP files to prevent future extraction issues:</p>';
        echo '<ul>';
        foreach ( $plugin_zip_files as $zip_file ) {
            echo '<li><code>' . esc_html( $zip_file ) . '</code></li>';
        }
        echo '</ul>';
        echo '</div>';
    }
    
    if ( ! empty( $versioned_dirs ) ) {
        echo '<div style="background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; margin: 10px 0; border-radius: 5px;">';
        echo '<h3>🗑️ Remove Versioned Directories</h3>';
        echo '<p>Remove these versioned directories to prevent conflicts:</p>';
        echo '<ul>';
        foreach ( $versioned_dirs as $dir ) {
            echo '<li><code>' . esc_html( $dir ) . '</code></li>';
        }
        echo '</ul>';
        echo '</div>';
    }
    
    // System information
    echo '<h2>💻 System Information</h2>';
    echo '<p><strong>PHP Version:</strong> ' . esc_html( PHP_VERSION ) . '</p>';
    echo '<p><strong>WordPress Version:</strong> ' . esc_html( get_bloginfo( 'version' ) ) . '</p>';
    echo '<p><strong>Current Working Directory:</strong> ' . esc_html( getcwd() ) . '</p>';
    
    echo '<h2>🔗 Useful Links</h2>';
    echo '<p><a href="' . admin_url( 'plugins.php' ) . '">WordPress Plugins Page</a></p>';
    echo '<p><a href="' . admin_url( 'plugin-install.php' ) . '">Install Plugins</a></p>';
}

// Run cleanup
fashion_variation_swatches_cleanup_installation(); 