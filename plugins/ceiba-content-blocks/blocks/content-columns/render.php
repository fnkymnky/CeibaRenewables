<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$attr = wp_parse_args( $attributes, [
  'title'         => '',
  'introContent'  => '',
  'outroContent'  => '',
  'columnsCount'  => 3,
  'columns'       => [],
  'enablePrimary' => false,
  'primaryLabel'  => '',
  'primaryLink'   => [ 'url' => '', 'opensInNewTab' => false ],
] );

$count = (int) $attr['columnsCount'];
if ( $count < 1 ) $count = 1;
if ( $count > 4 ) $count = 4;

$cols = is_array( $attr['columns'] ) ? $attr['columns'] : [];
$cols = array_slice( $cols, 0, 4 ); // ensure max 4
for ( $i = 0; $i < $count; $i++ ) {
    if ( ! isset( $cols[$i] ) ) $cols[$i] = [ 'imageId'=>0, 'imageUrl'=>'', 'imageAlt'=>'', 'heading'=>'', 'text'=>'' ];
}

$wrapper = get_block_wrapper_attributes( [ 'class' => 'ceiba-mcols' ] );

$has_cta = ! empty( $attr['enablePrimary'] ) && ! empty( $attr['primaryLabel'] ) && ! empty( $attr['primaryLink']['url'] );
$tgt = ! empty( $attr['primaryLink']['opensInNewTab'] ) ? ' target="_blank" rel="noopener"' : '';

ob_start(); ?>
<section class="ceiba-mcols__inner">
  <?php if ( ! empty( $attr['title'] ) ) : ?>
    <h2 class="ceiba-mcols__title"><?php echo esc_html( $attr['title'] ); ?></h2>
  <?php endif; ?>

  <?php if ( ! empty( $attr['introContent'] ) ) : ?>
    <div class="ceiba-mcols__intro"><?php echo wp_kses_post( $attr['introContent'] ); ?></div>
  <?php endif; ?>

  <div class="ceiba-mcols__grid cols-<?php echo (int) $count; ?>">
    <?php for ( $i = 0; $i < $count; $i++ ) :
      $c = wp_parse_args( $cols[$i], [ 'imageId'=>0, 'imageUrl'=>'', 'imageAlt'=>'', 'heading'=>'', 'text'=>'' ] );
      $thumb = '';
      if ( $c['imageId'] ) {
        $thumb = wp_get_attachment_image( (int) $c['imageId'], 'thumbnail', false, [ 'class' => 'ceiba-mcols__thumb', 'loading'=>'lazy', 'decoding'=>'async', 'alt'=> esc_attr( $c['imageAlt'] ) ] );
      } elseif ( ! empty( $c['imageUrl'] ) ) {
        $thumb = sprintf( '<img class="ceiba-mcols__thumb" src="%s" alt="%s" loading="lazy" decoding="async" />',
          esc_url( $c['imageUrl'] ), esc_attr( $c['imageAlt'] ) );
      }
      ?>
      <article class="ceiba-mcols__item">
        <?php if ( $thumb ) : ?><div class="ceiba-mcols__media"><?php echo $thumb; ?></div><?php endif; ?>
        <?php if ( ! empty( $c['heading'] ) ) : ?><h3 class="ceiba-mcols__heading"><?php echo esc_html( $c['heading'] ); ?></h3><?php endif; ?>
        <?php if ( ! empty( $c['text'] ) ) : ?><p class="ceiba-mcols__text"><?php echo esc_html( $c['text'] ); ?></p><?php endif; ?>
      </article>
    <?php endfor; ?>
  </div>

  <?php if ( ! empty( $attr['outroContent'] ) ) : ?>
    <div class="ceiba-mcols__outro"><?php echo wp_kses_post( $attr['outroContent'] ); ?></div>
  <?php endif; ?>

  <?php if ( $has_cta ) : ?>
    <div class="ceiba-mcols__cta">
      <a class="btn btn--primary" href="<?php echo esc_url( $attr['primaryLink']['url'] ); ?>"<?php echo $tgt; ?>>
        <?php echo esc_html( $attr['primaryLabel'] ); ?>
      </a>
    </div>
  <?php endif; ?>
</section>
<?php
echo sprintf( '<div %s>%s</div>', $wrapper, ob_get_clean() );
