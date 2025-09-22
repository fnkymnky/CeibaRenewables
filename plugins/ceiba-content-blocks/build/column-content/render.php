<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$attr = wp_parse_args( $attributes, [
  'columnsCount' => 4,
  'layoutMode'   => 'contained', // 'contained' | 'full'
] );

$columns = (int) $attr['columnsCount'];
if ( $columns < 1 ) $columns = 1;
if ( $columns > 4 ) $columns = 4;

// Determine wrapper alignment: force alignfull when layoutMode is full, else respect explicit align
$force_full = isset( $attr['layoutMode'] ) && $attr['layoutMode'] === 'full';
$extra_align = '';
if ( $force_full ) {
  $extra_align = ' alignfull';
} elseif ( ! empty( $attributes['align'] ) ) {
  $extra_align = ' align' . $attributes['align'];
}

$wrapper = get_block_wrapper_attributes( [ 'class' => 'ceiba-ccb cols-' . $columns . $extra_align ] );

$inner_style = 'max-width:var(--wp--style--global--content-size);width:100%;margin-left:auto;margin-right:auto;';

ob_start(); ?>
<section class="ccb__inner" style="<?php echo esc_attr( $inner_style ); ?>">
  <?php echo $content; ?>
  <?php // Expect structure from template: .ccb__top, .ccb__columns, .ccb__bottom inside ?>
</section>
<?php
echo sprintf( '<div %s>%s</div>', $wrapper, ob_get_clean() );

