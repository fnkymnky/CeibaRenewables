<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('init', function () {
	// Define button variant styles avialable in the editor view
	register_block_style('core/button', ['name' => 'ceiba-green',    'label' => 'Ceiba Green','is_default' => true,]);
	register_block_style('core/button', ['name' => 'ceiba-navy',   'label' => 'Ceiba Navy']);
	register_block_style('core/button', ['name' => 'white',   'label' => 'White']);

	// Remove block patterns frome editor
	remove_theme_support( 'core-block-patterns' );

	// Remove specific block patterns from editor
	unregister_block_pattern('core/query-standard-posts');
	unregister_block_pattern('core/query-medium-posts');
	unregister_block_pattern('core/query-small-posts');
	unregister_block_pattern('core/query-grid-posts');
	unregister_block_pattern('core/query-large-title-posts');
	unregister_block_pattern('core/query-offset-posts');  
}, 20);


/* Control What's Shown In The Editor and Unregister Blocks In One Place */
// Enqueue JavaScript for block editor
add_action('enqueue_block_editor_assets', function () {
    wp_enqueue_script(
        'unregister-core-blocks',
        get_stylesheet_directory_uri() . '/js/unregister-blocks.js',
        ['wp-blocks', 'wp-dom-ready', 'wp-edit-post'],
        null,
        true
    );
});

/******/

/* Force button variant styles to work */
add_action('wp_enqueue_scripts', function () {
  wp_enqueue_style(
    'child-button-variants',
    get_stylesheet_directory_uri() . '/buttons.css',
    ['global-styles'],
    filemtime(get_stylesheet_directory() . '/buttons.css')
  );
}, 100);

add_action('enqueue_block_editor_assets', function () {
  wp_enqueue_style(
	'child-editor-button-variants',
	get_stylesheet_directory_uri() . '/buttons.css',
	[],
	filemtime(get_stylesheet_directory() . '/buttons.css')
  );
});

add_action('after_setup_theme', function () {
  add_theme_support('editor-styles');
  add_editor_style('buttons.css');
});

add_action('enqueue_block_editor_assets', function () {
  // Editor-only CSS to hide the Styles panel for Button blocks
  wp_add_inline_style(
    'wp-edit-blocks',
    '.edit-post-sidebar .block-editor-block-styles { display:none !important; }'
  );
});



// Media: ensure featured images and custom sizes for page list cards
add_action('after_setup_theme', function(){
    add_theme_support('post-thumbnails');
    // Square crops for page list cards
	add_image_size('page-list-660', 660, 660, true);
    add_image_size('page-list-360', 360, 360, true);
    add_image_size('page-list-320', 320, 320, true);
    // 16:9 mobile hero background (used in CSS @media)
    add_image_size('hero-mobile', 768, 432, true);
});

/* Load design tokens in front-end and editor */
add_action('wp_enqueue_scripts', function () {
	wp_enqueue_style('ceiba-tokens', get_stylesheet_directory_uri() . '/assets/tokens.css', wp_get_theme()->get('Version'));
	wp_enqueue_style('ceiba-child-style', get_stylesheet_uri(), ['twentytwentyfive-style'], wp_get_theme()->get('Version'));
	wp_enqueue_style('ceiba-header', get_stylesheet_directory_uri() . '/assets/header.css',	wp_get_theme()->get('Version'));
	wp_enqueue_style('ceiba-fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css', [], '6.5.1');
	wp_enqueue_style('ceiba-google-fonts', 'https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap', [], null);
}, 20);

add_action('enqueue_block_editor_assets', function () {
	wp_enqueue_style('ceiba-tokens-editor', get_stylesheet_directory_uri() . '/assets/tokens.css', [], '1.0');
	// Load Montserrat in editor as well
	wp_enqueue_style('ceiba-google-fonts-editor', 'https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap', [], null);
});

// /* Curate allowed blocks */
// add_filter( 'allowed_block_types_all', function( $allowed, $ctx ) {
// 	return [
// 	    'core/paragraph','core/heading','core/list', 'core/list-item', 'core/quote',
// 	    'core/separator','core/spacer','core/image','core/gallery',
// 	    'core/cover','core/media-text','core/buttons','core/button',
// 	    'core/columns','core/column','core/group','core/row','core/stack', 'core/table', 'core/accordion', 'core/details',
// 	    'core/cover',
// 	    'ceiba/content-card', 'ceiba/image-frame', 'ceiba/map-embed', 'ceiba/testimonials-carousel', 'ceiba/callout',
// 	    'ceiba/testimonial', 'ceiba/case-study', 'ceiba/case-studies-carousel', 'ceiba/content-section', 'ceiba/content-columns', 'ceiba/team-grid', 'ceiba/content-grid',
// 	    'ceiba/content-grid-item'	
// 	];
// }, 10, 2 );


/**
 * Header-specific CSS.
*/
add_action('wp_enqueue_scripts', function () {
	$ver = wp_get_theme()->get('Version') ?: null;
	wp_enqueue_style(
		'ceiba-header',
		get_stylesheet_directory_uri() . '/assets/header.css',
		[],
		$ver
	);
});
			
			
add_action('init', function () {
	if ( ! post_type_exists('wp_navigation') ) {
		return;
	}
	
	$menus = [
		[
			'post_name'  => 'primary-navigation',
			'post_title' => 'Primary Navigation',
		],
		[
			'post_name'  => 'contact-navigation',
			'post_title' => 'Contact Details',
		],
		[
			'post_name'  => 'footer-navigation',
			'post_title' => 'Footer Navigation',
		],
	];
	
	foreach ($menus as $m) {
		$existing = get_page_by_path($m['post_name'], OBJECT, 'wp_navigation');
		if ( $existing ) {
			continue;
		}
		wp_insert_post([
			'post_type'   => 'wp_navigation',
			'post_status' => 'publish',
			'post_name'   => $m['post_name'],
			'post_title'  => $m['post_title'],
			'post_content'=> '', // links added in admin area
		]);
	}
});


/* Add New Ceiba Category For Custom Blocks */
add_filter( 'block_categories_all', 'add_order_block_category', 10, 2 );

function add_order_block_category( $categories ) {
    $custom_category = array(
        'slug'     => 'ceiba-blocks',
        'title'    => __( 'Ceiba Blocks', 'ceiba' ),
        'icon'     => null,
        'position' => 1,
    );

    // Extract position from the custom category array.
    $position = $custom_category['position'];

    // Remove position from the custom category array.
    unset( $custom_category['position'] );

    // Insert the custom category at the desired position.
    array_splice( $categories, $position, 0, array( $custom_category ) );

    return $categories;
}