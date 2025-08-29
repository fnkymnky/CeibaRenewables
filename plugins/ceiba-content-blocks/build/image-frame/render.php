<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$attr = wp_parse_args( $attributes, [
  'imageId' => 0,
  'imageUrl' => '',
  'imageAlt' => '',
  'imageFit' => 'cover',
  'focalPoint' => [ 'x' => 0.5, 'y' => 0.5 ],
  'proportion' => 'wide',
] );

$classes = [ 'ceiba-image-frame' ];
$classes[] = 'is-' . sanitize_html_class( $attr['proportion'] );
$wrapper = get_block_wrapper_attributes( [ 'class' => implode( ' ', $classes ) ] );

$fit = in_array( $attr['imageFit'], [ 'cover', 'fill', 'stretch' ], true ) ? $attr['imageFit'] : 'cover';
$object_fit = $fit === 'cover' ? 'cover' : ( $fit === 'stretch' ? 'fill' : 'contain' );
$object_pos = sprintf(
  '%.2f%% %.2f%%',
  isset($attr['focalPoint']['x']) ? $attr['focalPoint']['x'] * 100 : 50,
  isset($attr['focalPoint']['y']) ? $attr['focalPoint']['y'] * 100 : 50
);

$img_html = '';
if ( $attr['imageId'] ) {
  $img_html = wp_get_attachment_image( $attr['imageId'], 'full', false, [
    'class'   => 'ceiba-image-frame__img',
    'loading' => 'lazy',
    'decoding'=> 'async',
    'alt'     => esc_attr( $attr['imageAlt'] ),
    'style'   => sprintf( 'object-fit:%s;object-position:%s;', esc_attr($object_fit), esc_attr($object_pos) ),
  ] );
} elseif ( $attr['imageUrl'] ) {
  $img_html = sprintf(
    '<img class="ceiba-image-frame__img" src="%s" alt="%s" loading="lazy" decoding="async" style="object-fit:%s;object-position:%s;" />',
    esc_url( $attr['imageUrl'] ),
    esc_attr( $attr['imageAlt'] ),
    esc_attr( $object_fit ),
    esc_attr( $object_pos )
  );
}

if ( ! $img_html ) {
  echo sprintf( '<div %s><div class="ceiba-image-frame__placeholder">%s</div></div>', $wrapper, esc_html__( 'Select an image.', 'ceiba' ) );
  return;
}

echo sprintf( '<div %s>%s</div>', $wrapper, $img_html );
