<?php
/**
 * Attributes management class
 *
 * @package fashion_variation_swatches
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Attributes class
 */
class Fashion_Variation_Swatches_Attributes {

    /**
     * Instance
     *
     * @var Fashion_Variation_Swatches_Attributes
     */
    private static $instance = null;

    /**
     * Get instance
     *
     * @return Fashion_Variation_Swatches_Attributes
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
        // Add color field to attribute terms
        add_action( 'pa_color_add_form_fields', [ $this, 'add_color_field_create' ] );
        add_action( 'pa_color_edit_form_fields', [ $this, 'add_color_field_edit' ], 10, 2 );
        add_action( 'created_pa_color', [ $this, 'save_color_field' ] );
        add_action( 'edited_pa_color', [ $this, 'save_color_field' ] );
        
        // Add columns to attribute terms list
        add_filter( 'manage_edit-pa_color_columns', [ $this, 'add_color_column' ] );
        add_action( 'manage_pa_color_custom_column', [ $this, 'add_color_column_content' ], 10, 3 );
        
        // Enqueue admin assets
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
        
        // Create default attributes on activation
        add_action( 'init', [ $this, 'create_default_attributes' ] );
    }

    /**
     * Add color field to create term form
     */
    public function add_color_field_create() {
        ?>
        <div class="form-field">
            <label for="fashion_variation_swatches_color"><?php esc_html_e( 'Color', 'fashion-variation-swatches' ); ?></label>
            <input type="text" id="fashion_variation_swatches_color" name="fashion_variation_swatches_color" value="#ffffff" class="fashion-variation-swatches-color-picker" />
            <p class="description"><?php esc_html_e( 'Select the color for this attribute term.', 'fashion-variation-swatches' ); ?></p>
        </div>
        <?php
    }

    /**
     * Add color field to edit term form
     *
     * @param object $term
     * @param string $taxonomy
     */
    public function add_color_field_edit( $term, $taxonomy ) {
        $color = get_term_meta( $term->term_id, 'fashion_variation_swatches_color', true );
        if ( ! $color ) {
            $color = '#ffffff';
        }
        ?>
        <tr class="form-field">
            <th scope="row" valign="top">
                <label for="fashion_variation_swatches_color"><?php esc_html_e( 'Color', 'fashion-variation-swatches' ); ?></label>
            </th>
            <td>
                <input type="text" id="fashion_variation_swatches_color" name="fashion_variation_swatches_color" value="<?php echo esc_attr( $color ); ?>" class="fashion-variation-swatches-color-picker" />
                <p class="description"><?php esc_html_e( 'Select the color for this attribute term.', 'fashion-variation-swatches' ); ?></p>
            </td>
        </tr>
        <?php
    }

    /**
     * Save color field
     *
     * @param int $term_id
     */
    public function save_color_field( $term_id ) {
        if ( isset( $_POST['fashion_variation_swatches_color'] ) ) {
            $color = sanitize_hex_color( $_POST['fashion_variation_swatches_color'] );
            if ( $color ) {
                update_term_meta( $term_id, 'fashion_variation_swatches_color', $color );
            }
        }
    }

    /**
     * Add color column to terms list
     *
     * @param array $columns
     * @return array
     */
    public function add_color_column( $columns ) {
        $new_columns = [];
        foreach ( $columns as $key => $column ) {
            $new_columns[ $key ] = $column;
            if ( $key === 'name' ) {
                $new_columns['fashion_variation_swatches_color'] = __( 'Color', 'fashion-variation-swatches' );
            }
        }
        return $new_columns;
    }

    /**
     * Add color column content
     *
     * @param string $content
     * @param string $column_name
     * @param int $term_id
     */
    public function add_color_column_content( $content, $column_name, $term_id ) {
        if ( $column_name === 'fashion_variation_swatches_color' ) {
            $color = get_term_meta( $term_id, 'fashion_variation_swatches_color', true );
            if ( $color ) {
                $content = '<span class="fashion-variation-swatches-color-preview" style="background-color: ' . esc_attr( $color ) . '; width: 20px; height: 20px; display: inline-block; border-radius: 50%; border: 1px solid #ddd;"></span>';
            }
        }
        return $content;
    }

    /**
     * Enqueue admin assets
     *
     * @param string $hook
     */
    public function enqueue_admin_assets( $hook ) {
        if ( strpos( $hook, 'edit-tags.php' ) !== false || strpos( $hook, 'term.php' ) !== false ) {
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'wp-color-picker' );
            
            wp_enqueue_style(
                'fashion-variation-swatches-admin',
                FASHION_VARIATION_SWATCHES_PLUGIN_URL . 'assets/css/admin.css',
                [],
                FASHION_VARIATION_SWATCHES_VERSION
            );
            
            wp_enqueue_script(
                'fashion-variation-swatches-admin',
                FASHION_VARIATION_SWATCHES_PLUGIN_URL . 'assets/js/admin.js',
                [ 'jquery', 'wp-color-picker' ],
                FASHION_VARIATION_SWATCHES_VERSION,
                true
            );
        }
    }

    /**
     * Create default attributes
     */
    public function create_default_attributes() {
        if ( ! get_option( 'Fashion_Variation_Swatches_Attributes_created' ) ) {
            $this->create_size_attribute();
            $this->create_color_attribute();
            update_option( 'Fashion_Variation_Swatches_Attributes_created', true );
        }
    }

    /**
     * Create size attribute
     */
    private function create_size_attribute() {
        if ( taxonomy_exists( 'pa_size' ) ) {
            return;
        }

        $attribute_data = [
            'attribute_label'   => __( 'Size', 'fashion-variation-swatches' ),
            'attribute_name'    => 'size',
            'attribute_type'    => 'select',
            'attribute_orderby' => 'menu_order',
            'attribute_public'  => 0,
        ];

        $attribute_id = wc_create_attribute( $attribute_data );

        if ( ! is_wp_error( $attribute_id ) ) {
            // Create default size terms
            $size_terms = [
                'xs'     => __( 'XS', 'fashion-variation-swatches' ),
                's'      => __( 'S', 'fashion-variation-swatches' ),
                'm'      => __( 'M', 'fashion-variation-swatches' ),
                'l'      => __( 'L', 'fashion-variation-swatches' ),
                'xl'     => __( 'XL', 'fashion-variation-swatches' ),
                'xxl'    => __( 'XXL', 'fashion-variation-swatches' ),
            ];

            foreach ( $size_terms as $slug => $name ) {
                wp_insert_term( $name, 'pa_size', [
                    'slug' => $slug,
                ] );
            }
        }
    }

    /**
     * Create color attribute
     */
    private function create_color_attribute() {
        if ( taxonomy_exists( 'pa_color' ) ) {
            return;
        }

        $attribute_data = [
            'attribute_label'   => __( 'Color', 'fashion-variation-swatches' ),
            'attribute_name'    => 'color',
            'attribute_type'    => 'select',
            'attribute_orderby' => 'menu_order',
            'attribute_public'  => 0,
        ];

        $attribute_id = wc_create_attribute( $attribute_data );

        if ( ! is_wp_error( $attribute_id ) ) {
            // Create default color terms
            $color_terms = [
                'red'     => [ 'name' => __( 'Red', 'fashion-variation-swatches' ), 'color' => '#ff0000' ],
                'green'   => [ 'name' => __( 'Green', 'fashion-variation-swatches' ), 'color' => '#00ff00' ],
                'blue'    => [ 'name' => __( 'Blue', 'fashion-variation-swatches' ), 'color' => '#0000ff' ],
                'black'   => [ 'name' => __( 'Black', 'fashion-variation-swatches' ), 'color' => '#000000' ],
                'white'   => [ 'name' => __( 'White', 'fashion-variation-swatches' ), 'color' => '#ffffff' ],
                'yellow'  => [ 'name' => __( 'Yellow', 'fashion-variation-swatches' ), 'color' => '#ffff00' ],
            ];

            foreach ( $color_terms as $slug => $data ) {
                $term = wp_insert_term( $data['name'], 'pa_color', [
                    'slug' => $slug,
                ] );
                
                if ( ! is_wp_error( $term ) ) {
                    update_term_meta( $term['term_id'], 'fashion_variation_swatches_color', $data['color'] );
                }
            }
        }
    }
} 