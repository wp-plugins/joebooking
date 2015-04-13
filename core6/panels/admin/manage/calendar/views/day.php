<?php
$ntsconf =& ntsConf::getInstance();
$weekStartsOn = $ntsconf->get('weekStartsOn');

$current_filter = ntsLib::getVar( 'admin/manage:current_filter' );
//_print_r( $labels );
$t = $NTS_VIEW['t'];
$slot_file = dirname(__FILE__) . '/_day_slot.php';
$time_view_needed = 1;

$TYPE_TO_CSS = array(
	HA_SLOT_TYPE_WO			=> 'ntsWorking',
	HA_SLOT_TYPE_WO			=> 'alert-success-o',
	HA_SLOT_TYPE_APP_BODY	=> '',
	HA_SLOT_TYPE_APP_LEAD	=> 'ntsLead',
	HA_SLOT_TYPE_NA			=> 'alert-archive',
//	HA_SLOT_TYPE_TOFF		=> 'ntsTimeoff',
	HA_SLOT_TYPE_TOFF		=> 'alert-inverse',
	);
$calendarField = 'customer';

global $_NTS;
$viewstats = $_NTS['REQ']->getParam('viewstats');
if( $viewstats )
{
	require( dirname(__FILE__) . '/_stats.php' );
	return;
}

$appEdit = ntsLib::getVar( 'admin/manage:appEdit' );
$ress = ntsLib::getVar( 'admin::ress' );
$can_add = ( $appEdit && array_intersect($ress, $appEdit) ) ? TRUE : FALSE;

/* build list of entities that we'll display our matrices */
$locs = ntsLib::getVar( 'admin::locs' );
$ress = ntsLib::getVar( 'admin::ress' );
$sers = ntsLib::getVar( 'admin::sers' );

/* check out archived resources */
$ress_archive = ntsLib::getVar( 'admin::ress_archive' );
if( $ress_archive )
{
	$ress = array_diff( $ress, $ress_archive );
	$ress = array_values( $ress );
}

/* check out archived locations */
$locs_archive = ntsLib::getVar( 'admin::locs_archive' );
if( $locs_archive )
{
	$locs = array_diff( $locs, $locs_archive );
	$locs = array_values( $locs );
}

if( $range == 'dayloc'){
	$list_by = 'location';
}
else {
	if( (count($ress) <= 1) && (count($locs) <= 1) ){
		$list_by = 'resource';
	}
	elseif( count($ress) > 1 ){
		$list_by = 'resource';
	}
	else{
		$list_by = 'location';
	}
}

$list = array();
switch( $list_by )
{
	case 'resource':
		$list_ress = isset($current_filter['r']) ? array($current_filter['r']) : $ress;
		foreach( $list_ress as $obj_id ){
			$obj = ntsObjectFactory::get('resource');
			$obj->setId( $obj_id );
			$list[] = array(
				$obj,
				array(
					$locs,
					array($obj_id),
					$sers
					)
				);
		}
		break;

	case 'location':
		$list_locs = isset($current_filter['l']) ? array($current_filter['l']) : $locs;
		foreach( $list_locs as $obj_id ){
			$obj = ntsObjectFactory::get('location');
			$obj->setId( $obj_id );
			$list[] = array(
				$obj,
				array(
					array($obj_id),
					$ress,
					$sers
					)
				);
		}
		break;
}

$t->setDateDb( $start_date );
$month_matrix = $t->getMonthMatrix( $end_date );

$cals = array_keys( $dates );
require( dirname(__FILE__) . '/_build_day_slots.php' );
?>

<?php require( dirname(__FILE__) . '/_control.php' ); ?>

<?php if( ($list_by != 'location') && (count($locs) > 1) ) : ?>
	<p>
		<a href="<?php echo ntsLink::makeLink('-current-', '', array('range' => 'dayloc')); ?>" class="btn btn-sm btn-default"><i class="fa fa-home fa-fw"></i><?php echo M('Locations'); ?></a>
	</p>
<?php endif; ?>
<?php if( ($list_by != 'resource') && (count($ress) > 1) ) : ?>
	<p>
		<a href="<?php echo ntsLink::makeLink('-current-', '', array('range' => 'day')); ?>" class="btn btn-sm btn-default"><i class="fa fa-hand-o-up fa-fw"></i><?php echo M('Bookable Resources'); ?></a>
	</p>
<?php endif; ?>

<?php
$dii = -1;
?>
<?php foreach( $dates as $this_date => $darray ) : ?>
	<?php
	$dii++;
	$slots = $slotsArray[$this_date];
	$t->setDateDb( $this_date );
	$this_weekday = $t->getWeekday();
	?>
	<?php if( ($weekStartsOn == $this_weekday) && (count($dates) > 1) ) : ?>
		<h2><?php echo M('Week'); ?> <?php echo $t->getWeekNo(); ?></h2>
		<hr>
	<?php endif; ?>
	<h3><?php echo $t->formatDateFull(); ?></h3>

	<ul class="list-unstyled">
	<?php for( $ii = 0; $ii < count($list); $ii++ ) : ?>
		<?php
		$li = $list[$ii];
		$here_can_add = ( $appEdit && array_intersect($li[1][1], $appEdit) ) ? TRUE : FALSE;
		?>
		<!-- DAY LINE -->
		<li>
			<?php if( count($list) > 1 ) : ?>
				<h4>
					<?php echo ntsView::objectTitle( $li[0], TRUE ); ?>
					<?php if( $li[0]->getClassName() == 'location' ) : ?>
						<?php $capacity = $li[0]->getProp('capacity'); ?>
						<?php if( $capacity ) : ?>
							[<?php echo $capacity; ?> <?php echo ($capacity > 1) ? M('Seats') : M('Seat'); ?>]
						<?php endif; ?>
					<?php endif; ?>
				</h4>
			<?php endif; ?>

			<?php for( $r = 0; $r < count($slots[$ii]); $r++ ) : ?>
				<?php $slotContainer = $slots[$ii][$r]; ?>
				<ul class="list-inline" style="margin-bottom: 2px;">
					<?php
					foreach( $slotContainer as $slot )
					{
						$time_view_needed = 1;
						$availabilityLink = ntsLink::makeLink( 
							'admin/schedules', '', 
							array(
	//							'nts-filter'	=> join('-', $thisFilter),
								'cal'			=> $this_date,
								)
							);

							$t->setTimestamp( $slot[0] );
							$timeViewStart = $t->formatTime();

							if( is_array($slot[1]) ){
								$t->setTimestamp( $slot[1][0] );
							}
							else {
								$t->setTimestamp( $slot[1] );
							}
							$timeViewEnd = $t->formatTime();

							$targetLink = '#';
							$slotClass = array( $TYPE_TO_CSS[$slot[2]] );
							$slotId = '';
							$slotWidth = $slot[4];
							$slotInfo = '&nbsp;';
							$slotPullRight = '';

							$visibleMe = true;
							$startLabel = '';
							$menu = array();

							$lrs = $li[1];
							$create_params = array();
							$create_params['cal'] = $this_date;
							if( count($lrs[0]) == 1 )
								$create_params['location_id'] = $lrs[0][0];
							if( count($lrs[1]) == 1 )
								$create_params['resource_id'] = $lrs[1][0];
							if( count($lrs[2]) == 1 )
								$create_params['service_id'] = $lrs[2][0];
							$create_params['nts-filter'] = '-reset-';

							switch( $slot[2] )
							{
								case HA_SLOT_TYPE_WO:
									if( $here_can_add )
									{
										$targetLink = ntsLink::makeLink( 
											'-current-/../appointments/create',
											'', 
											$create_params
											);
									}
									$startLabel = M('Available');
									break;

								case HA_SLOT_TYPE_APP_BODY:
								case HA_SLOT_TYPE_APP_LEAD:
//									$time_view_needed = 0;
									$conflicts = array();
									$this_skip = array();
									if( isset($slot[3]) )
									{
										$cssClasses = array();
										$app = ntsObjectFactory::get( 'appointment' );
										if( is_array($slot[3]) )
										{
											$app_sid = 0;
											$already_cid = array();
											foreach( $slot[3] as $slot_app ){
												$app->setId( $slot_app['id'] );
												$app_sid = $app->getProp('service_id');
												$already_cid[] = $app->getProp('customer_id');
												$message = $app->statusText();
												$cssClass = $app->statusClass();
												$cssClass = 'alert-' . $cssClass;
												$cssClasses[ $cssClass ] = 1;
												}
											$slotId = 'nts-app-' . $slot[3][0];

											$idValue = join( '-', array($app->getProp('location_id'), $app->getProp('resource_id'), $app->getProp('service_id'), $app->getProp('starts_at')) );
											$targetLink = ntsLink::makeLink( 
												'-current-/../appointments/edit_class/overview', '', 
												array(
													'_id' => $idValue,
													)
												);

											/* ADD CUSTOMER */
											$class_create_params = $create_params;
											$class_create_params['starts_at'] = $slot[0];
											$class_create_params['service_id'] = $app_sid;
											$already_cid = array_unique($already_cid);
											$class_create_params['skip'] = join('-', $already_cid);
											$menu[] = array(
												'href'	=> ntsLink::makeLink(
													'admin/manage/appointments/create', 
													'',
													$class_create_params
													),
												'title'	=> '<i class="fa fa-plus"></i> ' . M('Customer'),
												);
										}
										else
										{
											$is_2nd_part = FALSE;
											if( strpos($slot[3], '_') === FALSE ){
												$app->setId( $slot[3] );
											}
											else { // is second part
												$is_2nd_part = TRUE;
												$app_id = substr($slot[3], 0, -1);
												$app->setId( $app_id );
											}

											$conflicts = $app->get_conflicts();

											$message = $app->statusText();
											$cssClass = $app->statusClass();
											$cssClass = 'alert-' . $cssClass;

											$startLabel = $message;
											$cssClasses[ $cssClass ] = 1;
											$slotId = 'nts-app-' . $slot[3];

											if( $conflicts ){
												$cssClasses[ 'alert-danger-o' ] = 1;
											}

											if( $slot[2] == HA_SLOT_TYPE_APP_BODY )
											{
												$check_app_start = $app->getProp('starts_at');
												$check_app_end = $app->getProp('starts_at') + $app->getProp('duration');

												if( $is_2nd_part ){
													$check_app_start = $app->getProp('starts_at') + $app->getProp('duration') + $app->getProp('duration_break');
													$check_app_end = $check_app_start + $app->getProp('duration2');
												}

											/* earlier than today */
												if( $check_app_start < $slot[0] ){
													$timeViewStart = '<-';
												}
												$slot_ends = is_array($slot[1]) ? $slot[1][0] : $slot[1];
												if( $check_app_end > $slot_ends ){
													$timeViewEnd = '- >';
												}

												$lead_out = 0;
												if( (! $app->getProp('duration2')) OR $is_2nd_part ){
													$lead_out = $app->getProp('lead_out');
												}

												if( $lead_out ){
													$cssClasses[ 'alert-left-part' ] = 1;
												}

												$targetLink = ntsLink::makeLink( 
													'-current-/../appointments/edit/overview', '', 
													array(
														'_id' => $slot[3],
														)
													);

												if( $conflicts ){
													foreach( $conflicts as $c ){
														$menu[] = '<i class="fa fa-exclamation-circle text-danger"></i> ' . $c;
													}
													$menu[] = '-divider-';
												}

												if( $lead_out ){
													$duration = $app->getProp('duration');
													$t->setTimestamp( $app->getProp('starts_at') );
													$t->modify( '+ ' . ($duration + $lead_out) . ' seconds' );
													$menu[] = '<i class="fa fa-angle-right"></i>' . $t->formatTime() . ' [' . M('Clean Up') . ']';
												}

											/* MORE INFO */
												if( isset($labels['dropdown']) && $labels['dropdown'] ){
													$app_seats = $app->getProp('seats');

													if( $app_seats <= 1 )
														$this_skip[] = 'seats';
													if( $calendarField )
														$this_skip[] = $calendarField;
													if( isset($create_params['location_id']) )
														$this_skip[] = 'location';
													if( isset($create_params['resource_id']) )
														$this_skip[] = 'resource';
													if( isset($create_params['service_id']) )
														$this_skip[] = 'service';

													$title = $app->dump( TRUE, array('location','resource','service','customer') );

													$customer = new ntsUser;
													$customer->setId( $app->getProp('customer_id') );
													$title['customer_link'] = array(
														'title'	=> ntsView::objectTitle( $customer, TRUE ),
														'href'	=> ntsLink::makeLink(
															'admin/customers/edit/edit',
															'',
															array(
																'_id'	=> $customer->getId()
																)
															),
														'target'	=> '_blank',
														);

													$this_labels_dropdown = $labels['dropdown'];
													$app_duration2 = $app->getProp('duration2');

													if( $is_2nd_part ){
														$service = ntsObjectFactory::get('service');
														$service->setId( $app->getProp('service_id') );
														$title['service'] = ntsView::objectTitle( $service, TRUE ) . ' (' . M('Part') . ' #2)';
														$this_skip = HC_Lib::remove_from_array( $this_skip, 'service' );
													}

													if( 0 && $app_duration2 ){
														$t->setTimestamp( $app->getProp('starts_at') );
														$title['time'] = $t->formatTime( $duration, FALSE, TRUE );

														$t->setTimestamp( $app->getProp('starts_at') );
														$t->modify( '+' . $duration . ' seconds' );
														$t->modify( '+' . $app->getProp('duration_break') . ' seconds' );
														$title['time2'] = $t->formatTime( $app_duration2, FALSE, TRUE );

														$this_labels_dropdown = HC_Lib::insert_after( 'time2', $this_labels_dropdown, 'time' );
														// $this_labels_dropdown[] = 'time2';
													}
													else {
														$this_skip[] = 'time';
														$this_skip[] = 'time2';
													}

													foreach( $this_labels_dropdown as $label ){
														if( in_array($label, $this_skip) )
															continue;
														if( isset($title[$label]) ){
															$menu[] = $title[$label];
														}
													}

												}
											}
											if( $slot[2] == HA_SLOT_TYPE_APP_BODY ){
												require( dirname(__FILE__) . '/_app_menu_actions.php' );
											}
										}
									}

									if( $slot[2] == HA_SLOT_TYPE_APP_LEAD ){
										$cssClasses[ 'alert-right-part' ] = 1;
										$cssClasses[ 'text-muted' ] = 1;
									}

									$slotClass = array_keys( $cssClasses );

									if( $slot[2] == HA_SLOT_TYPE_APP_LEAD ){
										$slotInfo = M('Clean Up');
									}
									else {
										switch ( $calendarField ){
											case 'customer':
												if( is_array($slot[3]) ){
													$slotInfo = '<i class="fa fa-user"></i> ' . count($slot[3]);
												}
												else {
													$customer = new ntsUser;
													$customer->setId( $app->getProp('customer_id') );
													$slotInfo = ntsView::objectTitle( $customer, TRUE );
													$slotInfo = ntsView::objectTitle( $customer, FALSE );
												}
												break;
											case 'service':
												$service = ntsObjectFactory::get('service');
												$service->setId( $app->getProp('service_id') );
												$slotInfo = ntsView::objectTitle( $service, TRUE );
												break;
										}
									}

									if( $slot[2] == HA_SLOT_TYPE_APP_BODY ){
										if( ! is_array($slot[3]) ){
											$cost = $app->getCost();
											if( $cost ){
												$slotPullRight = $app->paymentStatus(TRUE);
											}
										}

										$app_seats = $app->getProp('seats');
										if( $app_seats > 1 ){
											// $slotInfo .= '<br>' . $app->getProp('seats') . ' ' . M('Seats');
											$slotInfo .= '<br>' .
											HC_Html_Factory::element('span')
												->add_attr( 'title', M('Seats') . ': ' . $app_seats )
												->add_child( HC_Html::icon('users') . ' ' . $app_seats )
												->render()
												;
										}
									}

									break;

								case HA_SLOT_TYPE_TOFF:
									$targetLink = ntsLink::makeLink( 
										'-current-/../schedules/timeoff/edit', '', 
										array(
											'_id' => $slot[3],
											)
										);
									$startLabel = M('Timeoff');
									$toff = ntsObjectFactory::get('timeoff');
									$toff->setId( $slot[3] );

									$slotInfo = '<i class="fa fa-coffee"></i> ' . $toff->getProp('description');
									$conflicts = $toff->get_conflicts();

									if( $conflicts ){
										$slotClass[] = 'alert-danger-o';
										$menu[] = '<i class="fa fa-exclamation-circle text-danger"></i> ' . M('Appointments');
										$menu[] = '-divider-';
									}
									$menu[] = array(
										'href'	=> $targetLink,
										'title'	=> '<i class="fa fa-edit"></i> ' . M('Edit'),
										);

									$menu[] = array(
										'href'	=> ntsLink::makeLink(
											'admin/manage/schedules/timeoff/edit/delete',
											'confirm', 
											array(
												'_id' => $toff->getId(),
												NTS_PARAM_RETURN	=> 'calendar',
												)
											),
										'title'	=> '<i class="fa fa-times text-danger"></i> ' . M('Delete'),
										'class'	=> 'hc-confirm',
										);

									break;

								case HA_SLOT_TYPE_NA:
									$visibleMe = ($r > 0) ? false : true;
									$startLabel = M('Not Available');
	//								$slotClass[] = 'alert-right-part';
									break;

								default:
									$targetLink = $availabilityLink;
							}
						$slotClass = join( ' ', $slotClass );

						if( $visibleMe ){
							$slotInfo = trim( $slotInfo );
							$linkLabel = '';
							if( $startLabel )
								$linkLabel .= $startLabel . ' ';
							$linkLabel .= $timeViewStart . '-' . $timeViewEnd;
							if( $calendarField )
								$linkLabel .= ' ' . $slotInfo;
							$linkLabel = strip_tags($linkLabel);
							if( ! strlen($slotInfo) )
								$slotInfo = '&nbsp;';

							$moreAttr = '';
							$aClass = array();
							if( $menu ){
								$aClass[] = 'dropdown-toggle';
								$targetLink = '#';
								$moreAttr = ' data-toggle="dropdown"';
							}
							$aClass = join(' ', $aClass);
							ob_start();
							require( $slot_file );
							$slot_view = ob_get_contents();
							ob_end_clean();
							$slot_view = trim( $slot_view );
						}
						else {
							$slot_view = '&nbsp;';
						}

	//				$slot_view .= '<br>' . $slotWidth . '%';
					$slot_view = '<li style="width: ' . $slotWidth . '%;">' . $slot_view . '</li>';
					echo $slot_view;
					}
	?>
				</ul>
			<?php endfor; ?>
		</li>
		<?php endfor; ?>
	</ul>
<?php endforeach; ?>
