<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$attrs = wp_parse_args( $attributes, [ 'postId' => 0 ] );

$wrapper_attributes = get_block_wrapper_attributes( [ 'class' => 'fade-up wp-block-ceiba-case-study alignfull' ] );

$pid = absint( $attrs['postId'] );
if ( ! $pid ) {
    echo sprintf( '<div %s><p class="ceiba-cs-empty">%s</p></div>',
        $wrapper_attributes,
        esc_html__( 'Select a project post in block settings.', 'ceiba' )
    );
    return;
}

$post = get_post( $pid );
if ( ! $post || $post->post_type !== 'project' ) {
    echo sprintf( '<div %s><p class="ceiba-cs-empty">%s</p></div>',
        $wrapper_attributes,
        esc_html__( 'Invalid project selection.', 'ceiba' )
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
// Move background to the wrapper container instead of the inner article
$bg_style = $img ? 'background-image:url(' . esc_url( $img ) . ');background-size:cover;background-position:center;' : '';
// Recompute wrapper attributes to include background style for the main output
$wrapper_attributes = get_block_wrapper_attributes( [
    'class' => 'wp-block-ceiba-case-study alignfull',
    'style' => $bg_style,
] );
?>


<article class="ceiba-ic__inner">
    <div class="ceiba-casestudy__body">
        <h5 class="ceiba-casestudy__label"><?php echo esc_html__('Project','ceiba'); ?></h5>
        <?php if ( $title ) : ?>
            <h2 class="ceiba-casestudy__title"><?php echo esc_html( $title ); ?></h3>
        <?php endif; ?>
        <?php if ( $excerpt ) : ?>
            <p class="ceiba-casestudy__excerpt"><?php echo esc_html( $excerpt ); ?></p>
        <?php endif; ?>
        <div class="wp-block-button is-style-ceiba-green">
            <a href="<?php echo esc_url( $url ); ?>" class="wp-block-button__link wp-element-button">View Project</a>
        </div>
    </div>
</article>
<?php
echo sprintf( '<div %s>%s</div>', $wrapper_attributes, ob_get_clean() );
