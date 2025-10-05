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
  'class' => 'fade-up content-grid columns-' . $columns,
  'style' => sprintf('--content-grid-gap:%s;--content-grid-columns:%d;', esc_attr($gap), $columns),
] );

$inner_html = $content ? do_blocks( $content ) : '';

echo sprintf('<div %s>%s</div>', $wrapper, $inner_html);
