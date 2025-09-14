<?php
/**
 * Plugin Name: Ceiba Content Blocks
 * Description: Custom blocks and content types for Ceiba Renewables 2025.
 * Version: 1.0.1
 * Author: Steven McCurrach
 */

if ( ! defined( 'ABSPATH' ) ) exit;


add_action('init', function () {
    register_post_type('testimonial', [
        'labels' => [
            'name' => 'Testimonials',
            'singular_name' => 'Testimonial',
            'add_new_item' => 'Add New Testimonial',
            'edit_item' => 'Edit Testimonial',
            'add_new' => 'Add New',
            'new_item' => 'New Testimonial',
            'view_item' => 'View Testimonial',
            'search_items' => 'Search Testimonials',
        ],
        'public' => true,
        'show_ui' => true,
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-format-quote',
        'supports' => ['title'],
    ]);

    register_post_type('case_study', [
        'labels' => [
            'name' => 'Case Studies',
            'singular_name' => 'Case Study',
            'add_new_item' => 'Add New Case Study',
            'edit_item' => 'Edit Case Study'
        ],
        'public' => true,
        'show_ui' => true,
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-media-document',
        'supports' => ['title','editor','thumbnail','excerpt'],
        'has_archive' => false,
        'rewrite' => ['slug' => 'case-studies'],
    ]);

    register_post_meta('testimonial', 'ceiba_quote', [
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'wp_kses_post',
        'auth_callback' => function(){ return current_user_can('edit_posts'); }
    ]);

    register_post_meta('testimonial', 'ceiba_role', [
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback' => function(){ return current_user_can('edit_posts'); }
    ]);

    $built = __DIR__ . '/build';
    $src   = __DIR__ . '/blocks';
    $registry = WP_Block_Type_Registry::get_instance();

    // Register built blocks for assets; if a source render.php exists, wire it as the render callback
    foreach ( glob( $built . '/*/block.json' ) as $json ) {
        $build_dir = dirname( $json );
        $slug      = basename( $build_dir );
        $src_dir   = $src . '/' . $slug;

        if ( file_exists( $src_dir . '/render.php' ) ) {
            register_block_type_from_metadata( $build_dir, [
                'render_callback' => function( $attributes = [], $content = '', $block = null ) use ( $src_dir ) {
                    ob_start();
                    include $src_dir . '/render.php';
                    return ob_get_clean();
                }
            ] );
        } else {
            register_block_type_from_metadata( $build_dir );
        }
    }

    // Also register any source blocks that don't yet have a built artifact
    foreach ( glob( $src . '/*/block.json' ) as $json ) {
        $data = json_decode( file_get_contents( $json ), true );
        $name = is_array( $data ) && isset( $data['name'] ) ? $data['name'] : null;
        if ( $name && ! $registry->is_registered( $name ) ) {
            register_block_type_from_metadata( dirname( $json ) );
        }
    }
});

add_filter('use_block_editor_for_post_type', function($use, $post_type){
    if ($post_type === 'testimonial') return false;
    return $use;
}, 10, 2);

add_action('add_meta_boxes', function(){
    add_meta_box('ceiba_testimonial_details', 'Testimonial Details', function($post){
        $quote = get_post_meta($post->ID, 'ceiba_quote', true);
        $role  = get_post_meta($post->ID, 'ceiba_role', true);
        wp_nonce_field('ceiba_testimonial_save','ceiba_testimonial_nonce');
        echo '<p><label for="ceiba_quote" style="font-weight:600;display:block;margin-bottom:4px;">Quote</label>';
        echo '<textarea id="ceiba_quote" name="ceiba_quote" rows="5" style="width:100%;">'.esc_textarea($quote).'</textarea></p>';
        echo '<p><label for="ceiba_role" style="font-weight:600;display:block;margin-bottom:4px;">Customer name and role</label>';
        echo '<input type="text" id="ceiba_role" name="ceiba_role" value="'.esc_attr($role).'" style="width:100%;" /></p>';
        echo '<p style="color:#666;margin-top:8px;">Customer company name goes in the Title field.</p>';
    }, 'testimonial', 'normal', 'high');
});

add_action('save_post_testimonial', function($post_id){
    if (!isset($_POST['ceiba_testimonial_nonce']) || !wp_verify_nonce($_POST['ceiba_testimonial_nonce'],'ceiba_testimonial_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (isset($_POST['ceiba_quote'])) update_post_meta($post_id, 'ceiba_quote', wp_kses_post($_POST['ceiba_quote']));
    if (isset($_POST['ceiba_role'])) update_post_meta($post_id, 'ceiba_role', sanitize_text_field($_POST['ceiba_role']));
});

add_filter('enter_title_here', function($text, $post){
    if ($post && $post->post_type === 'testimonial') return 'Customer company name';
    return $text;
}, 10, 2);

// // Allow-list for Case Studies (supports both old/new filter signatures)
// function ceiba_allowed_blocks_for_case_study( $allowed, $context = null ) {
//     $post_type = null;

//     if ( is_array( $context ) && isset( $context['post_type'] ) ) {
//         $post_type = $context['post_type'];
//     } elseif ( is_object( $context ) && isset( $context->post ) && $context->post ) {
//         $post_type = $context->post->post_type;
//     } elseif ( is_object( $context ) && isset( $context->postType ) ) {
//         $post_type = $context->postType;
//     }

//     if ( $post_type !== 'case_study' ) {
//         return $allowed; // don't touch other post types
//     }

//     // IMPORTANT: include both core/list and core/list-item
//     return array(
//         'core/paragraph',
//         'core/heading',
//         'core/list',
//         'core/list-item',
//         'core/quote',
//         'core/image',
//         'core/gallery',
//         'core/separator',
//         'core/buttons',
//         'core/columns',
//         'core/column',
//         'core/embed',
//         'core/spacer',
//         'ceiba/map-embed',
//         'ceiba/testimonial',
//     );
// }

// // Ensure the allow-list actually applies in the editor
// add_filter('allowed_block_types_all', 'ceiba_allowed_blocks_for_case_study', 10, 2);
