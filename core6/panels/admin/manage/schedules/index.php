<?php
$locs2 = ntsLib::getVar( 'admin::locs2' );
$ress2 = ntsLib::getVar( 'admin::ress2' );
$sers2 = ntsLib::getVar( 'admin::sers2' );



$locs_all = ntsLib::getVar( 'admin::locs_all' );
$ress_all = ntsLib::getVar( 'admin::ress_all' );
$sers_all = ntsLib::getVar( 'admin::sers_all' );

$app_info = ntsLib::getAppInfo();
global $NTS_TIME_WEEKDAYS;

$schEdit = ntsLib::getVar( 'admin/manage:schEdit' );
$schView = ntsLib::getVar( 'admin/manage:schView' );
$ress = ntsLib::getVar( 'admin::ress' );

$ress_all = array_intersect( $ress_all, $schView );
$ress_all = array_values( $ress_all );

$ress_archive = ntsLib::getVar( 'admin::ress_archive' );
if( $ress_archive )
{
	$ress_all = array_diff( $ress_all, $ress_archive );
	$ress_all = array_values( $ress_all );
}
$locs_archive = ntsLib::getVar( 'admin::locs_archive' );
if( $locs_archive )
{
	$locs_all = array_diff( $locs_all, $locs_archive );
	$locs_all = array_values( $locs_all );
}

$allSids = ntsObjectFactory::getAllIds('service');
$allLids = ntsObjectFactory::getAllIds('location');

$showDate = true;
$t = $NTS_VIEW['t'];

$displayCreateLink = TRUE;
if( ! ( $schEdit && array_intersect($ress, $schEdit) ) )
	$displayCreateLink = FALSE;

$shownWeekdays = array();
reset( $blocks );
$t->setStartDay();
$startDay = $t->getTimestamp();

$locs = ntsLib::getVar( 'admin::locs' );
$ress = ntsLib::getVar( 'admin::ress' );
$sers = ntsLib::getVar( 'admin::sers' );

$ntsConf =& ntsConf::getInstance();
$weekStartsOn = $ntsConf->get('weekStartsOn');
$dis = array();
for( $i = 0; $i < 7; $i++ ){
	$di = $weekStartsOn + $i;
	$di = $di % 7;
	$dis[] = $di;
	}
reset( $dis );
?>

<?php if( (count($locs_all) > 1) OR (count($ress_all) > 1) ) : ?>
<div>
	<div class="row">
		<div class="col-md-5 col-xs-12 pull-right text-right">
			<?php $duplicate_form->display(); ?>
		</div>

		<div class="col-md-7 col-xs-12">
			<ul class="list-inline list-separated">
				<?php require( dirname(__FILE__) . '/../calendar/views/_filter_selectors.php' ); ?>
			</ul>
		</div>
	</div>

	<div>
		<?php require( dirname(__FILE__) . '/../calendar/views/_filter_dropdowns.php' ); ?>
	</div>
</div>
<?php endif; ?>

<div class="row">
	<div class="col-md-3">
		<?php
		$tm2 = ntsLib::getVar( 'admin::tm2' );
		$check_appointments = FALSE;
		$showWeekNo = TRUE;
		require( dirname(__FILE__) . '/../prepare-calendar.php' );
		?>
		<?php
		require( NTS_APP_DIR . '/helpers/calendar2.php' );
		?>
	</div>

	<div class="col-md-9">
		<ul class="list-unstyled">
		<?php foreach( $dis as $di ) : ?>
			<?php
			if( ! isset($blocks[$di]) )
				continue;
			?>

			<li>
				<h4>
				<?php if( ! $cal ) : ?>
					<?php echo $NTS_TIME_WEEKDAYS[$di]; ?>
				<?php else : ?>
					<?php
					$t->setDateDb( $cal );
					$dayTitleView = $t->formatWeekday() . ', ' . $t->formatDate() . ' [' . M('Week') . ': ' . $t->getWeekNo() . ']';
					?>
					<?php echo $dayTitleView; ?>
				<?php endif; ?>
				</h4>
			</li>

			<?php foreach( $blocks[$di] as $b ) : ?>
				<?php
			/* timeblock */
				if( isset($b[0]) )
				{
					$resource_id = $b[0]['resource_id'];
					$editLink = ntsLink::makeLink( 
						'-current-/edit',
						'',
						array(
							'gid' => $b[0]['group_id']
							)
						);

					$t->setTimestamp( $startDay + $b[0]['starts_at'] );
					$time_view = $t->formatTime();
					if( $b[0]['selectable_every'] )
					{
						$t->setTimestamp( $startDay + $b[0]['ends_at'] );
						$time_end_view = $t->formatTime();
						if( $b[0]['ends_at'] > 24 * 60 * 60 )
						{
							$time_end_view = ' -> ' . $time_end_view;
						}
						$time_view .= ' - ' . $time_end_view;
					}

					if( $b[0]['now_warning'] )
						$class = 'warning';
					elseif( $b[0]['now_active'] )
						$class = 'success';
					else
						$class = 'archive';
					$time_view = '<i class="fa fa-fw fa-clock-o"></i>' . $time_view;

					$capacity = $b[0]['capacity'];
				}
			/* timeoff */
				else
				{
					$capacity = 1;
					$editLink = ntsLink::makeLink( 
						'-current-/timeoff/edit',
						'',
						array(
							'_id' => $b['id']
							)
						);

					if( ($b['valid_from'] < $cal) && ($b['valid_to'] > $cal) )
					{
						$time_view = M('All Day');
					}
					elseif( ($b['valid_from'] == $cal) && ($b['valid_to'] == $cal) )
					{
						$t->setTimestamp( $startDay + $b['starts_at'] );
						$time_view = $t->formatTime( $b['ends_at'] - $b['starts_at'] );
					}
					elseif( $b['valid_from'] == $cal )
					{
						$t->setTimestamp( $startDay + $b['starts_at'] );
						$time_view = $t->formatTime() . ' ...';
					}
					else
					{
						$t->setTimestamp( $startDay + $b['ends_at'] );
						$time_view = '... ' . $t->formatTime();
					}
					$resource_id = $b['resource_id'];
					$class = 'danger';
					$class = 'default panel-inverse';
					$time_view = '<i class="fa fa-fw fa-coffee"></i>' . $time_view;

					$toff = ntsObjectFactory::get('timeoff');
					$toff->setId( $b['id'] );
					$conflicts = $toff->get_conflicts();
					if( $conflicts )
					{
						$time_view .= '<span class="label label-danger" style="margin-left: 0.5em;" title="' . M('Conflicts') . '">' . '<i class="fa fa-exclamation-circle"></i>' . '</span>';
					}
				}
				$iCanEdit = in_array($resource_id, $schEdit );
				?>
				<li class="nts-ajax-parent">
					<div class="panel panel-<?php echo $class; ?>">
						<div class="panel-heading">
							<div class="row">
								<div class="col-md-5">
									<h4 class="panel-title">
									<?php if( $iCanEdit ) : ?>
										<a href="<?php echo $editLink; ?>" class="nts-ajax-loader nts-ajax-scroll">
									<?php endif; ?>
									<?php echo $time_view; ?>
									<?php if( $iCanEdit ) : ?>
										</a>
									<?php endif; ?>
									</h4>
								</div>

								<div class="col-md-5">
									<?php if( count($ress2) > 1 ) : ?>
										<?php
										$resource = ntsObjectFactory::get('resource');
										$resource->setId( $resource_id );
										?>
										<?php echo ntsView::objectTitle( $resource, TRUE ); ?>
									<?php endif; ?>
								</div>

								<div class="col-md-2">
									<?php if( $capacity > 1 ) : ?>
										<span title="<?php echo M('Capacity'); ?>: <?php echo $capacity; ?>">
											<i class="fa fa-users fa-fw"></i> <?php echo $capacity; ?>
										</span>
									<?php endif; ?>
								</div>
							</div>
						</div>
						<div class="panel-body nts-ajax-container"></div>
					</div>
				</li>
			<?php endforeach; ?>
		<?php endforeach; ?>

		<?php if( $displayCreateLink ) : ?>
			<?php
			$createParams = array();
			if( $cal ){
				$createParams['cal'] = $cal;
				}
			else {
				$createParams['applied_on'] = $di;
				}
			$addLink = ntsLink::makeLink( 
				'-current-/create',
				'',
				$createParams
				);
			$addLink_Week = ntsLink::makeLink( 
				'-current-/create-week',
				'',
				$createParams
				);
			$addLink_Timeoff = ntsLink::makeLink( 
				'-current-/timeoff/create',
				'',
				$createParams
				);
			?>
			<li class="nts-ajax-parent">
				<div class="panel panel-default">
					<div class="panel-heading">
						<ul class="list-inline list-separated">
							<li>
								<a class="nts-ajax-loader nts-ajax-scroll btn btn-default" href="<?php echo $addLink; ?>">
									<i class="fa fa-fw fa-plus"></i> <i class="fa fa-fw fa-clock-o"></i><?php echo M('Availability'); ?>
								</a>
							</li>
							<li>
								<a class="nts-ajax-loader nts-ajax-scroll btn btn-default" href="<?php echo $addLink_Week; ?>">
									<i class="fa fa-fw fa-plus"></i> <i class="fa fa-fw fa-clock-o"></i><?php echo M('Week Wizard'); ?>
								</a>
							</li>
							<li>
								<a class="nts-ajax-loader nts-ajax-scroll btn btn-inverse" href="<?php echo $addLink_Timeoff; ?>">
									<i class="fa fa-fw fa-plus"></i> <i class="fa fa-fw fa-coffee"></i><?php echo M('Timeoff'); ?>
								</a>
							</li>
						</ul>
					</div>
					<div class="panel-body nts-ajax-container"></div>
				</div>
			</li>
		<?php endif; ?>
		</ul>
	</div>
</div>