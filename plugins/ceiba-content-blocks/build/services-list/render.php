<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Use selected parent if provided; otherwise locate the Services page
$attr = wp_parse_args( (array) $attributes, [ 'parentId' => 0 ] );
$parent_id = absint( $attr['parentId'] );
if ( ! $parent_id ) {
    // No parent selected; render nothing
    return '';
}

// Get direct child pages
$children = get_children( [
    'post_parent' => $parent_id,
    'post_type'   => 'page',
    'post_status' => 'publish',
    'orderby'     => 'menu_order',
    'order'       => 'ASC',
] );
if ( empty( $children ) ) {
    return '';
}

$wrapper = get_block_wrapper_attributes( [ 'class' => 'ceiba-page-list fade-up' ] );

echo '<section ' . $wrapper . '>';
echo '  <div class="ceiba-pl">';
echo '    <div class="ceiba-services-grid">';
foreach ( $children as $child ) {
    $permalink = get_permalink( $child );
    $title     = get_the_title( $child );
    $thumb     = get_the_post_thumbnail( $child, 'page-list-360', [
        'class'    => 'ceiba-service-card__image',
        'alt'      => esc_attr( $title ),
        'loading'  => 'lazy',
        'decoding' => 'async',
        'sizes'    => '(min-width: 426px) 360px, 320px'
    ] );

    echo '      <article class="ceiba-service-card">';
    echo '      <a href="' . esc_url( $permalink ) . '" class="ceiba-service-card__link">';
    if ( $thumb ) {
        echo '        <div class="ceiba-service-card__media">' . $thumb . '</div>';
    }
    echo '        <h3 class="ceiba-service-card__title">' . esc_html( $title ) . '</h3>';
    echo '      </a>';
    echo '    </article>';
}
echo '    </div>';
echo '    <div class="swiper-pagination"></div>';
echo '  </div>';
echo '</section>';
