<?php
$title = ntsView::objectTitle($e);
?>
<?php if( $returnTo ) : ?>
	<a class="nts-target-parent2" href="<?php echo $targetLink; ?>" title="<?php echo $title; ?>">
<?php else : ?>
	<a class="" href="<?php echo $targetLink; ?>" title="<?php echo $title; ?>">
<?php endif; ?>
	<strong><?php echo $title; ?></strong>
</a>
