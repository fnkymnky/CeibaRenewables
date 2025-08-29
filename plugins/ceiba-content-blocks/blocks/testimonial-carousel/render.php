<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$ids = [];
if ( isset( $attributes['includeIds'] ) && is_array( $attributes['includeIds'] ) ) {
    $ids = array_values( array_unique( array_map( 'absint', $attributes['includeIds'] ) ) );
    $ids = array_filter( $ids );
    if ( count( $ids ) > 6 ) $ids = array_slice( $ids, 0, 6 );
}

$args = [
    'post_type'           => 'testimonial',
    'post_status'         => 'publish',
    'ignore_sticky_posts' => true,
    'no_found_rows'       => true,
];

if ( ! empty( $ids ) ) {
    $args['post__in']       = $ids;
    $args['orderby']        = 'post__in';
    $args['posts_per_page'] = count( $ids );
} else {
    $args['posts_per_page'] = 6;
}

$q = new WP_Query( $args );
if ( ! $q->have_posts() ) {
    wp_reset_postdata();
    return;
}


echo '<div class="wp-block-ceiba-testimonials-carousel cb-tc swiper">';
echo '  <div class="swiper-wrapper">';

while ( $q->have_posts() ) {
    $q->the_post();
    $pid   = get_the_ID();
    $title = get_the_title( $pid );
    $role  = get_post_meta( $pid, 'ceiba_role', true );
    $quote = get_post_meta( $pid, 'ceiba_quote', true );

    echo '    <div class="swiper-slide">';
    echo '      <article class="cb-tc-card">';
    if ( has_post_thumbnail( $pid ) ) {
        echo '        <div class="cb-tc-card__logo">' . get_the_post_thumbnail( $pid, 'medium', [ 'loading' => 'lazy', 'decoding' => 'async' ] ) . '</div>';
    }
    if ( $quote ) {
        echo '        <blockquote class="cb-tc-card__quote">' . wp_kses_post( $quote ) . '</blockquote>';
    }
    echo '        <div class="cb-tc-card__meta">';
    if ( $title ) echo '          <div class="cb-tc-card__company">' . esc_html( $title ) . '</div>';
    if ( $role )  echo '          <div class="cb-tc-card__person">'  . esc_html( $role )  . '</div>';
    echo '        </div>';
    echo '      </article>';
    echo '    </div>';
}

echo '  </div>'; // .swiper-wrapper

echo '  <div class="cb-tc__nav swiper-nav">';
echo '    <button class="cb-tc__navbtn swiper-button-prev" type="button" aria-label="' . esc_attr__( 'Previous', 'ceiba' ) . '"></button>';
echo '    <button class="cb-tc__navbtn swiper-button-next" type="button" aria-label="' . esc_attr__( 'Next', 'ceiba' ) . '"></button>';
echo '  </div>';

echo '</div>';

wp_reset_postdata();
