<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$attr = wp_parse_args( (array) $attributes, [
  'title' => '',
  // legacy attributes retained for backward compatibility but ignored for background selection
  'backgroundId' => 0,
  'backgroundUrl' => '',
  'backgroundAlt' => '',
] );

// Use the page featured image as background; fallback to specified upload path when absent
$bg_url = '';
$featured = get_post_thumbnail_id();
if ( $featured ) {
  $img = wp_get_attachment_image_src( (int) $featured, 'full' );
  if ( $img ) { $bg_url = esc_url( $img[0] ); }
}
if ( ! $bg_url ) {
  // Use a site-relative default image path if no featured image exists
  $bg_url = esc_url( content_url( 'uploads/2025/09/Home-Page-Banner.jpg' ) );
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
$wrapper = get_block_wrapper_attributes( [ 'id' => $unique_id, 'class' => 'ceiba-hero' . $align_class ] );

// Prepare responsive background URLs
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

// No inline style attribute; use inline CSS so mobile avoids fetching desktop asset
$top_style = '';

echo '<section ' . $wrapper . '>';
// Top: background image + title
echo '  <style>';
if ( $bg_url_full ) echo '#' . esc_attr( $unique_id ) . ' .ceiba-hero__top{background-image:url(' . esc_url( $bg_url_full ) . ');}';
if ( $bg_url_mobile ) echo '@media (max-width: 768px){#' . esc_attr( $unique_id ) . ' .ceiba-hero__top{background-image:url(' . esc_url( $bg_url_mobile ) . ') !important;}}';
echo '</style>';
echo '  <div class="ceiba-hero__top"' . $top_style . '>';
echo '    <div class="ceiba-hero__backdrop" aria-hidden="true"></div>';
echo '    <div class="ceiba-hero__inner">';
if ( ! empty( $attr['title'] ) ) {
  echo '      <h1 class="ceiba-hero__title">' . wp_kses_post( $attr['title'] ) . '</h1>';
}
echo '    </div>';
echo '  </div>';

// Bottom: solid background + content (render only if there is content)
$has_content = trim( (string) $content ) !== '';
if ( $has_content ) {
  echo '  <div class="ceiba-hero__bottom">';
  echo '    <div class="ceiba-hero__inner">';
  echo '      <div class="ceiba-hero__content">' . $content . '</div>';
  echo '    </div>';
  echo '  </div>';
}
echo '</section>';
