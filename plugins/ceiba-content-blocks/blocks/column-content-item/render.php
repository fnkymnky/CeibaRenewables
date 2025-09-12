<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$wrapper = get_block_wrapper_attributes( [ 'class' => 'ccb-col' ] );

ob_start(); ?>
<div class="ccb-col__inner">
  <?php echo $content; ?>
</div>
<?php
echo sprintf('<article %s>%s</article>', $wrapper, ob_get_clean() );

