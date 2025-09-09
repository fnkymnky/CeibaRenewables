<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$attrs = wp_parse_args( $attributes, [ 'includeIds' => [] ] );
$ids = array_values(array_unique(array_map('absint', is_array($attrs['includeIds']) ? $attrs['includeIds'] : [] )));
$ids = array_filter($ids);
if (count($ids) > 6) $ids = array_slice($ids, 0, 6);

if (empty($ids)) return '';

// Query posts in chosen order
$args = [
  'post_type'           => 'post',
  'post_status'         => 'publish',
  'ignore_sticky_posts' => true,
  'no_found_rows'       => true,
  'post__in'            => $ids,
  'orderby'             => 'post__in',
  'posts_per_page'      => count($ids),
];

$q = new WP_Query($args);
if (!$q->have_posts()) return '';

$count = (int) $q->post_count;
$wrapper = get_block_wrapper_attributes( [ 'class' => 'ceiba-ic' ] );

ob_start(); ?>
  <div class="ceiba-ic__inner">
    <div class="ceiba-ic__track">
      <?php while ($q->have_posts()) : $q->the_post();
        $pid = get_the_ID();
        $title = get_the_title();
        $url = get_permalink();
        $img = get_the_post_thumbnail_url($pid, 'large');
        $bg_style = $img ? ' style="background-image:url(' . esc_url($img) . ')"' : '';
      ?>
        <article class="ceiba-ic__slide"<?php echo $bg_style; ?>>
          <div class="ceiba-ic__slide__backdrop" aria-hidden="true"></div>
          <div class="ceiba-insight-card__body">
            <h5 class="ceiba-insight-card__label"><?php echo esc_html__('Insights','ceiba'); ?></h5>
            <h3 class="ceiba-insight-card__title"><a href="<?php echo esc_url($url); ?>"><span><?php echo esc_html($title); ?></span></a></h3>
          </div>
        </article>
      <?php endwhile; wp_reset_postdata(); ?>
    </div>
    <div class="swiper-nav">
      <button type="button" class="swiper-button-nav swiper-button-prev" aria-label="<?php echo esc_attr__('Previous','ceiba'); ?>">Previous</button>
      <button type="button" class="swiper-button-nav swiper-button-next" aria-label="<?php echo esc_attr__('Next','ceiba'); ?>">Next</button>
    </div>
  </div>
<?php
echo sprintf('<section %s>%s</section>', $wrapper, ob_get_clean());
