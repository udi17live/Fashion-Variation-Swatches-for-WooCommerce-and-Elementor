<?php
/**
 * Plugin Packaging Script
 * 
 * This script creates a distributable ZIP file of the Fashion Variation Swatches plugin.
 * Run this script to create a package that can be installed on any WordPress site.
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

// Check if ZipArchive is available
if ( ! class_exists( 'ZipArchive' ) ) {
    wp_die( 'ZipArchive class is not available. Please enable PHP ZIP extension.' );
}

// Plugin packaging function
function fashion_variation_swatches_package_plugin() {
    $plugin_dir = __DIR__;
    $plugin_name = 'fashion-variation-swatches-for-woocommerce-elementor';
    $version = '1.0.1';
    
    // Create package directory
    $package_dir = $plugin_dir . '/package';
    if ( ! is_dir( $package_dir ) ) {
        mkdir( $package_dir, 0755, true );
    }
    
    // Create plugin directory inside package
    $plugin_package_dir = $package_dir . '/' . $plugin_name;
    if ( is_dir( $plugin_package_dir ) ) {
        // Remove existing directory
        function removeDirectory( $dir ) {
            if ( is_dir( $dir ) ) {
                $objects = scandir( $dir );
                foreach ( $objects as $object ) {
                    if ( $object != "." && $object != ".." ) {
                        if ( is_dir( $dir . "/" . $object ) ) {
                            removeDirectory( $dir . "/" . $object );
                        } else {
                            unlink( $dir . "/" . $object );
                        }
                    }
                }
                rmdir( $dir );
            }
        }
        removeDirectory( $plugin_package_dir );
    }
    mkdir( $plugin_package_dir, 0755, true );
    
    // Files to include in the package
    $include_files = [
        // Core files
        'wc-variation-swatches.php',
        'readme.txt',
        'CHANGELOG.md',
        
        // Documentation
        'README.md',
        'USER-GUIDE.md',
        'QUICK-START.md',
        'README-HPOS.md',
        
        // Utility files
        'demo-setup.php',
        'test-hpos-compatibility.php',
        
        // Directories
        'assets/',
        'includes/',
        'languages/',
    ];
    
    // Files to exclude from the package
    $exclude_files = [
        '.git/',
        '.gitignore',
        'package-plugin.php',
        'package/',
        'node_modules/',
        '*.log',
        '*.tmp',
        '.DS_Store',
        'Thumbs.db',
    ];
    
    echo '<h2>📦 Packaging Fashion Variation Swatches Plugin</h2>';
    echo '<p>Creating distributable package...</p>';
    
    $copied_files = 0;
    $copied_dirs = 0;
    
    // Copy files and directories
    foreach ( $include_files as $item ) {
        $source = $plugin_dir . '/' . $item;
        $destination = $plugin_package_dir . '/' . $item;
        
        if ( is_file( $source ) ) {
            // Copy file
            if ( copy( $source, $destination ) ) {
                echo '<p>✅ Copied file: ' . $item . '</p>';
                $copied_files++;
            } else {
                echo '<p>❌ Failed to copy file: ' . $item . '</p>';
            }
        } elseif ( is_dir( $source ) ) {
            // Copy directory recursively
            if ( copyDirectory( $source, $destination, $exclude_files ) ) {
                echo '<p>✅ Copied directory: ' . $item . '</p>';
                $copied_dirs++;
            } else {
                echo '<p>❌ Failed to copy directory: ' . $item . '</p>';
            }
        }
    }
    
    // Create ZIP file
    $zip_filename = $plugin_name . '-v' . $version . '.zip';
    $zip_path = $package_dir . '/' . $zip_filename;
    
    $zip = new ZipArchive();
    if ( $zip->open( $zip_path, ZipArchive::CREATE | ZipArchive::OVERWRITE ) === TRUE ) {
        // Add all files from the plugin package directory
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator( $plugin_package_dir ),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        
        foreach ( $iterator as $file ) {
            if ( ! $file->isDir() ) {
                $filePath = $file->getRealPath();
                $relativePath = substr( $filePath, strlen( $plugin_package_dir ) + 1 );
                $zip->addFile( $filePath, $relativePath );
            }
        }
        
        $zip->close();
        
        echo '<h3>🎉 Package Created Successfully!</h3>';
        echo '<p><strong>Package Details:</strong></p>';
        echo '<ul>';
        echo '<li>📁 Files copied: ' . $copied_files . '</li>';
        echo '<li>📂 Directories copied: ' . $copied_dirs . '</li>';
        echo '<li>📦 ZIP file: ' . $zip_filename . '</li>';
        echo '<li>📏 Package size: ' . formatBytes( filesize( $zip_path ) ) . '</li>';
        echo '</ul>';
        
        echo '<h3>📥 Download Links:</h3>';
        echo '<p><a href="package/' . $zip_filename . '" download style="background: #0073aa; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px;">⬇️ Download Plugin Package</a></p>';
        
        echo '<h3>📋 Installation Instructions:</h3>';
        echo '<ol>';
        echo '<li>Download the ZIP file above</li>';
        echo '<li>Go to your WordPress admin panel</li>';
        echo '<li>Navigate to <strong>Plugins > Add New</strong></li>';
        echo '<li>Click <strong>Upload Plugin</strong></li>';
        echo '<li>Choose the downloaded ZIP file</li>';
        echo '<li>Click <strong>Install Now</strong></li>';
        echo '<li>Activate the plugin</li>';
        echo '</ol>';
        
        echo '<h3>🔗 Quick Links:</h3>';
        echo '<p>';
        echo '<a href="package/' . $zip_filename . '" style="margin-right: 10px;">Download Package</a>';
        echo '<a href="demo-setup.php" style="margin-right: 10px;">Run Demo Setup</a>';
        echo '<a href="test-hpos-compatibility.php">Test HPOS Compatibility</a>';
        echo '</p>';
        
    } else {
        echo '<p>❌ Failed to create ZIP file</p>';
    }
    
    // Clean up temporary directory
    if ( is_dir( $plugin_package_dir ) ) {
        removeDirectory( $plugin_package_dir );
    }
}

// Helper function to copy directory recursively
function copyDirectory( $source, $destination, $exclude_files = [] ) {
    if ( ! is_dir( $source ) ) {
        return false;
    }
    
    if ( ! is_dir( $destination ) ) {
        mkdir( $destination, 0755, true );
    }
    
    $dir = opendir( $source );
    while ( ( $file = readdir( $dir ) ) !== false ) {
        if ( $file == '.' || $file == '..' ) {
            continue;
        }
        
        // Check if file should be excluded
        $exclude = false;
        foreach ( $exclude_files as $pattern ) {
            if ( fnmatch( $pattern, $file ) ) {
                $exclude = true;
                break;
            }
        }
        
        if ( $exclude ) {
            continue;
        }
        
        $source_path = $source . '/' . $file;
        $dest_path = $destination . '/' . $file;
        
        if ( is_dir( $source_path ) ) {
            copyDirectory( $source_path, $dest_path, $exclude_files );
        } else {
            copy( $source_path, $dest_path );
        }
    }
    closedir( $dir );
    
    return true;
}

// Helper function to format bytes
function formatBytes( $bytes, $precision = 2 ) {
    $units = array( 'B', 'KB', 'MB', 'GB', 'TB' );
    
    for ( $i = 0; $bytes > 1024 && $i < count( $units ) - 1; $i++ ) {
        $bytes /= 1024;
    }
    
    return round( $bytes, $precision ) . ' ' . $units[ $i ];
}

// Helper function to remove directory recursively
function removeDirectory( $dir ) {
    if ( is_dir( $dir ) ) {
        $objects = scandir( $dir );
        foreach ( $objects as $object ) {
            if ( $object != "." && $object != ".." ) {
                if ( is_dir( $dir . "/" . $object ) ) {
                    removeDirectory( $dir . "/" . $object );
                } else {
                    unlink( $dir . "/" . $object );
                }
            }
        }
        rmdir( $dir );
    }
}

// Run the packaging
fashion_variation_swatches_package_plugin(); 