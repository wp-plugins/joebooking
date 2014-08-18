<?php
$object = ntsLib::getVar( 'admin/manage/appointments/edit::OBJECT' );
$conflicts = $object->get_conflicts();

$t = $NTS_VIEW['t'];

$locs = ntsLib::getVar( 'admin::locs' );
$ress = ntsLib::getVar( 'admin::ress' );
$sers = ntsLib::getVar( 'admin::sers' );

$lid = $object->getProp('location_id');
$rid = $object->getProp('resource_id');
$sid = $object->getProp('service_id');

$tm2 = ntsLib::getVar('admin::tm2');

$starts_at = $object->getProp('starts_at');
$t->setTimestamp( $starts_at );
$start_init = $t->getStartMonth();
$end_init = $t->getEndMonth();
$tm2->init( $start_init, $end_init );

$tm2->setSkip( array($object->getId()) );

/* GET REQUESTED CHANGES */
$selected = array(
	'location_id'	=> $_NTS['REQ']->getParam('location_id'),
	'resource_id'	=> $_NTS['REQ']->getParam('resource_id'),
	'service_id'	=> $_NTS['REQ']->getParam('service_id'),
	'starts_at'		=> $_NTS['REQ']->getParam('starts_at'),
	'cal'			=> $_NTS['REQ']->getParam('cal'),
	);

$final = array(
	'location_id'	=> $selected['location_id'] ? $selected['location_id'] : $lid,
	'resource_id'	=> $selected['resource_id'] ? $selected['resource_id'] : $rid,
	'service_id'	=> $selected['service_id'] ? $selected['service_id'] : $sid,
	'starts_at'		=> $selected['starts_at'] ? $selected['starts_at'] : $starts_at,
	);

/* CHECK THIS APP STATUS */
$starts_at = $object->getProp('starts_at');
$check_locs = array( $lid );
$check_ress = array( $rid );
$check_sers = array( $sid );
require( dirname(__FILE__) . '/../../create/_build_availability.php' );
$app_status = $available;

/* CHECK SELECTED STATUS */
if( $selected['starts_at'] )
	$starts_at = $selected['starts_at'];
if( $selected['location_id'] )
	$check_locs = array( $selected['location_id'] );
if( $selected['resource_id'] )
	$check_ress = array( $selected['resource_id'] );
if( $selected['service_id'] )
	$check_sers = array( $selected['service_id'] );

require( dirname(__FILE__) . '/../../create/_build_availability.php' );
$selected_status = $available;

$total_available = $selected_status;

/* CHECK ALL DAY AVAILABILITY */
$save_check_locs = $check_locs;
$save_check_ress = $check_ress;
$save_check_sers = $check_sers;
$save_starts_at = $starts_at;

$check_locs = $locs;
require( dirname(__FILE__) . '/../../create/_build_availability.php' );
$check_locs = $save_check_locs;
$total_available['location'] = $available['location'];

$check_ress = $ress;
require( dirname(__FILE__) . '/../../create/_build_availability.php' );
$check_ress = $save_check_ress;
$total_available['resource'] = $available['resource'];

$check_sers = $sers;
require( dirname(__FILE__) . '/../../create/_build_availability.php' );
$check_sers = $save_check_sers;
$total_available['service'] = $available['service'];

$cal = $_NTS['REQ']->getParam('cal');
if( ! $cal )
{
	$t->setTimestamp( $starts_at );
	$cal = $t->formatDate_Db();
}
$starts_at = 0;

$t->setDateDb( $cal );
$start_init = $t->getStartMonth();
$end_init = $t->getEndMonth();
$tm2->init( $start_init, $end_init );

require( dirname(__FILE__) . '/../../create/_build_availability.php' );
$starts_at = $save_starts_at;
$total_available['time'] = $available['time'];

$available = $total_available;

$view = array(
	'object'		=> $object,
	'conflicts'		=> $conflicts,
	'available'		=> $available,
	'selected'		=> $selected,
	'all_times'		=> $all_times,
	'app_status'	=> $app_status,
	'selected_status'	=> $selected_status,
	'final'			=> $final,
	);

$ff =& ntsFormFactory::getInstance();
$form_file = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $form_file, $view );

$view['form'] = $form;

$is_ajax = ntsLib::isAjax();
$view_file = $is_ajax ? 'ajax.php' : 'index.php';

$this->render(
	dirname(__FILE__) . '/' . $view_file,
	$view
	);
?>