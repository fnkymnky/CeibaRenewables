<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$attrs = wp_parse_args( $attributes, [ 'postId' => 0 ] );

$wrapper_attributes = get_block_wrapper_attributes( [ 'class' => 'ceiba-case-study' ] );

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

ob_start(); ?>
<article class="ceiba-cs-card ceiba-cs-card--single">
    <?php if ( has_post_thumbnail( $pid ) ) : ?>
        <a class="ceiba-cs-card__media" href="<?php echo esc_url( $url ); ?>">
            <?php echo get_the_post_thumbnail( $pid, 'large', [ 'loading' => 'lazy', 'decoding' => 'async' ] ); ?>
        </a>
    <?php endif; ?>

    <div class="ceiba-cs-card__body">
        <?php if ( $title ) : ?>
            <h3 class="ceiba-cs-card__title">
                <a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $title ); ?></a>
            </h3>
        <?php endif; ?>

        <?php if ( $excerpt ) : ?>
            <p class="ceiba-cs-card__excerpt"><?php echo esc_html( $excerpt ); ?></p>
        <?php endif; ?>
        <?php /* No "read more" output here; theme/front-end controls any CTA text. */ ?>
    </div>
</article>
<?php
echo sprintf( '<div %s>%s</div>', $wrapper_attributes, ob_get_clean() );
