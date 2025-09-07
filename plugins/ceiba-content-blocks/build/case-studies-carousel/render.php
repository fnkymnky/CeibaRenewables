<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Case Studies Carousel render — mirror Testimonials init exactly.
 * - Root classes: `cb-tc swiper`
 * - Arrows live inside the same root (siblings of `.swiper-wrapper`)
 * - Keeps editor-selected order via includeIds (min 1, max 6)
 */

$attrs = wp_parse_args( $attributes, [
  'includeIds' => [],
]);

// Reuse the exact same asset handles as Testimonials

// Normalise & bound IDs
$ids = array_values(array_unique(array_map('absint', is_array($attrs['includeIds']) ? $attrs['includeIds'] : [] )));
$ids = array_filter($ids);
if (count($ids) > 6) $ids = array_slice($ids, 0, 6);

// Query posts in chosen order (or fallback to 1 latest)
$args = [
  'post_type'           => 'case_study',
  'post_status'         => 'publish',
  'ignore_sticky_posts' => true,
  'no_found_rows'       => true,
];

if (!empty($ids)) {
  $args['post__in']       = $ids;
  $args['orderby']        = 'post__in';
  $args['posts_per_page'] = count($ids);
} else {
  $args['posts_per_page'] = 1;
}

$q = new WP_Query($args);
if (!$q->have_posts()) return '';

// ★ IMPORTANT: this matches what view.js looks for: `.cb-tc.swiper`
$out  = '<div class="wp-block-ceiba-case-studies-carousel cb-tc swiper">';
$out .= '<div class="swiper-wrapper">';

while ($q->have_posts()) {
  $q->the_post();
  $pid   = get_the_ID();
  $title = get_the_title();
  $url   = get_permalink();

  $out .= '<div class="swiper-slide">';
  $out .= '<article class="cb-cs-card">';

  if (has_post_thumbnail($pid)) {
    $out .= '<a class="cb-cs-card__media" href="' . esc_url($url) . '">';
    $out .= get_the_post_thumbnail($pid, 'large', [ 'loading' => 'lazy', 'decoding' => 'async' ]);
    $out .= '</a>';
  }

  $out .= '<div class="cb-cs-card__body">';
  $out .= '<h3 class="cb-cs-card__title"><a href="' . esc_url($url) . '">' . esc_html($title) . '</a></h3>';
  $out .= '</div>'; // body

  $out .= '</article>';
  $out .= '</div>'; // slide
}

$out .= '</div>'; // .swiper-wrapper
$out .= '<div class="swiper-pagination"></div>';

// Mirror Testimonials: only show arrows when >3 items
if ($q->post_count > 3) {
  $out .= '<div class="cb-tc__nav swiper-nav">';
  $out .= '<button class="cb-tc__navbtn swiper-button-prev" type="button" aria-label="' . esc_attr__('Previous','ceiba') . '"></button>';
  $out .= '<button class="cb-tc__navbtn swiper-button-next" type="button" aria-label="' . esc_attr__('Next','ceiba') . '"></button>';
  $out .= '</div>';
}

$out .= '</div>'; // root

wp_reset_postdata();

echo $out;
