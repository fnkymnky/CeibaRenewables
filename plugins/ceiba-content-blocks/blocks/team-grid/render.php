<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Compute align class: force alignfull when a background/gradient is set
$has_bg = ! empty( $attributes['backgroundColor'] )
  || ( isset( $attributes['style']['color']['background'] ) && $attributes['style']['color']['background'] !== '' )
  || ( isset( $attributes['style']['color']['gradient'] ) && $attributes['style']['color']['gradient'] !== '' );

$extra_align = '';
if ( $has_bg ) {
  $extra_align = ' alignfull';
} elseif ( ! empty( $attributes['align'] ) ) {
  $extra_align = ' align' . $attributes['align'];
}

$wrapper = get_block_wrapper_attributes( [ 'class' => 'ceiba-team-grid fade-up' . $extra_align ] );

ob_start(); ?>
<section class="ceiba-team-grid__inner">
  <?php
    if ( trim( wp_strip_all_tags( (string) $content ) ) !== '' ) {
      // New structure: InnerBlocks handle head + grid area + items.
      echo $content;
    } else {
      // Legacy fallback: render from attributes (title, intro, members[])
      $attr = wp_parse_args( $attributes, [ 'title' => '', 'intro' => '', 'members' => [] ] );
      if ( ! empty( $attr['title'] ) ) {
        echo '<h2 class="ceiba-team-grid__title">' . esc_html( $attr['title'] ) . '</h2>';
      }
      if ( ! empty( $attr['intro'] ) ) {
        echo '<div class="ceiba-team-grid__intro">' . wp_kses_post( $attr['intro'] ) . '</div>';
      }
      $members = is_array( $attr['members'] ) ? $attr['members'] : [];
      if ( $members ) {
        echo '<div class="ceiba-team-grid__grid">';
        foreach ( $members as $m ) {
          $m = wp_parse_args( $m, [ 'imageId' => 0, 'imageUrl' => '', 'imageAlt' => '', 'name' => '', 'title' => '', 'bio' => '' ] );
          $img = '';
          if ( $m['imageId'] ) {
            $img = wp_get_attachment_image( (int) $m['imageId'], 'page-list-320', false, [ 'class' => 'ceiba-team-grid__img', 'loading' => 'lazy', 'decoding' => 'async', 'alt' => esc_attr( $m['imageAlt'] ) ] );
          } elseif ( ! empty( $m['imageUrl'] ) ) {
            $img = sprintf( '<img class="ceiba-team-grid__img" src="%s" alt="%s" loading="lazy" decoding="async" />', esc_url( $m['imageUrl'] ), esc_attr( $m['imageAlt'] ) );
          }
          echo '<article class="ceiba-team-grid__item">';
          if ( $img ) echo '<div class="ceiba-team-grid__media">' . $img . '</div>';
          if ( $m['name'] ) echo '<h3 class="ceiba-team-grid__name">' . esc_html( $m['name'] ) . '</h3>';
          if ( $m['title'] ) echo '<h4 class="ceiba-team-grid__role">' . esc_html( $m['title'] ) . '</h4>';
          if ( $m['bio'] ) {
            echo '<details class="ceiba-team-grid__bio">';
            echo '<summary class="ceiba-team-grid__bio-toggle">' . esc_html__( 'Read bio', 'ceiba' ) . '</summary>';
            echo '<div class="ceiba-team-grid__bio-content">' . wp_kses_post( $m['bio'] ) . '</div>';
            echo '</details>';
          }
          echo '</article>';
        }
        echo '</div>';
      }
    }
  ?>
</section>
<?php echo sprintf( '<div %s>%s</div>', $wrapper, ob_get_clean() );
