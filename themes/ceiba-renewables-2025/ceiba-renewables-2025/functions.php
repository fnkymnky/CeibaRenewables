<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/* Load design tokens in front-end and editor */
add_action('wp_enqueue_scripts', function () {
  wp_enqueue_style('ceiba-tokens', get_stylesheet_directory_uri() . '/assets/tokens.css', [], '1.0');
}, 20);

add_action('enqueue_block_editor_assets', function () {
  wp_enqueue_style('ceiba-tokens-editor', get_stylesheet_directory_uri() . '/assets/tokens.css', [], '1.0');
});

/* Curate allowed blocks */
add_filter( 'allowed_block_types_all', function( $allowed, $ctx ) {
  return [
    'core/paragraph','core/heading','core/list', 'core/list-item', 'core/quote',
    'core/separator','core/spacer','core/image','core/gallery',
    'core/cover','core/media-text','core/buttons','core/button',
    'core/columns','core/column','core/group','core/row','core/stack', 'core/table', 'core/accordion', 'core/details',
    'ceiba/content-card', 'ceiba/image-frame', 'ceiba/map-embed', 'ceiba/testimonials-carousel', 'ceiba/callout',
    'ceiba/testimonial', 'ceiba/case-study', 'ceiba/case-studies-carousel', 'ceiba/content-section', 'ceiba/content-columns', 'ceiba/team-grid', 'ceiba/content-grid',
    'ceiba/content-grid-item'

  ];
}, 10, 2 );



add_action( 'after_setup_theme', function() {
  remove_theme_support( 'core-block-patterns' );
} );
