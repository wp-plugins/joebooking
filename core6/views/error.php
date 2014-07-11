<?php if( ntsView::isAnnounce() ) : ?>
	<?php $text = ntsView::getAnnounceText();	?>
	<?php foreach( $text as $t ) : ?>
	<?php if( $t[1] == 'error' ) : ?>
		<div class="alert alert-danger">
	<?php else : ?>
		<div class="alert alert-success">
	<?php endif; ?>
		<?php echo $t[0]; ?>
		</div>
	<?php endforeach; ?>
	<?php ntsView::clearAnnounce(); ?>
<?php endif; ?>
