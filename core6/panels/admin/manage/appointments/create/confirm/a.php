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
reset( $apps );
for( $ii = 1; $ii <= count($apps); $ii++ )
{
	$a = $apps[$ii-1];

	$tm2->setLocation( $a['location_id'] );
	$tm2->setResource( $a['resource_id'] );
	$tm2->setService( $a['service_id'] );

	$skip = array(-$ii);
	$tm2->setSkip( $skip );
	$times = $tm2->getAllTime( $a['starts_at'], $a['starts_at'] );

	if( isset($times[$a['starts_at']]) )
	{
		$status[$ii] = 1;
	}
	else
	{
		$slot = $tm2->makeSlotFromAppointment( $a );
		$remain_seats = $tm2->checkSlot( $a['starts_at'], $slot, TRUE );
		$slot_errors = $tm2->getSlotErrors();
		if( $slot_errors )
			$status[$ii] = $slot_errors;
		else
			$status[$ii] = 0;
	}

	$tm2->setSkip( array() );
}

/* VIEW */
$view = array();
$view = array(
	'cid'		=> $cid,
	'apps'		=> $apps,
	'status'	=> $status,
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
				$cm->runCommand( $apps_objects[$ii], 'request' );
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