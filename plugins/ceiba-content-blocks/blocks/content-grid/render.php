<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$attr = wp_parse_args( $attributes, [
  'columns'   => 3,
  'itemCount' => 4,
  'gap'       => '1.25rem',
] );

$columns = max(1, min(6, (int) $attr['columns']));
$gap     = $attr['gap'];

$wrapper = get_block_wrapper_attributes( [
  'class' => 'ceiba-content-grid columns-' . $columns,
  'style' => sprintf('--ceiba-grid-gap:%s;--ceiba-grid-columns:%d;', esc_attr($gap), $columns),
] );

$inner_html = $content ? do_blocks( $content ) : '';

echo sprintf('<div %s>%s</div>', $wrapper, $inner_html);
