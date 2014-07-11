<?php
$own = ntsLib::hasVar( 'admin/manage/timeoff:own' ) ? ntsLib::getVar( 'admin/manage/timeoff:own' ) : FALSE;
?>
<?php if( $own ) : ?>
	<div class="page-header">
		<h2>
		<i class="fa fa-coffee"></i> <?php echo M('Timeoff'); ?>
		</h2>
	</div>
<?php endif; ?>

<?php
$addLink = ntsLink::makeLink( 
	'-current-/create',
	'',
	array(NTS_PARAM_VIEW_RICH => 'basic')
	);
?>
<?php if( $displayCreateLink ) : ?>
	<div class="nts-ajax-parent">
		<a class="nts-ajax-loader btn btn-success" title="<?php echo M('Timeoff'); ?>: <?php echo M('Add'); ?>" href="<?php echo $addLink; ?>">
			<i class="fa fa-plus"></i> <?php echo M('Add'); ?>
		</a>
	<div class="panel panel-default panel-body nts-ajax-container"></div>
	</div>
<?php endif; ?>