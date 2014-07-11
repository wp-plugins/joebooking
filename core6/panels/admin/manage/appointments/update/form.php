<?php
$action = $_NTS['REQ']->getRequestedAction();
$object = ntsLib::getVar( 'admin/manage/appointments/update::OBJECT' );

echo $this->makePostParams('-current-', $action . '-confirm' );

$action_labels = array(
	'complete'	=> M('Complete'),
	'approve'	=> M('Approve'),
	'reject'	=> M('Reject'),
	'noshow'	=> M('No Show'),
	'delete'	=> M('Delete'),
	);
$action_label = isset($action_labels[$action]) ? $action_labels[$action] : '';
?>

<?php if( is_array($object) ) : ?>
	<?php if( $action_label ) : ?>
		<div class="page-header">
			<h2>
				<?php echo $action_label; ?>
			</h2>
		</div>
	<?php endif; ?>

	<ul class="list-unstyled list-separated">
	<?php foreach( $object as $obj ) : ?>
		<li>
			<ul class="list-inline">
				<li>
					<?php echo $obj->statusLabel(FALSE); ?>
				</li>
				<li>
					<?php echo ntsView::objectTitle($obj); ?>
				</li>
			</ul>
		</li>
	<?php endforeach; ?>
	</ul>
	<hr>
<?php endif; ?>

<INPUT class="btn btn-default" TYPE="submit" VALUE="<?php echo M('Confirm'); ?>">