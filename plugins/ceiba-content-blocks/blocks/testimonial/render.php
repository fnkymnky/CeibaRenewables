<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$pid = isset($attributes['postId']) ? absint($attributes['postId']) : 0;
$wrapper = get_block_wrapper_attributes([ 'class' => 'cb-t-single fade-up' ]);

if ( ! $pid ) {
    echo '<div ' . $wrapper . '><p class="cb-empty">Select a testimonial.</p></div>';
    return;
}

$post = get_post($pid);
if ( ! $post || $post->post_type !== 'testimonial' ) {
    echo '<div ' . $wrapper . '><p class="cb-empty">Invalid testimonial.</p></div>';
    return;
}

$title = get_the_title($pid);
$role  = get_post_meta($pid, 'ceiba_role', true);
$quote = get_post_meta($pid, 'ceiba_quote', true);

echo '<div ' . $wrapper . '>';
echo '  <article class="cb-tc-card">';
if ( has_post_thumbnail($pid) ) {
    echo '    <div class="cb-tc-card__logo">' . get_the_post_thumbnail($pid, 'medium', [ 'loading' => 'lazy', 'decoding' => 'async' ]) . '</div>';
}
if ( $quote ) {
    echo '    <blockquote class="cb-tc-card__quote">' . wp_kses_post($quote) . '</blockquote>';
}
echo '    <div class="cb-tc-card__meta">';
if ( $role )  echo '      <div class="cb-tc-card__person">'  . esc_html($role)  . '</div>';
// if ( $title ) echo '      <div class="cb-tc-card__company">' . esc_html($title) . '</div>';
echo '    </div>';
echo '  </article>';
echo '</div>';
