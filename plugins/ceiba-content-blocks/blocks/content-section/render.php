<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$attr = wp_parse_args( $attributes, [
  'title' => '',
  'content' => '',
  'imageId' => 0,
  'imageUrl' => '',
  'imageAlt' => '',
  'imageSide' => 'left',
  'imageFit' => 'cover',
  'focalPoint' => [ 'x' => 0.5, 'y' => 0.5 ],
  'enablePrimary' => false,
  'primaryLabel' => '',
  'primaryLink' => [ 'url' => '', 'opensInNewTab' => false ],
  'enableSecondary' => false,
  'secondaryLabel' => '',
  'secondaryLink' => [ 'url' => '', 'opensInNewTab' => false ],
] );

$align_class = ( isset($attr['imageSide']) && $attr['imageSide'] === 'right' ) ? 'has-media-right' : 'has-media-left';
$wrapper = get_block_wrapper_attributes( [ 'class' => 'ceiba-csct ' . $align_class ] );

$fit = in_array( $attr['imageFit'], [ 'cover', 'fill', 'stretch' ], true ) ? $attr['imageFit'] : 'cover';
$object_fit = $fit === 'cover' ? 'cover' : ( $fit === 'stretch' ? 'fill' : 'contain' );
$object_pos = sprintf(
  '%.2f%% %.2f%%',
  isset($attr['focalPoint']['x']) ? $attr['focalPoint']['x'] * 100 : 50,
  isset($attr['focalPoint']['y']) ? $attr['focalPoint']['y'] * 100 : 50
);

$img_html = '';
if ( $attr['imageId'] ) {
  $img_html = wp_get_attachment_image( $attr['imageId'], 'large', false, [
    'class'   => 'ceiba-csct__img',
    'loading' => 'lazy',
    'decoding'=> 'async',
    'alt'     => esc_attr( $attr['imageAlt'] ),
    'style'   => sprintf( 'object-fit:%s;object-position:%s;', esc_attr($object_fit), esc_attr($object_pos) ),
  ] );
} elseif ( $attr['imageUrl'] ) {
  $img_html = sprintf(
    '<img class="ceiba-csct__img" src="%s" alt="%s" loading="lazy" decoding="async" style="object-fit:%s;object-position:%s;" />',
    esc_url( $attr['imageUrl'] ),
    esc_attr( $attr['imageAlt'] ),
    esc_attr( $object_fit ),
    esc_attr( $object_pos )
  );
}

$has_primary   = $attr['enablePrimary'] && ! empty( $attr['primaryLabel'] )   && ! empty( $attr['primaryLink']['url'] );
$has_secondary = $attr['enableSecondary'] && ! empty( $attr['secondaryLabel'] ) && ! empty( $attr['secondaryLink']['url'] );
$img_first     = $attr['imageSide'] === 'left';

// Prefer rendered inner blocks when present; fallback to legacy attribute content
$body_html = '';
if ( isset( $content ) && trim( $content ) !== '' ) {
  // When save() outputs InnerBlocks.Content, $content will contain nested markup
  $body_html = $content;
} elseif ( isset( $block ) && ! empty( $block->inner_blocks ) && is_array( $block->inner_blocks ) ) {
  // Render nested blocks server-side as a fallback
  foreach ( $block->inner_blocks as $inner ) {
    if ( isset( $inner->parsed_block ) ) {
      $body_html .= render_block( $inner->parsed_block );
    }
  }
}
// Final fallback to legacy attribute content (pre-InnerBlocks)
if ( $body_html === '' ) {
  $body_html = wp_kses_post( $attr['content'] );
}

ob_start(); ?>
<section class="ceiba-csct__inner">
  <div class="ceiba-csct__grid <?php echo $img_first ? 'is-media-left' : 'is-media-right'; ?>">
    <?php if ( $img_first ) : ?>
      <div class="ceiba-csct__col ceiba-csct__col--media">
        <?php echo $img_html ?: ''; ?>
      </div>
      <div class="ceiba-csct__col ceiba-csct__col--body">
        <?php if ( ! empty( $attr['title'] ) ) : ?>
          <h2 class="ceiba-csct__title"><?php echo esc_html( $attr['title'] ); ?></h2>
        <?php endif; ?>
        <div class="ceiba-csct__content">
          <?php echo $body_html; ?>
        </div>
        <?php if ( $has_primary || $has_secondary ) : ?>
          <div class="ceiba-csct__cta">
            <?php if ( $has_primary ) :
              $t1 = ! empty( $attr['primaryLink']['opensInNewTab'] ) ? ' target="_blank" rel="noopener"' : ''; ?>
              <a class="btn btn--primary" href="<?php echo esc_url( $attr['primaryLink']['url'] ); ?>"<?php echo $t1; ?>>
                <?php echo esc_html( $attr['primaryLabel'] ); ?>
              </a>
            <?php endif; ?>
            <?php if ( $has_secondary ) :
              $t2 = ! empty( $attr['secondaryLink']['opensInNewTab'] ) ? ' target="_blank" rel="noopener"' : ''; ?>
              <a class="btn btn--secondary" href="<?php echo esc_url( $attr['secondaryLink']['url'] ); ?>"<?php echo $t2; ?>>
                <?php echo esc_html( $attr['secondaryLabel'] ); ?>
              </a>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    <?php else : ?>
      <div class="ceiba-csct__col ceiba-csct__col--body">
        <?php if ( ! empty( $attr['title'] ) ) : ?>
          <h2 class="ceiba-csct__title"><?php echo esc_html( $attr['title'] ); ?></h2>
        <?php endif; ?>
        <div class="ceiba-csct__content">
          <?php echo $body_html; ?>
        </div>
        <?php if ( $has_primary || $has_secondary ) : ?>
          <div class="ceiba-csct__cta">
            <?php if ( $has_primary ) :
              $t1 = ! empty( $attr['primaryLink']['opensInNewTab'] ) ? ' target="_blank" rel="noopener"' : ''; ?>
              <a class="btn btn--primary" href="<?php echo esc_url( $attr['primaryLink']['url'] ); ?>"<?php echo $t1; ?>>
                <?php echo esc_html( $attr['primaryLabel'] ); ?>
              </a>
            <?php endif; ?>
            <?php if ( $has_secondary ) :
              $t2 = ! empty( $attr['secondaryLink']['opensInNewTab'] ) ? ' target="_blank" rel="noopener"' : ''; ?>
              <a class="btn btn--secondary" href="<?php echo esc_url( $attr['secondaryLink']['url'] ); ?>"<?php echo $t2; ?>>
                <?php echo esc_html( $attr['secondaryLabel'] ); ?>
              </a>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="ceiba-csct__col ceiba-csct__col--media">
        <?php echo $img_html ?: ''; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php
echo sprintf('<div %s>%s</div>', $wrapper, ob_get_clean() );
