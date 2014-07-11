<?php
$entries = ntsLib::getVar( 'admin/notes::entries' );
$iCanEdit = ntsLib::getVar( 'admin/notes::iCanEdit' );
$totalCols = 2;
?>

<?php if( $iCanEdit ) : ?>
<p>
<div class="nts-ajax-parent">
<?php
echo ntsLink::printLink(
	array(
		'panel'		=> '-current-/create',
		'title'		=> '<i class="fa fa-plus"></i> ' . M('Note'),
		'attr'		=> array(
			'class'	=> 'nts-ajax-loader btn btn-info btn-xs',
			),
		)
	);
?>
<div class="nts-ajax-container"></div>
</div>
</p>
<?php endif; ?>

<?php for( $ii = 0; $ii < count($entries); $ii++ ) : ?>
	<?php
	$e = $entries[$ii];
	$editLink = ntsLink::makeLink( '-current-/edit/edit', '', array('noteid' => $e['id']) );
	$deleteLink = ntsLink::makeLink( '-current-/edit/delete', '', array('noteid' => $e['id']) );

	list( $time, $adminId ) = explode( ':', $e['meta_data'] );

	$NTS_VIEW['t']->setTimestamp( $time );
	$timeView =  $NTS_VIEW['t']->formatFull();

	$admin = new ntsUser;
	$admin->setId( $adminId );
	$adminView = ntsView::objectTitle( $admin, TRUE );
	?>
	<div class="alert alert-archive2 nts-ajax-parent">

		<?php if( $iCanEdit ) : ?>
			<a class="nts-ajax-loader close text-danger" href="<?php echo $deleteLink; ?>" title="<?php echo M('Delete'); ?>">
				&times;
			</a>
		<?php endif; ?>

		<?php if( $iCanEdit ) : ?>
			<a class="nts-ajax-loader" href="<?php echo $editLink; ?>"><?php echo $e['meta_value']; ?></a>
		<?php else : ?>
			<?php echo $e['meta_value']; ?>
		<?php endif; ?>

		<div class="nts-ajax-container"></div>

		<ul class="list-inline text-muted">
			<li>
				<small>
					<?php echo $timeView; ?>
				</small>
			</li>
			<li>
				<small>
					<?php echo $adminView; ?>
				</small>
			</li>
		</ul>

	</div>
<?php endfor; ?>
