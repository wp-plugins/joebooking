<?php
$ntsConf =& ntsConf::getInstance();
$t = $NTS_VIEW['t'];

$btn_status = 'success';
foreach( $selected_status as $k => $v )
{
	if( ! is_array($v) )
		continue;
	$break_this = FALSE;
	foreach( $v as $k2 => $v2 )
	{
		if( is_array($v2) )
		{
			$btn_status = 'danger';
			$break_this = TRUE;
			break;
		}
		elseif( ! $v2 )
		{
			$btn_status = 'archive';
			$break_this = TRUE;
			break;
		}
	}
	if( $break_this )
		break;
}

$changed = FALSE;
if (
	$selected['location_id'] OR 
	$selected['resource_id'] OR 
	$selected['service_id'] OR 
	$selected['starts_at']
)
{
	$changed = TRUE;
}
$check_status = $changed ? $selected_status : $app_status;

$locs_all = ntsLib::getVar( 'admin::locs_all' );
$ress_all = ntsLib::getVar( 'admin::ress_all' );
$sers_all = ntsLib::getVar( 'admin::sers_all' );

$locs = ntsLib::getVar( 'admin::locs' );
$ress = ntsLib::getVar( 'admin::ress' );
$sers = ntsLib::getVar( 'admin::sers' );

$appEdit = ntsLib::getVar( 'admin/manage:appEdit' );
$object = ntsLib::getVar( 'admin/manage/appointments/edit::OBJECT' );
$objectId = $object->getId();
$a = $object->getByArray();

$to_select = array();
if( count($locs) > 1 )
	$to_select[] = 'location';
if( count($ress) > 1 )
	$to_select[] = 'resource';
if( count($sers) > 1 )
	$to_select[] = 'service';
$to_select[] = 'time';

$lid = $object->getProp('location_id');
$current_location = ntsObjectFactory::get('location');
$current_location->setId( $lid );

$rid = $object->getProp('resource_id');
$current_resource = ntsObjectFactory::get('resource');
$current_resource->setId( $rid );

$sid = $object->getProp('service_id');
$current_service = ntsObjectFactory::get('service');
$current_service->setId( $sid );

$starts_at = $object->getProp('starts_at'); 
$current_time = $starts_at;

$createdAt = $object->getProp('created_at'); 
$duration = $object->getProp('duration'); 

$NTS_VIEW['t']->setTimestamp( $starts_at );
$dateView = $NTS_VIEW['t']->formatWeekdayShort() . ', ' . $NTS_VIEW['t']->formatDate();

$timeView = $NTS_VIEW['t']->formatTime();
$NTS_VIEW['t']->modify( '+' . $duration . ' seconds' );
$timeView .= ' - ' . $NTS_VIEW['t']->formatTime();

$customerId = $object->getProp('customer_id');
$customer = new ntsUser;
$customer->setId( $customerId );

$dump = $object->dump( TRUE );
?>

<ul class="list-unstyled">
	<?php if( (count($locs_all) > 1) && $lid ) : ?>
		<?php
		$current_status = isset($check_status['location'][$lid]) ? $check_status['location'][$lid] : 0;
		if( is_array($current_status) )
			$current_selector_class = 'danger-o';
		elseif($current_status)
			$current_selector_class = 'success-o';
		else
			$current_selector_class = 'archive-o';
		?>
		<li>
			<?php if( count($locs) > 1 ) : ?>

				<?php if( $selected['location_id'] ) : ?>
					<div class="row">
						<div class="col-md-5">
							<?php
							$old_lid = $lid;
							$lid = $selected['location_id'];
							require( dirname(__FILE__) . '/../../create/views/_index_location.php' );
							?>
						</div>

						<div class="col-md-2 text-center">
							<h4><i class="fa fa-arrow-left"></i></h4>
						</div>

						<div class="col-md-5">
							<?php
							$current_status = isset($app_status['location'][$old_lid]) ? $app_status['location'][$old_lid] : 0;
							if( is_array($current_status) )
								$current_selector_class = 'danger-o';
							elseif($current_status)
								$current_selector_class = 'success-o';
							else
								$current_selector_class = 'archive-o';
							?>
							<div class="alert alert-<?php echo $current_selector_class; ?> squeeze-in">
								<?php echo $dump['location']; ?> 
							</div>
						</div>
					</div>
				<?php else : ?>
					<?php
					$lid = $selected['location_id'];
					require( dirname(__FILE__) . '/../../create/views/_index_location.php' );
					?>
				<?php endif; ?>

			<?php else : ?>

				<div class="alert alert-<?php echo $current_selector_class; ?> squeeze-in">
					<?php echo $dump['location']; ?> 
				</div>

			<?php endif; ?>
		</li>
	<?php endif; ?>

	<?php if( (count($ress_all) > 1) && $rid ) : ?>
		<?php
		$current_status = isset($check_status['resource'][$rid]) ? $check_status['resource'][$rid] : 0;
		if( is_array($current_status) )
			$current_selector_class = 'danger-o';
		elseif($current_status)
			$current_selector_class = 'success-o';
		else
			$current_selector_class = 'archive-o';
		?>
		<li>
			<?php if( count($ress) > 1 ) : ?>

				<?php if( $selected['resource_id'] ) : ?>
					<div class="row">
						<div class="col-md-5">
							<?php
							$old_rid = $rid;
							$rid = $selected['resource_id'];
							require( dirname(__FILE__) . '/../../create/views/_index_resource.php' );
							?>
						</div>

						<div class="col-md-2 text-center">
							<h4><i class="fa fa-arrow-left"></i></h4>
						</div>

						<div class="col-md-5">
							<?php
							$current_status = isset($app_status['resource'][$old_rid]) ? $app_status['resource'][$old_rid] : 0;
							if( is_array($current_status) )
								$current_selector_class = 'danger-o';
							elseif($current_status)
								$current_selector_class = 'success-o';
							else
								$current_selector_class = 'archive-o';
							?>
							<div class="alert alert-<?php echo $current_selector_class; ?> squeeze-in">
								<?php echo $dump['resource']; ?> 
							</div>
						</div>
					</div>

				<?php else : ?>

					<?php
					$rid = $selected['resource_id'];
					require( dirname(__FILE__) . '/../../create/views/_index_resource.php' );
					?>

				<?php endif; ?>

			<?php else : ?>

				<div class="alert alert-<?php echo $current_selector_class; ?> squeeze-in">
					<?php echo $dump['resource']; ?> 
				</div>

			<?php endif; ?>
		</li>
	<?php endif; ?>

	<?php
	$current_status = isset($check_status['service'][$sid]) ? $check_status['service'][$sid] : 0;
	if( is_array($current_status) )
		$current_selector_class = 'danger-o';
	elseif($current_status)
		$current_selector_class = 'success-o';
	else
		$current_selector_class = 'archive-o';
	?>
	<li>
		<?php if( count($sers) > 1 ) : ?>

			<?php if( $selected['service_id'] ) : ?>
				<div class="row">
					<div class="col-md-5">
						<?php
						$old_sid = $sid;
						$sid = $selected['service_id'];
						require( dirname(__FILE__) . '/../../create/views/_index_service.php' );
						?>
					</div>

					<div class="col-md-2 text-center">
						<h4><i class="fa fa-arrow-left"></i></h4>
					</div>

					<div class="col-md-5">
						<?php
						$current_status = isset($app_status['service'][$old_sid]) ? $app_status['service'][$old_sid] : 0;
						if( is_array($current_status) )
							$current_selector_class = 'danger-o';
						elseif($current_status)
							$current_selector_class = 'success-o';
						else
							$current_selector_class = 'archive-o';
						?>
						<div class="alert alert-<?php echo $current_selector_class; ?> squeeze-in">
							<ul class="list-unstyled list-separated">
								<li>
									<?php echo $dump['service']; ?> 
								</li>
								<li>
									<?php
									$obj = $current_service;
									require( dirname(__FILE__) . '/../../create/views/_service_details.php' );
									?>
								</li>
							</ul>
						</div>
					</div>
				</div>

			<?php else : ?>

				<?php
				$sid = $selected['service_id'];
				require( dirname(__FILE__) . '/../../create/views/_index_service.php' );
				?>
			<?php endif; ?>

		<?php else : ?>
			<div class="alert alert-<?php echo $current_selector_class; ?> squeeze-in">
				<?php echo $dump['service']; ?> 
			</div>
		<?php endif; ?>
	</li>

	<?php
	$current_status = isset($check_status['time'][$starts_at]) ? $check_status['time'][$starts_at] : 0;
	if( is_array($current_status) )
		$current_selector_class = 'danger-o';
	elseif($current_status)
		$current_selector_class = 'success-o';
	else
		$current_selector_class = 'archive-o';
	?>
	<li>
		<?php if( $selected['starts_at'] ) : ?>
			<div class="row">
				<div class="col-md-5">
					<?php
					$old_starts_at = $starts_at;
					$starts_at = $selected['starts_at'];
					require( dirname(__FILE__) . '/../../create/views/_index_time.php' );
					?>
				</div>

				<div class="col-md-2 text-center">
					<h4><i class="fa fa-arrow-left"></i></h4>
				</div>

				<div class="col-md-5">
					<?php
					$current_status = isset($app_status['time'][$old_starts_at]) ? $app_status['time'][$old_starts_at] : 0;
					if( is_array($current_status) )
						$current_selector_class = 'danger-o';
					elseif($current_status)
						$current_selector_class = 'success-o';
					else
						$current_selector_class = 'archive-o';
					?>
					<div class="alert alert-<?php echo $current_selector_class; ?> squeeze-in">
						<?php
						$t->setTimestamp( $old_starts_at );
						?>
						<i class="fa fa-fw fa-calendar"></i><?php echo $t->formatDateFull(); ?> <i class="fa fa-fw fa-clock-o"></i><?php echo $t->formatTime(); ?>
					</div>
				</div>
			</div>

		<?php else : ?>
			<?php
			if( $selected['cal'] )
			{
				$cal = $selected['cal'];
			}
			else
			{
				$t->setTimestamp( $starts_at );
				$cal = $t->formatDate_Db();
			}

			$errors = array();
			if( isset($selected_status['time'][$starts_at]) && is_array($selected_status['time'][$starts_at]) )
			{
				$errors = $selected_status['time'][$starts_at];
			}
			$starts_at = $selected['starts_at'];
			?>
			<div class="nts-ajax-container" style="display: block;">
				<?php require( dirname(__FILE__) . '/../../create/views/_index_time.php' ); ?>
			</div>

		<?php endif; ?>
	</li>

<?php if( $changed ) : ?>
	<hr style="margin: 1em 0 0 0;">
	<li>
		<?php echo $form->display(); ?>
	</li>
<?php endif; ?>
</ul>
