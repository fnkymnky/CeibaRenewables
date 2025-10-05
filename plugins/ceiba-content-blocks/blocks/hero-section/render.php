<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$attr = wp_parse_args( (array) $attributes, [
  'title' => '',
  'backgroundId' => 0,
  'backgroundUrl' => '',
  'backgroundAlt' => '',
] );

// Use the page featured image as background; allow a pluggable fallback via filter when absent
$bg_url = '';
$featured = get_post_thumbnail_id();
if ( $featured ) {
  $img = wp_get_attachment_image_src( (int) $featured, 'full' );
  if ( $img ) { $bg_url = esc_url( $img[0] ); }
}
if ( ! $bg_url ) {
  $bg_url = apply_filters( 'ceiba_hero_default_background_url', '' );
  $bg_url = $bg_url ? esc_url( $bg_url ) : '';
}

$align_class = '';
if ( isset( $attr['align'] ) ) {
  if ( $attr['align'] === 'full' ) {
    $align_class = ' alignfull';
  } elseif ( $attr['align'] === 'wide' ) {
    $align_class = ' alignwide';
  }
}

$unique_id = uniqid('ceiba-hero-');
$wrapper = get_block_wrapper_attributes( [ 'id' => $unique_id, 'class' => 'ceiba-hero fade-up' . $align_class ] );

$bg_url_full   = $bg_url;
$bg_url_mobile = $bg_url;
if ( $featured ) {
  // Use registered size for mobile first, fallback to common sizes
  $mobile = wp_get_attachment_image_src( (int) $featured, 'hero-mobile' );
  if ( ! $mobile ) { $mobile = wp_get_attachment_image_src( (int) $featured, 'medium_large' ); }
  if ( ! $mobile ) { $mobile = wp_get_attachment_image_src( (int) $featured, 'large' ); }
  if ( ! $mobile ) { $mobile = wp_get_attachment_image_src( (int) $featured, 'full' ); }
  if ( is_array( $mobile ) && ! empty( $mobile[0] ) ) {
    $bg_url_mobile = esc_url( $mobile[0] );
  }
}

echo '<section ' . $wrapper . '>';
// Top: background image + title
echo '  <style>';
if ( $bg_url_full ) echo '#' . esc_attr( $unique_id ) . ' .ceiba-hero__top{background-image:url(' . esc_url( $bg_url_full ) . ');}';
if ( $bg_url_mobile ) echo '@media (max-width: 768px){#' . esc_attr( $unique_id ) . ' .ceiba-hero__top{background-image:url(' . esc_url( $bg_url_mobile ) . ') !important;}}';
echo '</style>';
echo '  <div class="ceiba-hero__top">';
echo '    <div class="ceiba-hero__backdrop" aria-hidden="true"></div>';
echo '    <div class="ceiba-hero__top__inner">';
if ( ! empty( $attr['title'] ) ) {
  echo '      <h1 class="ceiba-hero__title">' . wp_kses_post( $attr['title'] ) . '</h1>';
}

echo '    </div>';
echo '  </div>';
echo '<div class="ceiba-hero__top__green-border"></div>';

// Bottom: solid background + content (render only if there is content)
$has_content = trim( (string) $content ) !== '';
if ( $has_content ) {
  echo '  <div class="ceiba-hero__bottom">';
  echo '    <div class="ceiba-hero__bottom__inner">';
  echo '      <div class="ceiba-hero__content">' . $content . '</div>';
  echo '    </div>';
  echo '  </div>';
}
echo '</section>';

