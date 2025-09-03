<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$attr = wp_parse_args( (array) $attributes, [
  'title' => '',
  'backgroundId' => 0,
  'backgroundUrl' => '',
  'backgroundAlt' => '',
] );

$bg_url = '';
if ( ! empty( $attr['backgroundUrl'] ) ) {
  $bg_url = esc_url( $attr['backgroundUrl'] );
} elseif ( ! empty( $attr['backgroundId'] ) ) {
  $img = wp_get_attachment_image_src( (int) $attr['backgroundId'], 'full' );
  if ( $img ) { $bg_url = esc_url( $img[0] ); }
}

$align_class = '';
if ( isset( $attr['align'] ) ) {
  if ( $attr['align'] === 'full' ) {
    $align_class = ' alignfull';
  } elseif ( $attr['align'] === 'wide' ) {
    $align_class = ' alignwide';
  }
}

$wrapper = get_block_wrapper_attributes( [ 'class' => 'ceiba-hero' . $align_class ] );

$top_style = $bg_url ? ' style="background-image:url(' . esc_url( $bg_url ) . ');"' : '';

echo '<section ' . $wrapper . '>';
// Top: background image + title
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
