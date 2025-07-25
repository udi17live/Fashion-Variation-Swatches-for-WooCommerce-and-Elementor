<?php
/**
 * Plugin Name: Fashion Variation Swatches for WooCommerce and Elementor
 * Plugin URI: https://github.com/uditha-mahindarathna/fashion-variation-swatches
 * Description: Add beautiful size and color variation swatches to WooCommerce products with Elementor widget support. Perfect for fashion and apparel stores. Compatible with WooCommerce HPOS.
 * Version: 1.0.1
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
define( 'FASHION_VARIATION_SWATCHES_VERSION', '1.0.1' );
define( 'FASHION_VARIATION_SWATCHES_PLUGIN_FILE', __FILE__ );
define( 'FASHION_VARIATION_SWATCHES_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
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
        $this->includes();
        
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
        // Core classes
        require_once FASHION_VARIATION_SWATCHES_PLUGIN_DIR . 'includes/class-fashion-variation-swatches-core.php';
        require_once FASHION_VARIATION_SWATCHES_PLUGIN_DIR . 'includes/class-fashion-variation-swatches-admin.php';
        require_once FASHION_VARIATION_SWATCHES_PLUGIN_DIR . 'includes/class-fashion-variation-swatches-frontend.php';
        require_once FASHION_VARIATION_SWATCHES_PLUGIN_DIR . 'includes/class-fashion-variation-swatches-attributes.php';
        require_once FASHION_VARIATION_SWATCHES_PLUGIN_DIR . 'includes/class-fashion-variation-swatches-shop-filters.php';
        
        // Elementor integration
        if ( defined( 'ELEMENTOR_VERSION' ) ) {
            require_once FASHION_VARIATION_SWATCHES_PLUGIN_DIR . 'includes/elementor/class-fashion-variation-swatches-elementor.php';
        }
    }

    /**
     * Initialize components
     */
    private function init_components() {
        // Initialize core functionality
        Fashion_Variation_Swatches_Core::instance();
        Fashion_Variation_Swatches_Admin::instance();
        Fashion_Variation_Swatches_Frontend::instance();
        Fashion_Variation_Swatches_Attributes::instance();
        Fashion_Variation_Swatches_Shop_Filters::instance();
        
        // Initialize Elementor integration if available
        if ( defined( 'ELEMENTOR_VERSION' ) ) {
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
}

// Initialize plugin
function fashion_variation_swatches() {
    return Fashion_Variation_Swatches::instance();
}

// Start the plugin
fashion_variation_swatches(); 