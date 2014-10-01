<?php
$t = $NTS_VIEW['t'];

$now = time();
$t->setTimestamp( $now );
$today = $t->formatDate_Db();

/* set virtual appointments */
$session = new ntsSession;
$apps = $session->userdata('apps');
$coupon = $session->userdata( 'coupon' );

$tm2 = $NTS_VIEW['tm2'];

if( $apps )
{
	reset( $apps );
	foreach( $apps as $a )
	{
		if( $a['service_id'] )
		{
			$service = ntsObjectFactory::get( 'service' );
			$service->setId( $a['service_id'] );
			if( ! isset($a['duration']) )
				$a['duration'] = $service->getProp('duration');
			if( ! isset($a['lead_in']) )
				$a['lead_in'] = $service->getProp('lead_in');
			if( ! isset($a['lead_out']) )
				$a['lead_out'] = $service->getProp('lead_out');
			if( ! isset($a['seats']) )
				$a['seats'] = 1;
			$tm2->addVirtualAppointment( $a );
		}
	}
}

/* set which ones were selected and thus can be changed */
$requested = array(
	'location'	=> 0,
	'resource'	=> 0,
	'service'	=> 0,
	'time'	=> 0,
	);
reset( $requested );
foreach( array_keys($requested) as $k )
{
	$requested[$k] = $_NTS['REQ']->getParam($k);
}

/* find out the cal date */
$cal = $_NTS['REQ']->getParam('cal');
$requested_time = $_NTS['REQ']->getParam('time');
if( $requested_time )
{
	$t->setTimestamp( $requested_time );
	$cal = $t->formatDate_Db();
}
else
{
	if( $cal )
	{
		// already requested
	}
	else
	{
		$next = $tm2->getNextTimes( $now, 1 );
		if( $next )
		{
			$t->setTimestamp( $next[0] );
			$cal = $t->formatDate_Db();
		}
		else
		{
		}
	}
}

$view = array();
if( ! $cal )
{
	$is_ajax = ntsLib::isAjax();
	$view_file = $is_ajax ? 'ajax.php' : 'index.php';
	$this->render(
		dirname(__FILE__) . '/' . $view_file,
		$view
		);
	return;
}

$locations = array();
$resources = array();
$services = array();

if( $requested_time )
{
	$lrs = array();
	$times = $tm2->getAllTime( $requested['time'], $requested['time'] );
	foreach( $times as $ts => $slots )
	{
		foreach( $slots as $slot )
		{
			$lrs[] = array(
				$slot[0],
				$slot[1],
				$slot[2],
				);
		}
	}
}
else
{
	$lrs = $tm2->getLrs( TRUE, $cal );
}

if( ! $lrs )
{
	$is_ajax = ntsLib::isAjax();
	$view_file = $is_ajax ? 'ajax.php' : 'index.php';
	$this->render(
		dirname(__FILE__) . '/' . $view_file,
		$view
		);
	return;
}

/* preload objects to save queries */
reset( $lrs );
$all_lids = array();
$all_rids = array();
$all_sids = array();
foreach( $lrs as $lrsa )
{
	$all_lids[ $lrsa[0] ] = 1;
	$all_rids[ $lrsa[1] ] = 1;
	$all_sids[ $lrsa[2] ] = 1;
}

ntsObjectFactory::preload( 'location', array_keys($all_lids) );
ntsObjectFactory::preload( 'resource', array_keys($all_rids) );
ntsObjectFactory::preload( 'service', array_keys($all_sids) );

reset( $lrs );
foreach( $lrs as $lrsa )
{
	if( ! isset($locations[$lrsa[0]]) )
	{
		$obj = ntsObjectFactory::get( 'location' );
		$obj->setId( $lrsa[0] );
		$locations[ $lrsa[0] ] = $obj;
	}

	if( ! isset($resources[$lrsa[1]]) )
	{
		$obj = ntsObjectFactory::get( 'resource' );
		$obj->setId( $lrsa[1] );
		$resources[ $lrsa[1] ] = $obj;
	}

	if( ! isset($services[$lrsa[2]]) )
	{
		$obj = ntsObjectFactory::get( 'service' );
		$obj->setId( $lrsa[2] );
		$services[ $lrsa[2] ] = $obj;
	}
}

/* get all dates for calendar */
$t->setDateDb( $cal );
$t->setStartMonth();
$dates_time_from = $t->getTimestamp();

$ntsConf =& ntsConf::getInstance();
$show_months = $ntsConf->get('monthsToShow');
if( $show_months > 1 )
	$t->modify( '+' . ($show_months - 1) . ' months' );
$t->setEndMonth();
$dates_time_to = $t->getTimestamp();

$tm2->init( $dates_time_from, $dates_time_to );

$dates = array();
$tm2->dayMode = TRUE;
$day_start = $dates_time_from;
$t->setTimestamp( $day_start );

while( $day_start <= $dates_time_to )
{
	$this_date = $t->formatDate_Db();
	$t->modify( '+1 day' );
	$day_end = $t->getTimestamp();

	if( $tm2->customerSide && ($this_date < $today) )
	{
		$times = array();
	}
	else
	{
		$times = $tm2->getAllTime( $day_start, $day_end );
	}
	if( $times )
	{
		$dates[] = $this_date;
	}
	$day_start = $day_end;
}
$tm2->dayMode = FALSE;

/* get times for the current date */
$t->setDateDb( $cal );
$time_start = $t->getStartDay();
$time_end = $t->getEndDay();
$times = $tm2->getAllTime( $time_start, $time_end );

/* SET THIS APPOINTMENT */
$this_a = array(
	'location_id'	=> 0,
	'resource_id'	=> 0,
	'service_id'	=> 0, 
	'starts_at'		=> 0, 
	'customer_id'	=> 0,
	);
if( count($locations) == 1 )
{
	$ids = array_keys($locations);
	$this_a['location_id'] = $ids[0];
}
if( count($resources) == 1 )
{
	$ids = array_keys($resources);
	$this_a['resource_id'] = $ids[0];
}
if( count($services) == 1 )
{
	$ids = array_keys($services);
	$this_a['service_id'] = $ids[0];
}
$this_a['customer_id'] = ntsLib::getCurrentUserId();
if( $requested['time'] )
{
	$this_a['starts_at'] = $requested['time'];
}
/* END OF THIS APPOINTMENT */

/* READY */
if( 
	( $this_a['location_id'] OR $auto_location )
	&& 
	( $this_a['resource_id'] OR $auto_resource )
	&& 
	$this_a['service_id']
	&& 
	$this_a['starts_at']
)
{
	$forwardTo = ntsLink::makeLink(
		'-current-/add',
		'',
		array(
			'location'	=> $this_a['location_id'],
			'resource'	=> $this_a['resource_id'],
			'service'	=> $this_a['service_id'],
			'time'		=> $this_a['starts_at'],
			)
		);
	ntsView::redirect( $forwardTo );
	exit;
}
/* END OF READY */

$view = array(
	'this_a'		=> $this_a,
	'locations'		=> $locations,
	'resources'		=> $resources,
	'services'		=> $services,
	'requested'		=> $requested,
	'requested_cal'	=> $cal,
	'dates'			=> $dates,
	'times'			=> $times,
	'show_months'	=> $show_months,

	'auto_resource'		=> $auto_resource,
	'auto_location'		=> $auto_location,
	);

//_print_r( $requested );

$is_ajax = ntsLib::isAjax();
$view_file = $is_ajax ? 'ajax.php' : 'index.php';

$this->render(
	dirname(__FILE__) . '/' . $view_file,
	$view
	);
?>