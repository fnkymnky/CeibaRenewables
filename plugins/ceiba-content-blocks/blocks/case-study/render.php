<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$attrs = wp_parse_args( $attributes, [ 'postId' => 0 ] );

$wrapper_attributes = get_block_wrapper_attributes( [ 'class' => 'wp-block-ceiba-case-study' ] );

$pid = absint( $attrs['postId'] );
if ( ! $pid ) {
    echo sprintf( '<div %s><p class="ceiba-cs-empty">%s</p></div>',
        $wrapper_attributes,
        esc_html__( 'Select a Case Study post in block settings.', 'ceiba' )
    );
    return;
}

$post = get_post( $pid );
if ( ! $post || $post->post_type !== 'case_study' ) {
    echo sprintf( '<div %s><p class="ceiba-cs-empty">%s</p></div>',
        $wrapper_attributes,
        esc_html__( 'Invalid Case Study selection.', 'ceiba' )
    );
    return;
}

$title   = get_the_title( $pid );
$url     = get_permalink( $pid );
$excerpt = has_excerpt( $pid )
    ? get_the_excerpt( $pid )
    : wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', $pid ) ), 22 );

ob_start();

$img = get_the_post_thumbnail_url( $pid, 'large' );
$bg  = $img ? ' style="background-image:url(' . esc_url( $img ) . ')"' : '';
?>

<article class="ceiba-ic__slide ceiba-ic__slide--single"<?php echo $bg; ?>>
    <div class="ceiba-ic__slide__backdrop" aria-hidden="true"></div>
    <div class="ceiba-insight-card__body">
        <h5 class="ceiba-insight-card__label"><?php echo esc_html__('Case Study','ceiba'); ?></h5>
        <?php if ( $title ) : ?>
            <h3 class="ceiba-insight-card__title"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $title ); ?></a></h3>
        <?php endif; ?>
        <?php if ( $excerpt ) : ?>
            <p class="ceiba-insight-card__excerpt"><?php echo esc_html( $excerpt ); ?></p>
        <?php endif; ?>
    </div>
</article>
<?php
echo sprintf( '<div %s>%s</div>', $wrapper_attributes, ob_get_clean() );
