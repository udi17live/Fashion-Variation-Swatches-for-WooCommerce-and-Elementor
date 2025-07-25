<?php
/**
 * Create Proper ZIP File Script
 * 
 * This script creates a properly structured ZIP file that won't create versioned directories
 * when extracted. It ensures the plugin files are at the root of the ZIP, not in a subdirectory.
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function fashion_variation_swatches_create_proper_zip() {
    $plugin_dir = __DIR__;
    $version = '1.0.5'; // Update this version number as needed
    $zip_filename = "fashion-variation-swatches-for-woocommerce-elementor-{$version}.zip";
    $zip_path = dirname( $plugin_dir ) . '/' . $zip_filename;
    
    // Files and directories to include
    $include_patterns = [
        '*.php',
        '*.txt',
        '*.md',
        '*.json',
        '*.css',
        '*.js',
        'includes/*',
        'assets/*',
        'languages/*',
        'elementor/*',
    ];
    
    // Files and directories to exclude
    $exclude_patterns = [
        '.git/*',
        'node_modules/*',
        '*.log',
        '*.tmp',
        '*.zip',
        'package/*',
        'create-proper-zip.php',
        'cleanup-installation.php',
        'plugin-diagnostics.php',
        'verify-installation.php',
        'demo-setup.php',
        'test-hpos-compatibility.php',
    ];
    
    echo '<h1>📦 Creating Proper ZIP File</h1>';
    echo '<p><strong>Plugin Directory:</strong> ' . esc_html( $plugin_dir ) . '</p>';
    echo '<p><strong>ZIP File:</strong> ' . esc_html( $zip_path ) . '</p>';
    echo '<p><strong>Version:</strong> ' . esc_html( $version ) . '</p>';
    
    // Create ZIP file
    $zip = new ZipArchive();
    $result = $zip->open( $zip_path, ZipArchive::CREATE | ZipArchive::OVERWRITE );
    
    if ( $result !== true ) {
        echo '<div style="background: #f8d7da; border: 1px solid #dc3545; padding: 15px; margin: 10px 0; border-radius: 5px;">';
        echo '<h3>❌ Error Creating ZIP File</h3>';
        echo '<p>Failed to create ZIP file. Error code: ' . esc_html( $result ) . '</p>';
        echo '</div>';
        return;
    }
    
    // Function to check if file should be excluded
    $should_exclude = function( $file_path ) use ( $exclude_patterns ) {
        $relative_path = str_replace( __DIR__ . '/', '', $file_path );
        foreach ( $exclude_patterns as $pattern ) {
            if ( fnmatch( $pattern, $relative_path ) ) {
                return true;
            }
        }
        return false;
    };
    
    // Function to check if file should be included
    $should_include = function( $file_path ) use ( $include_patterns ) {
        $relative_path = str_replace( __DIR__ . '/', '', $file_path );
        foreach ( $include_patterns as $pattern ) {
            if ( fnmatch( $pattern, $relative_path ) ) {
                return true;
            }
        }
        return false;
    };
    
    // Add files to ZIP
    $files_added = 0;
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator( $plugin_dir, RecursiveDirectoryIterator::SKIP_DOTS ),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ( $iterator as $file ) {
        $file_path = $file->getPathname();
        
        // Skip if should be excluded
        if ( $should_exclude( $file_path ) ) {
            continue;
        }
        
        // Include if it matches include patterns or is a directory
        if ( $file->isDir() || $should_include( $file_path ) ) {
            $relative_path = str_replace( $plugin_dir . '/', '', $file_path );
            
            if ( $file->isDir() ) {
                $zip->addEmptyDir( $relative_path );
            } else {
                $zip->addFile( $file_path, $relative_path );
                $files_added++;
            }
        }
    }
    
    $zip->close();
    
    echo '<div style="background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; margin: 10px 0; border-radius: 5px;">';
    echo '<h3>✅ ZIP File Created Successfully</h3>';
    echo '<p><strong>Files Added:</strong> ' . esc_html( $files_added ) . '</p>';
    echo '<p><strong>ZIP Size:</strong> ' . esc_html( number_format( filesize( $zip_path ) / 1024, 2 ) ) . ' KB</p>';
    echo '<p><strong>Location:</strong> <code>' . esc_html( $zip_path ) . '</code></p>';
    echo '</div>';
    
    // Verify ZIP structure
    echo '<h2>🔍 Verifying ZIP Structure</h2>';
    $verify_zip = new ZipArchive();
    if ( $verify_zip->open( $zip_path ) === true ) {
        $root_files = [];
        for ( $i = 0; $i < $verify_zip->numFiles; $i++ ) {
            $filename = $verify_zip->getNameIndex( $i );
            if ( strpos( $filename, '/' ) === false ) {
                $root_files[] = $filename;
            }
        }
        $verify_zip->close();
        
        echo '<p><strong>Files at ZIP Root:</strong></p>';
        echo '<ul>';
        foreach ( $root_files as $file ) {
            echo '<li>' . esc_html( $file ) . '</li>';
        }
        echo '</ul>';
        
        // Check for required files
        $required_files = [
            'wc-variation-swatches.php',
            'readme.txt',
            'includes/class-fashion-variation-swatches-core.php',
        ];
        
        echo '<p><strong>Required Files Check:</strong></p>';
        echo '<ul>';
        foreach ( $required_files as $file ) {
            $verify_zip = new ZipArchive();
            $verify_zip->open( $zip_path );
            $exists = $verify_zip->locateName( $file ) !== false;
            $verify_zip->close();
            echo '<li>' . esc_html( $file ) . ': ' . ( $exists ? '✅ Yes' : '❌ No' ) . '</li>';
        }
        echo '</ul>';
    }
    
    echo '<h2>📋 Instructions for Use</h2>';
    echo '<div style="background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; margin: 10px 0; border-radius: 5px;">';
    echo '<h3>✅ Proper Installation Steps</h3>';
    echo '<ol>';
    echo '<li>Upload the ZIP file to WordPress via Plugins > Add New > Upload Plugin</li>';
    echo '<li>Or extract the ZIP file directly to the plugins directory</li>';
    echo '<li>The plugin will be installed in the correct directory structure</li>';
    echo '<li>Activate the plugin from the WordPress admin</li>';
    echo '</ol>';
    echo '</div>';
    
    echo '<h2>⚠️ Important Notes</h2>';
    echo '<ul>';
    echo '<li>This ZIP file is structured correctly and will not create versioned directories</li>';
    echo '<li>Always use this method to create distribution ZIP files</li>';
    echo '<li>Remove any existing ZIP files from the plugins directory before installing</li>';
    echo '<li>If you see versioned directories, use the cleanup script to fix them</li>';
    echo '</ul>';
}

// Run the script
fashion_variation_swatches_create_proper_zip(); 