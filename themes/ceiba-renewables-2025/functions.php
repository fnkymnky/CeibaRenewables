<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/* Load design tokens in front-end and editor */
add_action('wp_enqueue_scripts', function () {
  wp_enqueue_style('ceiba-tokens', get_stylesheet_directory_uri() . '/assets/tokens.css', [], '1.0');
}, 20);

add_action('enqueue_block_editor_assets', function () {
  wp_enqueue_style('ceiba-tokens-editor', get_stylesheet_directory_uri() . '/assets/tokens.css', [], '1.0');
});

// /* Curate allowed blocks */
// add_filter( 'allowed_block_types_all', function( $allowed, $ctx ) {
//   return [
//     'core/paragraph','core/heading','core/list', 'core/list-item', 'core/quote',
//     'core/separator','core/spacer','core/image','core/gallery',
//     'core/cover','core/media-text','core/buttons','core/button',
//     'core/columns','core/column','core/group','core/row','core/stack', 'core/table', 'core/accordion', 'core/details',
//     'core/cover',
//     'ceiba/content-card', 'ceiba/image-frame', 'ceiba/map-embed', 'ceiba/testimonials-carousel', 'ceiba/callout',
//     'ceiba/testimonial', 'ceiba/case-study', 'ceiba/case-studies-carousel', 'ceiba/content-section', 'ceiba/content-columns', 'ceiba/team-grid', 'ceiba/content-grid',
//     'ceiba/content-grid-item'

//   ];
// }, 10, 2 );



add_action( 'after_setup_theme', function() {
  remove_theme_support( 'core-block-patterns' );
} );


/**
 * Header-specific CSS.
 */
add_action('wp_enqueue_scripts', function () {
	$ver = wp_get_theme()->get('Version') ?: null;
	wp_enqueue_style(
		'yourtheme-header',
		get_template_directory_uri() . '/assets/css/header.css',
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
			'post_content'=> '', // leave empty; editors will add links
		]);
	}
});