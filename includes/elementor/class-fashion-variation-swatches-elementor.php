<?php
/**
 * Elementor integration class
 *
 * @package fashion_variation_swatches
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Elementor integration class
 */
class Fashion_Variation_Swatches_Elementor {

    /**
     * Instance
     *
     * @var Fashion_Variation_Swatches_Elementor
     */
    private static $instance = null;

    /**
     * Get instance
     *
     * @return Fashion_Variation_Swatches_Elementor
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
        // Register widgets
        add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
        
        // Add widget category
        add_action( 'elementor/elements/categories_registered', [ $this, 'add_widget_category' ] );
        
        // Enqueue editor assets
        add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'enqueue_editor_assets' ] );
    }

    /**
     * Register widgets
     *
     * @param \Elementor\Widgets_Manager $widgets_manager
     */
    public function register_widgets( $widgets_manager ) {
        // Include widget files
        require_once FASHION_VARIATION_SWATCHES_PLUGIN_DIR . 'includes/elementor/widgets/class-wc-size-swatches-widget.php';
        require_once FASHION_VARIATION_SWATCHES_PLUGIN_DIR . 'includes/elementor/widgets/class-wc-color-swatches-widget.php';

        // Register widgets
        $widgets_manager->register( new \Fashion_Size_Swatches_Widget() );
        $widgets_manager->register( new \Fashion_Color_Swatches_Widget() );
    }

    /**
     * Add widget category
     *
     * @param \Elementor\Elements_Manager $elements_manager
     */
    public function add_widget_category( $elements_manager ) {
        $elements_manager->add_category(
            'fashion-variation-swatches',
            [
                'title' => __( 'WC Variation Swatches', 'fashion-variation-swatches' ),
                'icon' => 'fa fa-shopping-cart',
            ]
        );
    }

    /**
     * Enqueue editor assets
     */
    public function enqueue_editor_assets() {
        wp_enqueue_style(
            'fashion-variation-swatches-elementor-editor',
            FASHION_VARIATION_SWATCHES_PLUGIN_URL . 'assets/css/elementor-editor.css',
            [],
            FASHION_VARIATION_SWATCHES_VERSION
        );
    }
} 