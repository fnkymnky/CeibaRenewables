<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Render only meaningful inner blocks and avoid empty headings.
$html = '';

if ( isset( $block ) && is_object( $block ) && ! empty( $block->parsed_block['innerBlocks'] ) ) {
  foreach ( $block->parsed_block['innerBlocks'] as $child ) {
    $name     = isset( $child['blockName'] ) ? $child['blockName'] : '';
    $attrs    = isset( $child['attrs'] ) ? $child['attrs'] : [];
    $rendered = render_block( $child );

    // Helper flags
    $has_text = trim( wp_strip_all_tags( $rendered ) ) !== '';
    $has_media = ( stripos( $rendered, '<img' ) !== false ) || ( stripos( $rendered, '<figure' ) !== false ) || ( stripos( $rendered, '<svg' ) !== false );

    if ( $name === 'core/heading' ) {
      // Require non-empty content for headings
      $text = '';
      if ( isset( $attrs['content'] ) ) {
        $text = trim( wp_strip_all_tags( $attrs['content'] ) );
      } else {
        $text = trim( wp_strip_all_tags( $rendered ) );
      }
      if ( $text !== '' ) {
        $html .= $rendered;
      }
      continue;
    }

    if ( $name === 'core/image' ) {
      // Allow if we have an id/url or rendered contains an <img>
      if ( ! empty( $attrs['id'] ) || ! empty( $attrs['url'] ) || stripos( $rendered, '<img' ) !== false ) {
        $html .= $rendered;
      }
      continue;
    }

    if ( $name === 'core/details' ) {
      // Allow if summary/body produce any output
      if ( $has_text || $has_media ) {
        $html .= $rendered;
      }
      continue;
    }

    // Fallback: render blocks that produce visible content
    if ( $has_text || $has_media ) {
      $html .= $rendered;
    }
  }
}

if ( trim( $html ) !== '' ) {
  $wrapper = get_block_wrapper_attributes( [ 'class' => 'ceiba-team-grid__item' ] );
  echo sprintf( '<article %s>%s</article>', $wrapper, $html );
}
