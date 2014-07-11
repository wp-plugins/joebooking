<?php
require( dirname(__FILE__) . '/_a_init.php' );
if( $date )
{
	$t->setDateDb( $date );
}
else
{
	$t->setNow();
}
$start_date = $t->formatDate_Db();

if( $display == 'calendar' )
{
	switch( $range )
	{
		case 'day':
		case 'dayloc':
			$start_date = $t->formatDate_Db();
			$end_date = $start_date;
			break;

		case 'week':
			$t->setStartWeek();
			$start_date = $t->formatDate_Db();
			$t->setEndWeek();
			$end_date = $t->formatDate_Db();
			break;

		case 'month':
			$t->setStartMonth();
			$start_date = $t->formatDate_Db();
			$t->setEndMonth();
			$end_date = $t->formatDate_Db();
			break;
	}
}

if( $end_date < $start_date )
	$end_date = $start_date;

$saveOn = array(
	'start'		=> $start_date,
	'end'		=> $end_date,
	'display'	=> $display,
	'range'		=> $range
	);
ntsView::setPersistentParams( $saveOn, 'admin/manage/calendar' );

$this_link = ntsLink::makeLink('-current-');
$session = new ntsSession;
$session->set_userdata( 'calendar_view', $this_link );

/* get appointments */
$t->setDateDb( $start_date );
$periodStart = $t->getStartDay();
$t->setDateDb( $end_date );
$periodEnd = $t->getEndDay();

$where = array(
	'(starts_at + duration + lead_out)'	=> array('>', $periodStart),
	'starts_at'							=> array('<', $periodEnd)
	);

if( $display == 'calendar' )
{
	$where['completed'] = array( '<>', HA_STATUS_CANCELLED );
//	$where['completed '] = array( '<>', HA_STATUS_NOSHOW );
}
else
{
	$where['completed'] = array('>=', 0);
}

$tm2 = ntsLib::getVar( 'admin::tm2' );
$tm2->processCompleted = TRUE;
$tm2->init( $periodStart, $periodEnd );

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

/* get timeoffs */
$all_timeoffs = array();
if( count($ress) == 1 )
{
	$all_timeoffs = $tm2->getTimeoff( $start_date, $end_date );
}

$split_by = 'day';
require( dirname(__FILE__) . '/_a_prepare_apps.php' );
require( dirname(__FILE__) . '/_a_labels.php' );

$dates = array();
if( $display == 'calendar' )
{
	$rex_date = $start_date;
	while( $rex_date <= $end_date )
	{
		$t->setDateDb( $rex_date );
		$startDay = $t->getStartDay();
		$t->modify( '+1 day' );
		$endDay = $t->getTimestamp();

		$selectable = 0;
		$dates[ $rex_date ] = array( $startDay, $endDay, $selectable );
		$rex_date = $t->formatDate_Db();
	}

	reset( $dates );
	foreach( $dates as $rd => $da )
	{
		$tm2->dayMode = TRUE;
		$times = $tm2->getAllTime( $da[0], $da[1] - 1 );
		if( $times )
		{
			$dates[$rd][2] = 1;
		}
		$tm2->dayMode = FALSE;
	}
}

$view = array(
	'labels'		=> $labels,
	'display'		=> $display,
	'start_date'	=> $start_date,
	'end_date'		=> $end_date,
	'range'			=> $range,
	'apps'			=> $apps,
	'dates'			=> $dates,
	'stats'			=> $stats,
	);

switch( $display )
{
	case 'browse':
		$view_file = 'list';
		break;

	default:
		switch( $range )
		{
			case 'day':
			case 'dayloc':
				$view_file = 'day';
				break;

			default:
				$view_file = 'index';
				break;
		}
		break;
}

$this->render( 
	dirname(__FILE__) . '/views/' . $view_file . '.php',
	$view
	);
?>