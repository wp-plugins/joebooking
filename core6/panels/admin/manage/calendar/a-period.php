<?php
require( dirname(__FILE__) . '/_a_init.php' );
$period = $_NTS['REQ']->getParam('period');

$split_by = ( strlen($period) == 6 ) ? 'month' : 'day';
if( $split_by == 'month' )
{
	$t->setMonthDb( $period );
	$periodStart = $t->getStartMonth();
	$periodEnd = $t->getEndMonth();
}
else
{
	$t->setDateDb( $period );
	$periodStart = $t->getStartDay();
	$periodEnd = $t->getEndDay();
}

$where = array(
	'(starts_at + duration + lead_out)'	=> array('>', $periodStart),
	'starts_at'							=> array('<', $periodEnd)
	);

// $where['completed'] = array( '<>', HA_STATUS_CANCELLED );
if( $display == 'calendar' ){
	$where['completed'] = array( '<>', HA_STATUS_CANCELLED );
//	$where['completed '] = array( '<>', HA_STATUS_NOSHOW );
}
else {
	$where['completed'] = array('>=', 0);
}


$tm2 = ntsLib::getVar( 'admin::tm2' );
$tm2->processCompleted = TRUE;

if( $locs )
{
	$where['location_id'] = array( 'IN', $locs );
}
if( $ress )
{
	$where['resource_id'] = array( 'IN', $ress );
}

/* get apps */
$all_apps = $tm2->getAppointments( $where, 'ORDER BY starts_at ASC, id DESC' );

$apps = array();
foreach( $all_apps as $a )
{
	if( $a['starts_at'] > $periodEnd )
		break;

	if( ($a['starts_at'] + $a['duration']) < $periodStart )
		continue;

	$app = ntsObjectFactory::get('appointment');
	$app->setByArray( $a );
	$apps[] = $app;
}

/* get timeoffs */
$all_timeoffs = array();
if( count($ress) == 1 )
{
	$all_timeoffs = $tm2->getTimeoff( $start_date, $end_date );
}
foreach( $all_timeoffs as $to )
{
	if( $to['starts_at'] > $periodEnd )
		break;

	if( $to['ends_at'] < $periodStart )
		continue;

	$toff = ntsObjectFactory::get('timeoff');
	$toff->setByArray( $to );
	$apps[] = $toff;
}
if( $all_timeoffs )
{
	$sortFunc = create_function('$a, $b', 'return ($a->getProp("starts_at") - $b->getProp("starts_at"));');
	usort( $apps, $sortFunc );
}

switch( $display )
{
	case 'browse':
		$view_file = 'period_list';
		break;

	default:
		$view_file = 'period';
		break;
}

require( dirname(__FILE__) . '/_a_labels.php' );

$view = array(
	'labels'	=> $labels,
	'apps'		=> $apps,
	'date'		=> $period,
	);

$this->render(
	dirname(__FILE__) . '/views/' . $view_file . '.php',
	$view
	);
?>