<?php
require( dirname(__FILE__) . '/_a_init.php' );

$session = new ntsSession;
$coupon = $session->userdata('coupon');

/* INIT LRS */
$locs = ntsLib::getVar( 'admin::locs' );
$ress = ntsLib::getVar( 'admin::ress' );
$sers = ntsLib::getVar( 'admin::sers' );

/* CHECK ERRORS */
$tm2 = ntsLib::getVar('admin::tm2');
$tm2->customerId = $cid;

$status = array();
$total_seats = array();
$available_seats = array();

reset( $apps );
for( $ii = 1; $ii <= count($apps); $ii++ )
{
	$a = $apps[$ii-1];

	$tm2->setLocation( $a['location_id'] );
	$tm2->setResource( $a['resource_id'] );
	$tm2->setService( $a['service_id'] );

	// $skip = array(-$ii);
	/* skip this and next apps */
	$skip = array();
	for( $jj = $ii; $jj <= count($apps); $jj++){
		$skip[] = -$jj;
	}

	$tm2->setSkip( $skip );
	$times = $tm2->getAllTime( $a['starts_at'], $a['starts_at'] );

/* check total seats */
	$this_total_seats = 0;
	$tm2->dryRun = TRUE;
	$defined_times = $tm2->getAllTime( $a['starts_at'], $a['starts_at'] );
	if( isset($defined_times[$a['starts_at']]) ){
		$this_total_seats = $tm2->getAvailableSeats( $defined_times[$a['starts_at']], ($a['starts_at'] + $a['duration'] + $a['lead_out']) );
		}
	$total_seats[$ii] = $this_total_seats;
	$tm2->dryRun = FALSE;

	if( isset($times[$a['starts_at']]) ){
		$status[$ii] = 1;
		$this_available_seats = $tm2->getAvailableSeats( $times[$a['starts_at']], ($a['starts_at'] + $a['duration'] + $a['lead_out']) );
	}
	else {
		$slot = $tm2->makeSlotFromAppointment( $a );
		$remain_seats = $tm2->checkSlot( $a['starts_at'], $slot, TRUE );
		$slot_errors = $tm2->getSlotErrors();
		if( $slot_errors )
			$status[$ii] = $slot_errors;
		else
			$status[$ii] = 0;
	}

	$this_available_seats = $this_total_seats;
	if( $this_total_seats > 1 ){
		$tm2->appsOnly = TRUE;
		$available_times = $tm2->getAllTime( $a['starts_at'], $a['starts_at'] );
		if( isset($available_times[$a['starts_at']]) ){
			$this_available_seats = $tm2->getAvailableSeats( $available_times[$a['starts_at']], ($a['starts_at'] + $a['duration'] + $a['lead_out']) );
			}
		$tm2->appsOnly = FALSE;
	}
	$available_seats[$ii] = $this_available_seats;

	$tm2->setSkip( array() );
}

/* VIEW */
$view = array();
$view = array(
	'cid'		=> $cid,
	'apps'		=> $apps,
	'status'	=> $status,
	'total_seats'		=> $total_seats,
	'available_seats'	=> $available_seats,
	'locs'	=> $locs,
	'ress'	=> $ress,
	'sers'	=> $sers,
	);

/* CONFIRM FORM */
$ff =& ntsFormFactory::getInstance();
$form_file = dirname( __FILE__ ) . '/views/form';
$form =& $ff->makeForm( $form_file, $view );

switch( $action )
{
	case 'create':
		if( $form->validate() )
		{
			$form_values = $form->getValues();
			$set_status = isset($form_values['set_status']) ? $form_values['set_status'] : 'approved';
			$notify_customer = (isset($form_values['notify_customer']) && $form_values['notify_customer']) ? 1 : 0;

			$cm =& ntsCommandManager::getInstance();

			$apps_objects = array();
			reset( $apps );
			for( $ii = 0; $ii < count($apps); $ii++ )
			{
				$a = $apps[$ii];
				foreach( $form_values as $fk => $fv )
				{
					$a[$fk] = $fv;
				}
				$new_a = ntsObjectFactory::get( 'appointment' );
				$new_a->setByArray( $a );
				
				$params = array(
					'coupon'	=> $coupon
					);
				$cm->runCommand( $new_a, 'init', $params );
				if( $cm->isOk() )
				{
					$apps_objects[] = $new_a;
				}
				else
				{
					// failed
					$errorText = $cm->printActionErrors();
					ntsView::addAnnounce( $errorText, 'error' );
				}
			}

			$ok = 0;
			for( $ii = 0; $ii < count($apps_objects); $ii++ )
			{
				$params = array();
				if( ! $notify_customer )
				{
					$params['_silent_customer'] = 1;
				}

				$command_name = ( $set_status == 'pending' ) ? 'require_approval' : 'request';
				$cm->runCommand( 
					$apps_objects[$ii],
					$command_name,
					$params
					);

				if( $cm->isOk() )
				{
					$ok++;
				}
				else
				{
					$errorText = $cm->printActionErrors();
					ntsView::addAnnounce( $errorText, 'error' );
				}
			}

			if( $ok )
			{
				if( $ok == 1 )
					$msg = array( M('Appointment'), ntsView::objectTitle($apps_objects[0]) );
				elseif( $ok > 1 )
					$msg = array( M('Appointments') . ' [' . $ok . ']' );

				$msg[] = M('Create');
				$msg[] = M('OK');
				$msg = join( ': ' , $msg );
				ntsView::addAnnounce( $msg, 'ok' );
			}

			$session->set_userdata( 'apps', array() );
			$session->set_userdata( 'coupon', '' );

			$forwardTo = $session->userdata( 'calendar_view' );
			if( $forwardTo )
			{
				ntsView::redirect( $forwardTo );
			}
			else
			{
				$startsAt = $apps[0]->getProp('starts_at'); 
				$t->setTimestamp( $startsAt );
				$cal = $t->formatDate_Db();
				$forwardTo = ntsLink::makeLink( 
					'admin/manage/calendar', 
					'',
					array(
						'cal'	=> $cal,
						)
					);
			}
			ntsView::redirect( $forwardTo );
			exit;
		}
		break;
}

$view['form'] = $form;
$this->render(
	dirname(__FILE__) . '/views/index.php',
	$view
	);
?>