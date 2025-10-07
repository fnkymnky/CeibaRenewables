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

    register_post_type('project', [
        'labels'             => [ 'name' => 'Projects', 'singular_name' => 'Project' ],
        'public'             => true,
        'show_ui'            => true,
        'show_in_rest'       => true,
        'menu_icon'          => 'dashicons-media-document',
        'supports'           => ['title','editor','thumbnail','excerpt'],
        'has_archive'        => false,
        'rewrite' => ['slug' => 'projects'],
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

// Highlight the projects archive menu item on archive and single views.
add_filter('nav_menu_css_class', function ($classes, $item) {
    if (is_post_type_archive('project') || is_singular('project')) {
        // Target the "Post Type Archive" menu item for this CPT
        if ($item->type === 'post_type_archive' && $item->object === 'project') {
            $classes[] = 'current-menu-item';
            $classes[] = 'current_page_item'; // some themes still look for this
        }
    }
    return array_unique($classes);
}, 10, 2);

// Accessibility nicety: set aria-current on the same item.
add_filter('nav_menu_link_attributes', function ($atts, $item) {
    if (is_post_type_archive('project') || is_singular('project')) {
        if ($item->type === 'post_type_archive' && $item->object === 'project') {
            $atts['aria-current'] = 'page';
        }
    }
    return $atts;
}, 10, 2);


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

// Dynamic blocks for Case Studies page content split
add_action('init', function(){
    // Top segment of current page content, before separator with class "cs-loop-split"
    register_block_type('ceiba/case-studies-top', array(
        'api_version' => 2,
        'render_callback' => function( $attributes = [], $content = '', $block = null ){
            $post = get_post();
            if ( ! $post ) return '';
            $blocks = parse_blocks( $post->post_content );
            $out = '';
            foreach ( $blocks as $b ) {
                $name = isset($b['blockName']) ? $b['blockName'] : '';
                $class = isset($b['attrs']['className']) ? $b['attrs']['className'] : '';
                if ( $name === 'core/separator' && strpos($class, 'cs-loop-split') !== false ) {
                    break;
                }
                $out .= render_block( $b );
            }
            return $out;
        },
        'supports' => array( 'inserter' => false ),
    ));

    // Bottom segment of current page content, after separator with class "cs-loop-split"
    register_block_type('ceiba/case-studies-bottom', array(
        'api_version' => 2,
        'render_callback' => function( $attributes = [], $content = '', $block = null ){
            $post = get_post();
            if ( ! $post ) return '';
            $blocks = parse_blocks( $post->post_content );
            $out = '';
            $after = false;
            foreach ( $blocks as $b ) {
                if ( ! $after ) {
                    $name = isset($b['blockName']) ? $b['blockName'] : '';
                    $class = isset($b['attrs']['className']) ? $b['attrs']['className'] : '';
                    if ( $name === 'core/separator' && strpos($class, 'cs-loop-split') !== false ) {
                        $after = true;
                    }
                    continue;
                }
                $out .= render_block( $b );
            }
            return $out;
        },
        'supports' => array( 'inserter' => false ),
    ));
});

// Shortcode to render Case Studies loop inside page content
add_action('init', function(){
    add_shortcode('case_studies_loop', function($atts){
        $atts = shortcode_atts([
            'per_page' => 12,
            'columns'  => 3,
        ], $atts, 'case_studies_loop');

        $ppp = max(1, intval($atts['per_page']));
        $cols = max(1, intval($atts['columns']));
        $paged = max(1, (int) get_query_var('paged'), (int) get_query_var('page'));

        $q = new WP_Query([
            'post_type'      => 'project',
            'posts_per_page' => $ppp,
            'paged'          => $paged,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'no_found_rows'  => false,
        ]);

        ob_start();
        if ( $q->have_posts() ){
            $grid_style = sprintf('grid-template-columns: repeat(%d, minmax(0,1fr));', $cols);
            echo '<div class="posts-grid" style="display:grid; gap: var(--wp--style--block-gap, 24px); ' . esc_attr($grid_style) . '">';
            while( $q->have_posts() ){
                $q->the_post();
                echo '<article class="wp-block-group blog-list-item">';
                if ( has_post_thumbnail() ){
                    echo '<div class="blog-list-item__media">';
                    the_post_thumbnail('large');
                    echo '</div>';
                }
                echo '<div class="wp-block-group blog-list-item__body">';
                echo '<h3 class="blog-list-item__title"><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h3>';
                echo '</div>';
                echo '</article>';
            }
            echo '</div>';

            // Pagination
            $big = 999999;
            $base = trailingslashit( get_permalink( get_queried_object_id() ) ) . '%_%';
            $format = 'page/%#%/';
            $links = paginate_links([
                'base'      => $base,
                'format'    => $format,
                'current'   => max( 1, $paged ),
                'total'     => max( 1, (int) $q->max_num_pages ),
                'type'      => 'list',
                'prev_text' => __('Newer'),
                'next_text' => __('Older'),
            ]);
            if ( $links ){
                echo '<nav class="posts-pagination">' . $links . '</nav>';
            }
        } else {
            echo '<p>' . esc_html__('No case studies found.', 'ceiba') . '</p>';
        }
        wp_reset_postdata();
        return ob_get_clean();
    });
});
