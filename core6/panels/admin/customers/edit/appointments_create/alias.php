<?php
$object = ntsLib::getVar( 'admin/customers/edit::OBJECT' );
$customerId = $object->getId();

ntsView::setBack( ntsLink::makeLink('admin/customers/edit/appointments', '', array('_id' => $customerId) ) );

$alias = 'admin/manage/appointments/create';
ntsLib::setVar( 'admin/manage/appointments/create::fixCustomer', $customerId );

$showFull = 1;
ntsLib::setVar( 'admin/manage/appointments/create::showFull', $showFull );

$reschedule = null;
ntsLib::setVar( 'admin/manage/appointments/create::reschedule', $reschedule );

$capture = array( 
	'location_id',
	'resource_id',
	'service_id',
	'starts_at',
	'reschedule',
	'customer_id',
	'no_customer',
	'hidden',
	'all',
	'from',
	'to',
	'add_time',
	);
reset( $capture );
foreach( $capture as $c ){
	$value = $_NTS['REQ']->getParam( $c );
	if( $value )
		$saveOn[$c] = $value;
	}
ntsView::setPersistentParams( $saveOn, 'admin/customers/edit/appointments_create' );

$noCustomer = $_NTS['REQ']->getParam('no_customer');
ntsLib::setVar( 'admin/manage/appointments/create::noCustomer', $noCustomer );

$hidden = $_NTS['REQ']->getParam('hidden');
ntsLib::setVar( 'admin/manage/appointments/create::hidden', $hidden );

ntsLib::setVar( 'admin/manage/appointments/create::changeDate', TRUE );
ntsLib::setVar( 'admin/manage/appointments/create::showTitle', FALSE );

$canAddMore = TRUE;
ntsLib::setVar( 'admin/manage/appointments/create::canAddMore', $canAddMore );
?>