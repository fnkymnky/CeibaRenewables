<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$attrs = wp_parse_args( $attributes, [
  'imageId'   => 0,
  'imageSize' => 'large',
  'imageLeft' => false,
] );

$image_id   = absint( $attrs['imageId'] );
$image_size = is_string( $attrs['imageSize'] ) && $attrs['imageSize'] ? $attrs['imageSize'] : 'large';
$image_left = ! empty( $attrs['imageLeft'] );

$wrapper_classes = [ 'ceiba-callout fade-up', $image_left ? 'is-image-left' : 'is-image-right' ];
$wrapper = get_block_wrapper_attributes( [ 'class' => implode(' ', $wrapper_classes) ] );

$image_html = '';
if ( $image_id ) {
    $image_html = wp_get_attachment_image( $image_id, $image_size, false, [ 'class' => 'ceiba-callout__img', 'loading' => 'lazy' ] );
}

// Render inner content robustly (dynamic parent): prefer provided $content; fallback to rendering child blocks
$body_html = '';
if ( isset( $content ) && trim( $content ) !== '' ) {
    $body_html = $content; // already contains nested markup when save() used InnerBlocks.Content
} elseif ( isset( $block ) && ! empty( $block->inner_blocks ) && is_array( $block->inner_blocks ) ) {
    foreach ( $block->inner_blocks as $inner ) {
        if ( isset( $inner->parsed_block ) ) {
            $body_html .= render_block( $inner->parsed_block );
        }
    }
}

ob_start(); ?>
  <div class="ceiba-callout__inner">
    <div class="ceiba-callout__media">
      <?php if ( $image_html ) : ?>
        <div class="ceiba-callout__media__frame">
          <?php echo $image_html; ?>
        </div>
      <?php endif; ?>
    </div>
    <div class="ceiba-callout__content">
      <?php echo $body_html; ?>
    </div>
  </div>
<?php
echo sprintf('<section %s>%s</section>', $wrapper, ob_get_clean());
