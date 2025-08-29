<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$attr = wp_parse_args( $attributes, [
  'title'   => '',
  'intro'   => '',
  'members' => [],
] );

$members = is_array( $attr['members'] ) ? $attr['members'] : [];
$wrapper = get_block_wrapper_attributes( [ 'class' => 'ceiba-team-grid' ] );

ob_start(); ?>
<section class="ceiba-team-grid__inner">
  <?php if ( ! empty( $attr['title'] ) ) : ?>
    <h2 class="ceiba-team-grid__title"><?php echo esc_html( $attr['title'] ); ?></h2>
  <?php endif; ?>

  <?php if ( ! empty( $attr['intro'] ) ) : ?>
    <div class="ceiba-team-grid__intro"><?php echo wp_kses_post( $attr['intro'] ); ?></div>
  <?php endif; ?>

  <?php if ( ! empty( $members ) ) : ?>
    <div class="ceiba-team-grid__grid">
      <?php foreach ( $members as $m ) :
        $m = wp_parse_args( $m, [
          'imageId'  => 0,
          'imageUrl' => '',
          'imageAlt' => '',
          'name'     => '',
          'title'    => '',
          'bio'      => '',
        ] );

        $img = '';
        if ( $m['imageId'] ) {
          $img = wp_get_attachment_image(
            (int) $m['imageId'],
            'thumbnail',
            false,
            [
              'class'    => 'ceiba-team-grid__img',
              'loading'  => 'lazy',
              'decoding' => 'async',
              'alt'      => esc_attr( $m['imageAlt'] ),
              'style'    => 'object-fit:cover;object-position:center;',
            ]
          );
        } elseif ( ! empty( $m['imageUrl'] ) ) {
          $img = sprintf(
            '<img class="ceiba-team-grid__img" src="%s" alt="%s" loading="lazy" decoding="async" style="object-fit:cover;object-position:center;" />',
            esc_url( $m['imageUrl'] ),
            esc_attr( $m['imageAlt'] )
          );
        }
      ?>
        <article class="ceiba-team-grid__item">
          <?php if ( $img ) : ?>
            <div class="ceiba-team-grid__media"><?php echo $img; ?></div>
          <?php endif; ?>
          <?php if ( $m['name'] ) : ?>
            <h3 class="ceiba-team-grid__name"><?php echo esc_html( $m['name'] ); ?></h3>
          <?php endif; ?>
          <?php if ( $m['title'] ) : ?>
            <h4 class="ceiba-team-grid__role"><?php echo esc_html( $m['title'] ); ?></h4>
          <?php endif; ?>
          <?php if ( $m['bio'] ) : ?>
            <details class="ceiba-team-grid__bio">
              <summary class="ceiba-team-grid__bio-toggle"><?php echo esc_html__( 'Read bio', 'ceiba' ); ?></summary>
              <div class="ceiba-team-grid__bio-content"><?php echo wp_kses_post( $m['bio'] ); ?></div>
            </details>
          <?php endif; ?>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>
<?php echo sprintf( '<div %s>%s</div>', $wrapper, ob_get_clean() );
