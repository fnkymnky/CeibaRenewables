<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$attr = wp_parse_args( (array) $attributes, [
  'title'         => '',
  'backgroundId'  => 0,
  'backgroundUrl' => '',
  'backgroundAlt' => '',
  'align'         => '',
]);

/**
 * Render the first direct child core/group whose className contains $class_substring.
 * Guarded so multiple includes don’t fatal.
 */
if ( ! function_exists( 'ceiba_hero_render_group_by_class' ) ) {
  function ceiba_hero_render_group_by_class( $block, $class_substring ) {
    if ( empty( $block ) || empty( $block->parsed_block ) || empty( $block->parsed_block['innerBlocks'] ) ) {
      return '';
    }
    foreach ( $block->parsed_block['innerBlocks'] as $child ) {
      if ( isset( $child['blockName'] ) && $child['blockName'] === 'core/group' ) {
        $cls = isset( $child['attrs']['className'] ) ? (string) $child['attrs']['className'] : '';
        if ( $cls !== '' && strpos( $cls, $class_substring ) !== false ) {
          return render_block( $child );
        }
      }
    }
    return '';
  }
}

/**
 * Detect whether this instance uses the two-slot template (so we know when $content is legacy).
 */
$has_slots = false;
if ( ! empty( $block->parsed_block['innerBlocks'] ) ) {
  foreach ( $block->parsed_block['innerBlocks'] as $child ) {
    if (
      isset( $child['blockName'], $child['attrs']['className'] )
      && $child['blockName'] === 'core/group'
      && (
        strpos( (string) $child['attrs']['className'], 'ceiba-hero__top-extra' ) !== false
        || strpos( (string) $child['attrs']['className'], 'ceiba-hero__bottom-content' ) !== false
      )
    ) {
      $has_slots = true;
      break;
    }
  }
}

/**
 * Background: prefer featured image; else allow block attr; else filter fallback.
 */
$bg_url = '';
$featured = get_post_thumbnail_id();

if ( $featured ) {
  $img = wp_get_attachment_image_src( (int) $featured, 'full' );
  if ( $img ) { $bg_url = esc_url( $img[0] ); }
}

if ( ! $bg_url && ! empty( $attr['backgroundUrl'] ) ) {
  // Manual override: use block attribute if no featured image
  $bg_url = esc_url( $attr['backgroundUrl'] );
}

if ( ! $bg_url ) {
  // Final fallback via filter (keeps your original behavior)
  $bg_url = apply_filters( 'ceiba_hero_default_background_url', '' );
  $bg_url = $bg_url ? esc_url( $bg_url ) : '';
}

// Build a mobile-size variant when we have a featured image
$bg_url_full   = $bg_url;
$bg_url_mobile = $bg_url;

if ( $featured ) {
  // Try a registered mobile size first; then fall back sanely
  $mobile = wp_get_attachment_image_src( (int) $featured, 'hero-mobile' );
  if ( ! $mobile ) { $mobile = wp_get_attachment_image_src( (int) $featured, 'medium_large' ); }
  if ( ! $mobile ) { $mobile = wp_get_attachment_image_src( (int) $featured, 'large' ); }
  if ( ! $mobile ) { $mobile = wp_get_attachment_image_src( (int) $featured, 'full' ); }
  if ( is_array( $mobile ) && ! empty( $mobile[0] ) ) {
    $bg_url_mobile = esc_url( $mobile[0] );
  }
}

/**
 * Alignment + wrapper
 */
$align_class = '';
if ( is_string( $attr['align'] ) && $attr['align'] !== '' ) {
  if ( $attr['align'] === 'full' ) {
    $align_class = ' alignfull';
  } elseif ( $attr['align'] === 'wide' ) {
    $align_class = ' alignwide';
  }
}

$unique_id = uniqid( 'ceiba-hero-' );
$wrapper   = get_block_wrapper_attributes( [ 'id' => $unique_id, 'class' => 'ceiba-hero fade-up' . $align_class ] );

/**
 * Resolve slot HTML (or legacy fallback ONLY when no slots exist).
 */
$top_html    = $has_slots ? ceiba_hero_render_group_by_class( $block, 'ceiba-hero__top-extra' ) : '';
$bottom_html = $has_slots ? ceiba_hero_render_group_by_class( $block, 'ceiba-hero__bottom-content' ) : ( isset( $content ) ? (string) $content : '' );

$has_content = static function( $html ) {
  return trim( wp_strip_all_tags( (string) $html ) ) !== '';
};

/**
 * Output
 */
echo '<section ' . $wrapper . '>';

  // Inline style for per-instance background URLs (desktop + mobile)
  echo '  <style>';
    if ( $bg_url_full ) {
      echo '#' . esc_attr( $unique_id ) . ' .ceiba-hero__top{background-image:url(' . esc_url( $bg_url_full ) . ');}';
    }
    if ( $bg_url_mobile ) {
      echo '@media (max-width: 768px){#' . esc_attr( $unique_id ) . ' .ceiba-hero__top{background-image:url(' . esc_url( $bg_url_mobile ) . ') !important;}}';
    }
  echo '</style>';

  // Top: background + H1 + top slot
  echo '  <div class="ceiba-hero__top">';
  echo '    <div class="ceiba-hero__backdrop" aria-hidden="true"></div>';
  echo '    <div class="ceiba-hero__top__inner">';
    if ( ! empty( $attr['title'] ) ) {
      echo '      <h1 class="ceiba-hero__title">' . wp_kses_post( $attr['title'] ) . '</h1>';
    }
    if ( $has_content( $top_html ) ) {
      echo $top_html; // only the top slot lives here
    }
  echo '    </div>';
  echo '  </div>';

  echo '<div class="ceiba-hero__top__green-border"></div>';

  // Bottom: only render if bottom slot has content (or legacy $content when no slots exist)
  if ( $has_content( $bottom_html ) ) {
    echo '  <div class="ceiba-hero__bottom">';
    echo '    <div class="ceiba-hero__bottom__inner">';
    echo '      <div class="ceiba-hero__content">' . $bottom_html . '</div>';
    echo '    </div>';
    echo '  </div>';
  }

echo '</section>';
