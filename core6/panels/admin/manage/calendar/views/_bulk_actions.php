<?php
$appEdit = ntsLib::getVar( 'admin/manage:appEdit' );

$ress = ntsLib::getVar( 'admin::ress' );
$can_add = ( $appEdit && array_intersect($ress, $appEdit) ) ? TRUE : FALSE;

$can_bulk = FALSE;
if( $display == 'calendar' )
{
	$can_bulk = FALSE;
}
else
{
	reset( $apps );
	foreach( $apps as $date => $all_apps )
	{
		reset( $all_apps );
		foreach( $all_apps as $app )
		{
			$rid = $app->getProp( 'resource_id' );
			if( in_array($rid, $appEdit) )
			{
				$can_bulk = TRUE;
				break;
			}
		}
		if( $can_bulk )
			break;
	}
}

$form = new ntsForm2;

$bulk_actions = array();
$bulk_actions[] = array( 'complete',	ntsAppointment::_statusLabel(0, HA_STATUS_COMPLETED, '', 'i') . ' ' . M('Complete') );
$bulk_actions[] = array( 'approve',		ntsAppointment::_statusLabel(HA_STATUS_APPROVED, 0, '', 'i') . ' ' . M('Approve') );
$bulk_actions[] = array( 'reject',		ntsAppointment::_statusLabel(0, HA_STATUS_CANCELLED, '', 'i') . ' ' . M('Reject') );
$bulk_actions[] = array( 'noshow',		ntsAppointment::_statusLabel(0, HA_STATUS_NOSHOW, '', 'i') . ' ' . M('No Show') );
$bulk_actions[] = '-divider-';
$bulk_actions[] = array( 'delete',		'<i class="fa fa-fw fa-times text-danger"></i> ' . M('Delete') );
?>
<?php echo $form->start(TRUE); ?>

<ul class="list-inline">
	<?php if( isset($customer_id) ) : ?>
		<?php if( $can_add ) : ?>
			<?php if( $can_add ) : ?>
				<li>
					<?php require( dirname(__FILE__) . '/_add_link.php' ); ?>
				</li>
				<li class="divider hidden-xs">&nbsp;</li>
			<?php endif; ?>
		<?php endif; ?>
	<?php endif; ?>

	<li class="hc-page-status" data-src="<?php echo ntsLink::makeLink('-current-', '', array('viewstats' => 1)); ?>">
		<?php require( dirname(__FILE__) . '/_stats.php' ); ?>
	</li>

	<?php if( $can_bulk ) : ?>
		<li class="divider">&nbsp;</li>
		<li>
			<a href="#" class="btn btn-default hc-all-checker" data-collect="nts-app_id" title="<?php echo M('Select All'); ?>">
				<i class="fa fa-check fa-fw"></i> <span class="hidden-xs"><?php echo M('Select All'); ?></span>
			</a>
		</li>

		<li class="dropdown">
			<?php
			echo $form->make_post_params('admin/manage/appointments/update' );
			?>
			<a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" title="<?php echo M('With Selected'); ?>">
				<i class="fa fa-cog fa-fw"></i> <span class="hidden-xs"><?php echo M('With Selected'); ?></span> <b class="caret"></b>
			</a>
			<ul class="dropdown-menu">
				<?php foreach( $bulk_actions as $ba ) : ?>
					<?php if( (! is_array($ba)) && ($ba == '-divider-') ) : ?>
						<li class="divider">
						</li>
					<?php else : ?>
						<li>
							<a class="hc-form-submit" href="#<?php echo $ba[0]; ?>" data-collect="nts-app_id">
								<?php echo $ba[1]; ?>
							</a>
						</li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		</li>
	<?php endif; ?>
</ul>
<?php echo $form->end(); ?>