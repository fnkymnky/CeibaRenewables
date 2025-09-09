<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$attrs = wp_parse_args( $attributes, [ 'includeIds' => [] ] );
$ids = array_values(array_unique(array_map('absint', is_array($attrs['includeIds']) ? $attrs['includeIds'] : [] )));
$ids = array_filter($ids);
if (empty($ids)) return '';

$args = [
  'post_type'           => 'testimonial',
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
$wrapper = get_block_wrapper_attributes( [ 'class' => 'ceiba-tc' ] );

ob_start(); ?>
  <div class="ceiba-tc__inner">
    <div class="ceiba-tc__track">
      <?php while ($q->have_posts()) : $q->the_post();
        $pid   = get_the_ID();
        $quote = get_post_meta($pid, 'ceiba_quote', true);
        $role  = get_post_meta($pid, 'ceiba_role', true);
        $title = get_the_title($pid);
      ?>
        <article class="ceiba-tc__slide">
          <blockquote class="ceiba-tc__quote">
            <?php if ( $quote ) : ?>
              <p><?php echo wp_kses_post( $quote ); ?></p>
            <?php endif; ?>
            <?php if ( $role ) : ?>
              <footer>
                <cite>
                  <?php echo esc_html( $role ); ?>
                  <?php if ( $title ) : ?>
                    <div class="ceiba-tc__title"><?php echo esc_html( $title ); ?></div>
                  <?php endif; ?>
                </cite>
              </footer>
            <?php endif; ?>
          </blockquote>
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
