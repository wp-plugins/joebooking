<?php
/* create services */
$services = array();

$titles = array(
	array( 'Swedish Massage', 30 * 60, 25 ),
	array( 'Thai Massage', 30 * 60, 45 ),
	array( 'Traditional Massage', 60 * 60, 60 ),
	);

foreach( $titles as $ta ){
	$object = ntsObjectFactory::get( 'service' );
	$object->setByArray( array(
		'title'			=> $ta[0],
		'description'	=> 'Description of ' . $ta[0],
		'min_cancel'	=> 1 * 24 * 60 * 60,
		'allow_queue'	=> 0,
		'recur_total'	=> 1,
		'duration'	 	=> $ta[1],
		'lead_in'		=> 0,
		'lead_out'		=> 0,
//		'price'			=> $ta[2],
		)
		);
	$cm->runCommand( $object, 'create' );
	$serviceId = $object->getId();
	$services[] = $serviceId;
	}

/* create locations */
$locations = array();
$titles = array('My Massage Studio');
foreach( $titles as $t ){
	$object = ntsObjectFactory::get( 'location' );
	$object->setByArray( array(
		'title'			=> $t,
		'description'	=> 'Description of ' . $t,
		)
		);
	$cm->runCommand( $object, 'create' );
	$locationId = $object->getId();
	$locations[] = $locationId;
	}

$tm2 = new haTimeManager2();

/* create resources */
$titles = array( 'Massage Therapist' );
$resources = array();
foreach( $titles as $title ){
	$object = ntsObjectFactory::get('resource');
	$object->setByArray( 
		array(
			'title'		=> $title,
			)
		);

	$cm->runCommand( $object, 'create' );
	$resId = $object->getId();
	$resources[] = $resId;

	$resourceSchedules = $admin->getSchedulePermissions();
	$resourceApps = $admin->getAppointmentPermissions();
	$resourceSchedules[ $resId ] = array( 'view' => 1, 'edit' => 1 );
	$resourceApps[ $resId ] = array( 'view' => 1, 'edit' => 1, 'notified' => 1 );

	$admin->setSchedulePermissions( $resourceSchedules );
	$admin->setAppointmentPermissions( $resourceApps );

	$cm->runCommand( $admin, 'update' );

	/* schedules */
	$t = new ntsTime;
	$startSchedule = $t->formatDate_Db();
	list( $year, $month, $day ) = ntsTime::splitDate( $startSchedule );
	$t->setDateTime( $year + 1, $month, $day, 0, 0, 0 );
	$endSchedule = $t->formatDate_Db();

	$newBlock = array(
		'starts_at'			=> 9 * 60 * 60,
		'ends_at'			=> 18 * 60 * 60, 
		'selectable_every'	=> 15 * 60,
		'applied_on'		=> array( 1, 2, 3, 4, 5),
		'location_id'		=> 0,
		'resource_id'		=> $resId,
		'service_id'		=> 0,
		'valid_from'		=> $startSchedule,
		'valid_to'			=> $endSchedule,
		'capacity'			=> 1,
		);
	$tm2->addBlock( $newBlock );
	}

// resources terminology
$resnameSing = 'Massage Therapist';
$resnamePlu = 'Massage Therapists';
$conf->set( 'text-Bookable Resource', $resnameSing );
$conf->set( 'text-Bookable Resources', $resnamePlu );
$conf->set( 'htmlTitle', 'Massage Salon Scheduling' );
?>