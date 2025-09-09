<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$attrs = wp_parse_args( $attributes, [ 'includeIds' => [] ] );

$ids = array_values(array_unique(array_map('absint', is_array($attrs['includeIds']) ? $attrs['includeIds'] : [] )));
$ids = array_filter($ids);
if (count($ids) > 6) $ids = array_slice($ids, 0, 6);

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

// Match Insights wrapper + structure
$wrapper = get_block_wrapper_attributes( [ 'class' => 'ceiba-ic' ] );

ob_start(); ?>
  <div class="ceiba-ic__inner">
    <div class="ceiba-ic__track">
      <?php while ($q->have_posts()) : $q->the_post();
        $pid   = get_the_ID();
        $title = get_the_title();
        $url   = get_permalink();
        $img   = get_the_post_thumbnail_url($pid, 'large');
        $bg    = $img ? ' style="background-image:url(' . esc_url($img) . ')"' : '';
      ?>
        <article class="ceiba-ic__slide"<?php echo $bg; ?>>
          <div class="ceiba-ic__slide__backdrop" aria-hidden="true"></div>
          <div class="ceiba-insight-card__body">
            <h5 class="ceiba-insight-card__label"><?php echo esc_html__('Case Study','ceiba'); ?></h5>
            <h3 class="ceiba-insight-card__title"><a href="<?php echo esc_url($url); ?>"><?php echo esc_html($title); ?></a></h3>
          </div>
        </article>
      <?php endwhile; ?>
    </div>
    <div class="cb-tc__nav swiper-nav">
      <button class="swiper-button-nav swiper-button-prev" type="button" aria-label="<?php echo esc_attr__('Previous','ceiba'); ?>"></button>
      <button class="swiper-button-nav swiper-button-next" type="button" aria-label="<?php echo esc_attr__('Next','ceiba'); ?>"></button>
    </div>
  </div>
<?php
wp_reset_postdata();
echo sprintf('<section %s>%s</section>', $wrapper, ob_get_clean());

