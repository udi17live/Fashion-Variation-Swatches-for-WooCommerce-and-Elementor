<?php
/**
 * Demo Setup Script for Fashion Variation Swatches
 * 
 * This script will create sample attributes and a demo product to showcase the plugin.
 * Run this once to see how the plugin works with real data.
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

// Check if WooCommerce is active
if ( ! class_exists( 'WooCommerce' ) ) {
    wp_die( 'WooCommerce is not active. Please activate WooCommerce first.' );
}

// Check if the plugin is active
if ( ! class_exists( 'Fashion_Variation_Swatches' ) ) {
    wp_die( 'Fashion Variation Swatches plugin is not active.' );
}

// Demo setup function
function fashion_variation_swatches_demo_setup() {
    global $wpdb;
    
    echo '<h2>🎯 Fashion Variation Swatches - Demo Setup</h2>';
    echo '<p>This script will create sample data to demonstrate the plugin functionality.</p>';
    
    // Step 1: Create or verify attributes
    echo '<h3>Step 1: Setting up Attributes</h3>';
    
    // Size attribute
    $size_attribute = wc_get_attribute( wc_attribute_taxonomy_id_by_name( 'pa_size' ) );
    if ( ! $size_attribute ) {
        echo '<p>❌ Size attribute not found. Please create it manually in Products > Attributes.</p>';
        return;
    }
    echo '<p>✅ Size attribute found: ' . $size_attribute->attribute_label . '</p>';
    
    // Color attribute
    $color_attribute = wc_get_attribute( wc_attribute_taxonomy_id_by_name( 'pa_color' ) );
    if ( ! $color_attribute ) {
        echo '<p>❌ Color attribute not found. Please create it manually in Products > Attributes.</p>';
        return;
    }
    echo '<p>✅ Color attribute found: ' . $color_attribute->attribute_label . '</p>';
    
    // Step 2: Create size terms
    echo '<h3>Step 2: Creating Size Terms</h3>';
    $size_terms = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
    foreach ( $size_terms as $size ) {
        $term = term_exists( $size, 'pa_size' );
        if ( ! $term ) {
            $term = wp_insert_term( $size, 'pa_size' );
            if ( ! is_wp_error( $term ) ) {
                echo '<p>✅ Created size term: ' . $size . '</p>';
            } else {
                echo '<p>❌ Failed to create size term: ' . $size . '</p>';
            }
        } else {
            echo '<p>✅ Size term already exists: ' . $size . '</p>';
        }
    }
    
    // Step 3: Create color terms with colors
    echo '<h3>Step 3: Creating Color Terms</h3>';
    $color_terms = [
        'Red' => '#ff0000',
        'Blue' => '#0000ff',
        'Green' => '#00ff00',
        'Black' => '#000000',
        'White' => '#ffffff',
        'Yellow' => '#ffff00'
    ];
    
    foreach ( $color_terms as $color_name => $color_code ) {
        $term = term_exists( $color_name, 'pa_color' );
        if ( ! $term ) {
            $term = wp_insert_term( $color_name, 'pa_color' );
            if ( ! is_wp_error( $term ) ) {
                update_term_meta( $term['term_id'], 'product_attribute_color', $color_code );
                echo '<p>✅ Created color term: ' . $color_name . ' (' . $color_code . ')</p>';
            } else {
                echo '<p>❌ Failed to create color term: ' . $color_name . '</p>';
            }
        } else {
            // Update color if it doesn't exist
            $existing_color = get_term_meta( $term['term_id'], 'product_attribute_color', true );
            if ( ! $existing_color ) {
                update_term_meta( $term['term_id'], 'product_attribute_color', $color_code );
                echo '<p>✅ Updated color for: ' . $color_name . ' (' . $color_code . ')</p>';
            } else {
                echo '<p>✅ Color term already exists: ' . $color_name . '</p>';
            }
        }
    }
    
    // Step 4: Create demo product
    echo '<h3>Step 4: Creating Demo Product</h3>';
    
    // Check if demo product already exists
    $demo_product = get_page_by_title( 'Demo T-Shirt - Fashion Variation Swatches', OBJECT, 'product' );
    
    if ( $demo_product ) {
        echo '<p>✅ Demo product already exists: <a href="' . get_edit_post_link( $demo_product->ID ) . '">Edit Demo T-Shirt</a></p>';
        echo '<p>View product: <a href="' . get_permalink( $demo_product->ID ) . '" target="_blank">Demo T-Shirt</a></p>';
    } else {
        // Create demo product
        $product_data = array(
            'post_title'    => 'Demo T-Shirt - Fashion Variation Swatches',
            'post_content'  => 'This is a demo product to showcase the Fashion Variation Swatches plugin. It includes size and color variations with beautiful swatches.',
            'post_status'   => 'publish',
            'post_type'     => 'product',
            'post_author'   => get_current_user_id(),
        );
        
        $product_id = wp_insert_post( $product_data );
        
        if ( ! is_wp_error( $product_id ) ) {
            // Set product type to variable
            wp_set_object_terms( $product_id, 'variable', 'product_type' );
            
            // Add attributes
            $attributes = array();
            
            // Size attribute
            $size_terms = get_terms( array(
                'taxonomy' => 'pa_size',
                'hide_empty' => false,
            ) );
            
            if ( ! is_wp_error( $size_terms ) ) {
                $size_values = array();
                foreach ( $size_terms as $term ) {
                    $size_values[] = $term->slug;
                }
                
                $attributes['pa_size'] = array(
                    'name' => 'pa_size',
                    'value' => '',
                    'position' => 0,
                    'is_visible' => 1,
                    'is_variation' => 1,
                    'is_taxonomy' => 1
                );
            }
            
            // Color attribute
            $color_terms = get_terms( array(
                'taxonomy' => 'pa_color',
                'hide_empty' => false,
            ) );
            
            if ( ! is_wp_error( $color_terms ) ) {
                $color_values = array();
                foreach ( $color_terms as $term ) {
                    $color_values[] = $term->slug;
                }
                
                $attributes['pa_color'] = array(
                    'name' => 'pa_color',
                    'value' => '',
                    'position' => 1,
                    'is_visible' => 1,
                    'is_variation' => 1,
                    'is_taxonomy' => 1
                );
            }
            
            // Save attributes
            update_post_meta( $product_id, '_product_attributes', $attributes );
            
            // Create variations
            if ( ! empty( $size_terms ) && ! empty( $color_terms ) ) {
                foreach ( $size_terms as $size_term ) {
                    foreach ( $color_terms as $color_term ) {
                        $variation_data = array(
                            'post_title'  => 'Demo T-Shirt - ' . $size_term->name . ' - ' . $color_term->name,
                            'post_name'   => 'product-' . $product_id . '-variation',
                            'post_status' => 'publish',
                            'post_parent' => $product_id,
                            'post_type'   => 'product_variation',
                            'guid'        => home_url() . '/?product_variation=product-' . $product_id . '-variation'
                        );
                        
                        $variation_id = wp_insert_post( $variation_data );
                        
                        if ( ! is_wp_error( $variation_id ) ) {
                            // Set variation attributes
                            update_post_meta( $variation_id, 'attribute_pa_size', $size_term->slug );
                            update_post_meta( $variation_id, 'attribute_pa_color', $color_term->slug );
                            
                            // Set variation data
                            update_post_meta( $variation_id, '_regular_price', '29.99' );
                            update_post_meta( $variation_id, '_price', '29.99' );
                            update_post_meta( $variation_id, '_stock', '10' );
                            update_post_meta( $variation_id, '_manage_stock', 'yes' );
                            update_post_meta( $variation_id, '_stock_status', 'instock' );
                        }
                    }
                }
            }
            
            echo '<p>✅ Demo product created successfully!</p>';
            echo '<p><a href="' . get_edit_post_link( $product_id ) . '">Edit Demo T-Shirt</a></p>';
            echo '<p><a href="' . get_permalink( $product_id ) . '" target="_blank">View Demo T-Shirt</a></p>';
        } else {
            echo '<p>❌ Failed to create demo product</p>';
        }
    }
    
    // Step 5: Summary
    echo '<h3>🎉 Demo Setup Complete!</h3>';
    echo '<p>Your demo data has been created successfully. Here\'s what you can do now:</p>';
    echo '<ul>';
    echo '<li>✅ Visit your demo product to see the swatches in action</li>';
    echo '<li>✅ Go to your shop page to see the filter widgets</li>';
    echo '<li>✅ Test the swatches on mobile devices</li>';
    echo '<li>✅ Customize the appearance in WooCommerce > Variation Swatches</li>';
    echo '</ul>';
    
    echo '<h3>🔗 Quick Links</h3>';
    echo '<p>';
    echo '<a href="' . admin_url( 'admin.php?page=fashion-variation-swatches' ) . '" style="margin-right: 10px;">Plugin Settings</a>';
    echo '<a href="' . admin_url( 'edit.php?post_type=product' ) . '" style="margin-right: 10px;">All Products</a>';
    echo '<a href="' . home_url( '/shop/' ) . '" target="_blank">Shop Page</a>';
    echo '</p>';
    
    echo '<p><strong>Note:</strong> You can delete the demo product later when you\'re ready to create your own products.</p>';
}

// Run the demo setup
fashion_variation_swatches_demo_setup(); 