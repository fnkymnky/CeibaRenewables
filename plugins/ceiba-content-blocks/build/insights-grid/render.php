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
$cols = $count >= 3 ? 3 : ($count === 2 ? 2 : 1);

$wrapper = get_block_wrapper_attributes( [ 'class' => 'ceiba-insights' ] );

ob_start();
?>
  <div class="ceiba-insights__inner cols-<?php echo (int) $cols; ?>">
    <div class="ceiba-insights-grid">
      <?php while ($q->have_posts()) : $q->the_post();
        $pid = get_the_ID();
        $title = get_the_title();
        $url = get_permalink();
        $img = get_the_post_thumbnail_url($pid, $cols === 1 ? 'large' : 'medium_large');
      ?>
        <article class="ceiba-insight-card">
          <a class="ceiba-insight-card__media" href="<?php echo esc_url($url); ?>" aria-label="<?php echo esc_attr($title); ?>">
            <?php if ($img) : ?>
              <img class="ceiba-insight-card__img" src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy" decoding="async" />
            <?php endif; ?>
          </a>
          <div class="ceiba-insight-card__body">
            <h5 class="ceiba-insight-card__label"><?php echo esc_html__('Insight','ceiba'); ?></h5>
            <h3 class="ceiba-insight-card__title"><a href="<?php echo esc_url($url); ?>"><?php echo esc_html($title); ?></a></h3>
          </div>
        </article>
      <?php endwhile; wp_reset_postdata(); ?>
    </div>
    <?php if ($count > 1) : ?>
      <div class="swiper-pagination"></div>
      <button class="swiper-button-prev" aria-label="<?php echo esc_attr__('Previous','ceiba'); ?>"></button>
      <button class="swiper-button-next" aria-label="<?php echo esc_attr__('Next','ceiba'); ?>"></button>
    <?php endif; ?>
  </div>
<?php
echo sprintf('<section %s>%s</section>', $wrapper, ob_get_clean());
