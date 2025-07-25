<?php
/**
 * Plugin Name: Fashion Variation Swatches for WooCommerce and Elementor
 * Plugin URI: https://github.com/uditha-mahindarathna/fashion-variation-swatches
 * Description: Add beautiful size and color variation swatches to WooCommerce products with Elementor widget support. Perfect for fashion and apparel stores. Compatible with WooCommerce HPOS.
 * Version: 1.0.5
 * Author: Uditha Mahindarathna
 * Author URI: mailto:melan.udi@gmail.com
 * Text Domain: fashion-variation-swatches
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.8
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 10.0
 * Elementor tested up to: 3.25.0
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * 
 * @package fashion_variation_swatches
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants
define( 'FASHION_VARIATION_SWATCHES_VERSION', '1.0.5' );
define( 'FASHION_VARIATION_SWATCHES_PLUGIN_FILE', __FILE__ );

// Use a more robust plugin directory definition
$plugin_dir = plugin_dir_path( __FILE__ );
if ( empty( $plugin_dir ) ) {
    // Fallback to dirname if plugin_dir_path fails
    $plugin_dir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;
}
define( 'FASHION_VARIATION_SWATCHES_PLUGIN_DIR', $plugin_dir );

/**
 * Get the actual plugin directory path, handling versioned directory names
 *
 * @return string
 */
function fashion_variation_swatches_get_plugin_dir() {
    $plugin_dir = FASHION_VARIATION_SWATCHES_PLUGIN_DIR;
    
    // Log the current directory for debugging
    error_log( 'Fashion Variation Swatches - Current directory: ' . dirname( __FILE__ ) );
    error_log( 'Fashion Variation Swatches - Plugin directory: ' . $plugin_dir );
    
    // Check if the includes directory exists in the current plugin directory
    $includes_dir = $plugin_dir . 'includes/';
    if ( is_dir( $includes_dir ) ) {
        error_log( 'Fashion Variation Swatches - Includes directory found at: ' . $includes_dir );
        return $plugin_dir;
    }
    
    // If not found, try alternative paths
    $base_plugin_name = 'fashion-variation-swatches-for-woocommerce-elementor';
    $current_dir = basename( dirname( __FILE__ ) );
    
    // Check if we're in a versioned directory (e.g., -v1.0.5)
    if ( preg_match( '/^' . preg_quote( $base_plugin_name, '/' ) . '-v\d+\.\d+\.\d+/', $current_dir ) ) {
        // We're in a versioned directory, try to find the actual plugin directory
        $parent_dir = dirname( $plugin_dir ) . '/';
        $actual_plugin_dir = $parent_dir . $base_plugin_name . '/';
        if ( is_dir( $actual_plugin_dir ) ) {
            error_log( 'Fashion Variation Swatches - Found actual plugin directory at: ' . $actual_plugin_dir );
            return $actual_plugin_dir;
        }
    }
    
    // Try parent directory
    $parent_dir = dirname( $plugin_dir ) . '/';
    $parent_includes_dir = $parent_dir . 'includes/';
    if ( is_dir( $parent_includes_dir ) ) {
        error_log( 'Fashion Variation Swatches - Found includes directory in parent: ' . $parent_includes_dir );
        return $parent_dir;
    }
    
    // Try to find the plugin directory by searching in the plugins directory
    $wp_plugins_dir = WP_PLUGIN_DIR . '/';
    $actual_plugin_dir = $wp_plugins_dir . $base_plugin_name . '/';
    if ( is_dir( $actual_plugin_dir ) && is_dir( $actual_plugin_dir . 'includes/' ) ) {
        error_log( 'Fashion Variation Swatches - Found plugin directory in wp-content/plugins: ' . $actual_plugin_dir );
        return $actual_plugin_dir;
    }
    
    error_log( 'Fashion Variation Swatches - Using default plugin directory: ' . $plugin_dir );
    return $plugin_dir;
}

define( 'FASHION_VARIATION_SWATCHES_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'FASHION_VARIATION_SWATCHES_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Main plugin class
 */
final class Fashion_Variation_Swatches {

    /**
     * Plugin instance
     *
     * @var Fashion_Variation_Swatches
     */
    private static $instance = null;

    /**
     * Get plugin instance
     *
     * @return Fashion_Variation_Swatches
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
        add_action( 'plugins_loaded', [ $this, 'init' ] );
        add_action( 'init', [ $this, 'load_textdomain' ] );
        
        // Activation/Deactivation hooks
        register_activation_hook( __FILE__, [ $this, 'activate' ] );
        register_deactivation_hook( __FILE__, [ $this, 'deactivate' ] );
        
        // Declare HPOS compatibility
        add_action( 'before_woocommerce_init', [ $this, 'declare_hpos_compatibility' ] );
    }

    /**
     * Initialize plugin
     */
    public function init() {
        // Check dependencies
        if ( ! $this->check_dependencies() ) {
            return;
        }

        // Include required files
        if ( ! $this->includes() ) {
            return;
        }
        
        // Initialize components
        $this->init_components();
    }

    /**
     * Check plugin dependencies
     *
     * @return bool
     */
    private function check_dependencies() {
        $notices = [];

        // Check WooCommerce
        if ( ! class_exists( 'WooCommerce' ) ) {
            $notices[] = __( 'Fashion Variation Swatches requires WooCommerce to be installed and active.', 'fashion-variation-swatches' );
        }

        // Check PHP version
        if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
            $notices[] = sprintf( 
                __( 'Fashion Variation Swatches requires PHP version 7.4 or higher. You are running version %s.', 'fashion-variation-swatches' ),
                PHP_VERSION 
            );
        }

        if ( ! empty( $notices ) ) {
            add_action( 'admin_notices', function() use ( $notices ) {
                foreach ( $notices as $notice ) {
                    echo '<div class="notice notice-error"><p>' . esc_html( $notice ) . '</p></div>';
                }
            });
            return false;
        }

        return true;
    }

    /**
     * Include required files
     */
    private function includes() {
        // Debug: Log the plugin directory path
        error_log( 'Fashion Variation Swatches - Includes function - Plugin Directory: ' . FASHION_VARIATION_SWATCHES_PLUGIN_DIR );
        
        // Core classes
        $required_files = [
            'includes/class-fashion-variation-swatches-core.php',
            'includes/class-fashion-variation-swatches-admin.php',
            'includes/class-fashion-variation-swatches-frontend.php',
            'includes/class-fashion-variation-swatches-attributes.php',
            'includes/class-fashion-variation-swatches-shop-filters.php',
        ];

        foreach ( $required_files as $file ) {
            // Get the actual plugin directory path
            $actual_plugin_dir = fashion_variation_swatches_get_plugin_dir();
            
            // Try multiple path construction methods
            $file_paths = [
                $actual_plugin_dir . $file,
                FASHION_VARIATION_SWATCHES_PLUGIN_DIR . $file,
                dirname( __FILE__ ) . DIRECTORY_SEPARATOR . $file,
                dirname( __FILE__ ) . '/' . $file,
            ];
            
            $file_found = false;
            $actual_path = '';
            
            foreach ( $file_paths as $file_path ) {
                error_log( 'Fashion Variation Swatches - Includes function - Trying path: ' . $file_path . ' - Exists: ' . ( file_exists( $file_path ) ? 'Yes' : 'No' ) );
                
                if ( file_exists( $file_path ) ) {
                    $file_found = true;
                    $actual_path = $file_path;
                    break;
                }
            }
            
            if ( $file_found ) {
                require_once $actual_path;
            } else {
                // Log error and deactivate plugin if critical files are missing
                error_log( 'Fashion Variation Swatches: Missing required file: ' . $file . ' - Tried paths: ' . implode( ', ', $file_paths ) );
                add_action( 'admin_notices', function() use ( $file ) {
                    echo '<div class="notice notice-error"><p>';
                    echo '<strong>Fashion Variation Swatches Error:</strong> ';
                    echo 'Required file is missing: <code>' . esc_html( $file ) . '</code>. ';
                    echo 'Please reinstall the plugin or contact support.';
                    echo '</p></div>';
                });
                return false;
            }
        }
        
        // Elementor integration
        if ( defined( 'ELEMENTOR_VERSION' ) ) {
            $actual_plugin_dir = fashion_variation_swatches_get_plugin_dir();
            $elementor_file = $actual_plugin_dir . 'includes/elementor/class-fashion-variation-swatches-elementor.php';
            if ( file_exists( $elementor_file ) ) {
                require_once $elementor_file;
            }
        }

        return true;
    }

    /**
     * Initialize components
     */
    private function init_components() {
        // Initialize core functionality
        if ( class_exists( 'Fashion_Variation_Swatches_Core' ) ) {
            Fashion_Variation_Swatches_Core::instance();
        }
        
        if ( class_exists( 'Fashion_Variation_Swatches_Admin' ) ) {
            Fashion_Variation_Swatches_Admin::instance();
        }
        
        if ( class_exists( 'Fashion_Variation_Swatches_Frontend' ) ) {
            Fashion_Variation_Swatches_Frontend::instance();
        }
        
        if ( class_exists( 'Fashion_Variation_Swatches_Attributes' ) ) {
            Fashion_Variation_Swatches_Attributes::instance();
        }
        
        if ( class_exists( 'Fashion_Variation_Swatches_Shop_Filters' ) ) {
            Fashion_Variation_Swatches_Shop_Filters::instance();
        }
        
        // Initialize Elementor integration if available
        if ( defined( 'ELEMENTOR_VERSION' ) && class_exists( 'Fashion_Variation_Swatches_Elementor' ) ) {
            Fashion_Variation_Swatches_Elementor::instance();
        }
    }

    /**
     * Load plugin textdomain
     */
    public function load_textdomain() {
        load_plugin_textdomain( 'fashion-variation-swatches', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }

    /**
     * Plugin activation
     */
    public function activate() {
        // Debug: Log the plugin directory path
        error_log( 'Fashion Variation Swatches - Plugin Directory: ' . FASHION_VARIATION_SWATCHES_PLUGIN_DIR );
        
        // Check if we're in a versioned directory and provide guidance
        $current_dir = basename( dirname( __FILE__ ) );
        $base_plugin_name = 'fashion-variation-swatches-for-woocommerce-elementor';
        
        if ( preg_match( '/^' . preg_quote( $base_plugin_name, '/' ) . '-v\d+\.\d+\.\d+/', $current_dir ) ) {
            // We're in a versioned directory, this is likely an extraction issue
            deactivate_plugins( plugin_basename( __FILE__ ) );
            wp_die(
                '<h1>Plugin Installation Issue Detected</h1>' .
                '<p><strong>Fashion Variation Swatches</strong> appears to be installed in a versioned directory: <code>' . esc_html( $current_dir ) . '</code></p>' .
                '<p>This typically happens when ZIP files are extracted incorrectly. To fix this:</p>' .
                '<ol>' .
                '<li>Deactivate and delete the current plugin installation</li>' .
                '<li>Remove any ZIP files from the plugins directory</li>' .
                '<li>Reinstall the plugin properly</li>' .
                '</ol>' .
                '<p><strong>Current Directory:</strong> <code>' . esc_html( dirname( __FILE__ ) ) . '</code></p>' .
                '<p><strong>Expected Directory:</strong> <code>' . esc_html( dirname( dirname( __FILE__ ) ) . '/' . $base_plugin_name ) . '</code></p>' .
                '<p><a href="' . admin_url( 'plugins.php' ) . '">Return to Plugins page</a></p>'
            );
        }
        
        // Check if all required files exist
        $required_files = [
            'includes/class-fashion-variation-swatches-core.php',
            'includes/class-fashion-variation-swatches-admin.php',
            'includes/class-fashion-variation-swatches-frontend.php',
            'includes/class-fashion-variation-swatches-attributes.php',
            'includes/class-fashion-variation-swatches-shop-filters.php',
        ];

        $missing_files = [];
        foreach ( $required_files as $file ) {
            // Get the actual plugin directory path
            $actual_plugin_dir = fashion_variation_swatches_get_plugin_dir();
            
            // Try multiple path construction methods
            $file_paths = [
                $actual_plugin_dir . $file,
                FASHION_VARIATION_SWATCHES_PLUGIN_DIR . $file,
                dirname( __FILE__ ) . DIRECTORY_SEPARATOR . $file,
                dirname( __FILE__ ) . '/' . $file,
            ];
            
            $file_found = false;
            
            foreach ( $file_paths as $full_path ) {
                error_log( 'Fashion Variation Swatches - Checking file: ' . $full_path . ' - Exists: ' . ( file_exists( $full_path ) ? 'Yes' : 'No' ) );
                
                if ( file_exists( $full_path ) ) {
                    $file_found = true;
                    break;
                }
            }
            
            if ( ! $file_found ) {
                $missing_files[] = $file;
            }
        }

        if ( ! empty( $missing_files ) ) {
            // Deactivate plugin if critical files are missing
            deactivate_plugins( plugin_basename( __FILE__ ) );
            wp_die(
                '<h1>Plugin Activation Error</h1>' .
                '<p><strong>Fashion Variation Swatches</strong> could not be activated because the following required files are missing:</p>' .
                '<ul><li>' . implode( '</li><li>', array_map( 'esc_html', $missing_files ) ) . '</li></ul>' .
                '<p><strong>Plugin Directory:</strong> <code>' . esc_html( FASHION_VARIATION_SWATCHES_PLUGIN_DIR ) . '</code></p>' .
                '<p><strong>Actual Plugin Directory:</strong> <code>' . esc_html( fashion_variation_swatches_get_plugin_dir() ) . '</code></p>' .
                '<p><strong>Current File:</strong> <code>' . esc_html( __FILE__ ) . '</code></p>' .
                '<p>Please reinstall the plugin or contact support.</p>' .
                '<p><a href="' . admin_url( 'plugins.php' ) . '">Return to Plugins page</a></p>'
            );
        }

        // Create database tables if needed
        $this->create_tables();
        
        // Set default options
        $this->set_default_options();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Create database tables
     */
    private function create_tables() {
        // Add any custom tables if needed in future versions
    }

    /**
     * Set default options
     */
    private function set_default_options() {
        $defaults = [
            'fashion_variation_swatches_size_style' => 'square',
            'fashion_variation_swatches_color_style' => 'circle',
            'fashion_variation_swatches_enable_tooltip' => 'yes',
            'fashion_variation_swatches_enable_shop_filters' => 'yes',
            'fashion_variation_swatches_size_attribute' => 'pa_size',
            'fashion_variation_swatches_color_attribute' => 'pa_color',
        ];

        foreach ( $defaults as $option => $value ) {
            if ( false === get_option( $option ) ) {
                add_option( $option, $value );
            }
        }
    }

    /**
     * Declare HPOS (High-Performance Order Storage) compatibility
     */
    public function declare_hpos_compatibility() {
        if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
        }
    }

    /**
     * Diagnostic function to check plugin integrity
     */
    public static function run_diagnostics() {
        $required_files = [
            'includes/class-fashion-variation-swatches-core.php',
            'includes/class-fashion-variation-swatches-admin.php',
            'includes/class-fashion-variation-swatches-frontend.php',
            'includes/class-fashion-variation-swatches-attributes.php',
            'includes/class-fashion-variation-swatches-shop-filters.php',
        ];

        $results = [];
        foreach ( $required_files as $file ) {
            $file_path = FASHION_VARIATION_SWATCHES_PLUGIN_DIR . $file;
            $results[ $file ] = [
                'exists' => file_exists( $file_path ),
                'readable' => is_readable( $file_path ),
                'path' => $file_path,
            ];
        }

        return $results;
    }
}

// Initialize plugin
function fashion_variation_swatches() {
    return Fashion_Variation_Swatches::instance();
}

// Start the plugin
fashion_variation_swatches(); 