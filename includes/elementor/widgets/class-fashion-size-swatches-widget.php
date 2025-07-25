<?php
/**
 * Size Swatches Elementor Widget
 *
 * @package fashion_variation_swatches
 */

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Size Swatches Widget
 */
class Fashion_Size_Swatches_Widget extends Widget_Base {

    /**
     * Get widget name
     *
     * @return string
     */
    public function get_name() {
        return 'wc-size-swatches';
    }

    /**
     * Get widget title
     *
     * @return string
     */
    public function get_title() {
        return __( 'WC Size Swatches', 'fashion-variation-swatches' );
    }

    /**
     * Get widget icon
     *
     * @return string
     */
    public function get_icon() {
        return 'eicon-products';
    }

    /**
     * Get widget categories
     *
     * @return array
     */
    public function get_categories() {
        return [ 'fashion-variation-swatches' ];
    }

    /**
     * Get widget keywords
     *
     * @return array
     */
    public function get_keywords() {
        return [ 'woocommerce', 'size', 'swatch', 'variation', 'attribute' ];
    }

    /**
     * Register controls
     */
    protected function register_controls() {
        // Content section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Content', 'fashion-variation-swatches' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => __( 'Title', 'fashion-variation-swatches' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Size', 'fashion-variation-swatches' ),
                'placeholder' => __( 'Enter title', 'fashion-variation-swatches' ),
            ]
        );

        $this->add_control(
            'size_attribute',
            [
                'label' => __( 'Size Attribute', 'fashion-variation-swatches' ),
                'type' => Controls_Manager::SELECT,
                'options' => $this->get_attributes(),
                'default' => get_option( 'fashion_variation_swatches_size_attribute', 'pa_size' ),
            ]
        );

        $this->add_control(
            'show_title',
            [
                'label' => __( 'Show Title', 'fashion-variation-swatches' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Show', 'fashion-variation-swatches' ),
                'label_off' => __( 'Hide', 'fashion-variation-swatches' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'swatch_style',
            [
                'label' => __( 'Swatch Style', 'fashion-variation-swatches' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'square' => __( 'Square', 'fashion-variation-swatches' ),
                    'rounded' => __( 'Rounded', 'fashion-variation-swatches' ),
                    'circle' => __( 'Circle', 'fashion-variation-swatches' ),
                ],
                'default' => 'square',
            ]
        );

        $this->add_control(
            'enable_tooltip',
            [
                'label' => __( 'Enable Tooltip', 'fashion-variation-swatches' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Yes', 'fashion-variation-swatches' ),
                'label_off' => __( 'No', 'fashion-variation-swatches' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        // Style section - Title
        $this->start_controls_section(
            'title_style_section',
            [
                'label' => __( 'Title', 'fashion-variation-swatches' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_title' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .wc-size-swatches-title',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __( 'Color', 'fashion-variation-swatches' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wc-size-swatches-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_margin',
            [
                'label' => __( 'Margin', 'fashion-variation-swatches' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .wc-size-swatches-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style section - Swatches
        $this->start_controls_section(
            'swatches_style_section',
            [
                'label' => __( 'Swatches', 'fashion-variation-swatches' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'swatch_size',
            [
                'label' => __( 'Size', 'fashion-variation-swatches' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 20,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 40,
                ],
                'selectors' => [
                    '{{WRAPPER}} .wc-size-swatch' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'swatch_spacing',
            [
                'label' => __( 'Spacing', 'fashion-variation-swatches' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .wc-size-swatch' => 'margin-right: {{SIZE}}{{UNIT}}; margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'swatch_typography',
                'selector' => '{{WRAPPER}} .wc-size-swatch',
            ]
        );

        $this->start_controls_tabs( 'swatch_style_tabs' );

        $this->start_controls_tab(
            'swatch_normal_tab',
            [
                'label' => __( 'Normal', 'fashion-variation-swatches' ),
            ]
        );

        $this->add_control(
            'swatch_color',
            [
                'label' => __( 'Text Color', 'fashion-variation-swatches' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wc-size-swatch' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'swatch_background',
            [
                'label' => __( 'Background Color', 'fashion-variation-swatches' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wc-size-swatch' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'swatch_border',
                'selector' => '{{WRAPPER}} .wc-size-swatch',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'swatch_hover_tab',
            [
                'label' => __( 'Hover', 'fashion-variation-swatches' ),
            ]
        );

        $this->add_control(
            'swatch_hover_color',
            [
                'label' => __( 'Text Color', 'fashion-variation-swatches' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wc-size-swatch:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'swatch_hover_background',
            [
                'label' => __( 'Background Color', 'fashion-variation-swatches' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wc-size-swatch:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'swatch_hover_border',
                'selector' => '{{WRAPPER}} .wc-size-swatch:hover',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'swatch_selected_tab',
            [
                'label' => __( 'Selected', 'fashion-variation-swatches' ),
            ]
        );

        $this->add_control(
            'swatch_selected_color',
            [
                'label' => __( 'Text Color', 'fashion-variation-swatches' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wc-size-swatch.selected' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'swatch_selected_background',
            [
                'label' => __( 'Background Color', 'fashion-variation-swatches' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wc-size-swatch.selected' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'swatch_selected_border',
                'selector' => '{{WRAPPER}} .wc-size-swatch.selected',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'swatch_box_shadow',
                'selector' => '{{WRAPPER}} .wc-size-swatch',
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Get available attributes
     *
     * @return array
     */
    private function get_attributes() {
        $attributes = [];
        $wc_attributes = wc_get_attribute_taxonomies();
        
        foreach ( $wc_attributes as $attribute ) {
            $attributes[ 'pa_' . $attribute->attribute_name ] = $attribute->attribute_label;
        }
        
        return $attributes;
    }

    /**
     * Render widget
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        if ( ! class_exists( 'WooCommerce' ) ) {
            echo '<p>' . __( 'WooCommerce is required for this widget.', 'fashion-variation-swatches' ) . '</p>';
            return;
        }

        $attribute = $settings['size_attribute'];
        $terms = get_terms( [
            'taxonomy' => $attribute,
            'hide_empty' => false,
        ] );

        if ( empty( $terms ) || is_wp_error( $terms ) ) {
            echo '<p>' . __( 'No size options found.', 'fashion-variation-swatches' ) . '</p>';
            return;
        }

        ?>
        <div class="wc-size-swatches-widget">
            <?php if ( $settings['show_title'] === 'yes' && ! empty( $settings['title'] ) ) : ?>
                <h3 class="wc-size-swatches-title"><?php echo esc_html( $settings['title'] ); ?></h3>
            <?php endif; ?>
            
            <div class="fashion-variation-swatches wc-size-swatches elementor-widget-swatches">
                <?php foreach ( $terms as $term ) : ?>
                    <?php
                    $tooltip = $settings['enable_tooltip'] === 'yes' ? 'title="' . esc_attr( $term->name ) . '"' : '';
                    ?>
                    <span class="wc-swatch wc-size-swatch <?php echo esc_attr( $settings['swatch_style'] ); ?>" 
                          data-value="<?php echo esc_attr( $term->slug ); ?>"
                          <?php echo $tooltip; ?>>
                        <?php echo esc_html( $term->name ); ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render widget in editor
     */
    protected function content_template() {
        ?>
        <#
        var iconHTML = elementor.helpers.renderIcon( view, settings.selected_icon, { 'aria-hidden': true }, 'i' , 'object' );
        #>
        <div class="wc-size-swatches-widget">
            <# if ( settings.show_title === 'yes' && settings.title ) { #>
                <h3 class="wc-size-swatches-title">{{{ settings.title }}}</h3>
            <# } #>
            
            <div class="fashion-variation-swatches wc-size-swatches elementor-widget-swatches">
                <span class="wc-swatch wc-size-swatch {{{ settings.swatch_style }}}"><?php esc_html_e( 'XS', 'fashion-variation-swatches' ); ?></span>
                <span class="wc-swatch wc-size-swatch {{{ settings.swatch_style }}}"><?php esc_html_e( 'S', 'fashion-variation-swatches' ); ?></span>
                <span class="wc-swatch wc-size-swatch {{{ settings.swatch_style }}}"><?php esc_html_e( 'M', 'fashion-variation-swatches' ); ?></span>
                <span class="wc-swatch wc-size-swatch {{{ settings.swatch_style }}}"><?php esc_html_e( 'L', 'fashion-variation-swatches' ); ?></span>
                <span class="wc-swatch wc-size-swatch {{{ settings.swatch_style }}}"><?php esc_html_e( 'XL', 'fashion-variation-swatches' ); ?></span>
            </div>
        </div>
        <?php
    }
} 