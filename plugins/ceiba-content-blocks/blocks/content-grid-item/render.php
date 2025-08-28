<?php
if ( ! defined( 'ABSPATH' ) ) exit;

return function( $attributes = [] ) {
  $mediaURL = $attributes['mediaURL'] ?? '';
  $alt      = $attributes['alt'] ?? '';
  $heading  = $attributes['heading'] ?? '';
  $text     = $attributes['text'] ?? '';
  $tag      = in_array( $attributes['tag'] ?? 'h3', [ 'h2','h3','h4' ], true ) ? $attributes['tag'] : 'h3';

  ob_start(); ?>
  <div class="ceiba-content-grid-item">
    <?php if ( $mediaURL ) : ?>
      <div class="ceiba-content-grid-item__image">
        <img src="<?php echo esc_url( $mediaURL ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy" decoding="async" />
      </div>
    <?php endif; ?>

    <div class="ceiba-content-grid-item__content">
      <?php if ( $heading ) printf( '<%1$s class="ceiba-content-grid-item__title">%2$s</%1$s>', esc_html( $tag ), wp_kses_post( $heading ) ); ?>
      <?php if ( $text ) echo '<p class="ceiba-content-grid-item__text">' . wp_kses_post( $text ) . '</p>'; ?>
    </div>
  </div>
  <?php
  return ob_get_clean();
};
