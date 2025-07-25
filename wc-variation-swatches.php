<?php
/**
 * Plugin Name: Fashion Variation Swatches for WooCommerce and Elementor
 * Plugin URI: https://github.com/uditha-mahindarathna/fashion-variation-swatches
 * Description: Add beautiful size and color variation swatches to WooCommerce products with Elementor widget support. Perfect for fashion and apparel stores. Compatible with WooCommerce HPOS.
 * Version: 1.0.3
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
define( 'FASHION_VARIATION_SWATCHES_VERSION', '1.0.3' );
define( 'FASHION_VARIATION_SWATCHES_PLUGIN_FILE', __FILE__ );

// Use a more robust plugin directory definition
$plugin_dir = plugin_dir_path( __FILE__ );
if ( empty( $plugin_dir ) ) {
    // Fallback to dirname if plugin_dir_path fails
    $plugin_dir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;
}
define( 'FASHION_VARIATION_SWATCHES_PLUGIN_DIR', $plugin_dir );

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
            // Try multiple path construction methods
            $file_paths = [
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
            $elementor_file = FASHION_VARIATION_SWATCHES_PLUGIN_DIR . 'includes/elementor/class-fashion-variation-swatches-elementor.php';
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
            // Try multiple path construction methods
            $file_paths = [
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
                '<p>Plugin Directory: <code>' . esc_html( FASHION_VARIATION_SWATCHES_PLUGIN_DIR ) . '</code></p>' .
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