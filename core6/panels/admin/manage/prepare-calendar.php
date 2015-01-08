<?php
$cacheTime = array();

$t = $NTS_VIEW['t'];
$t->setDateDb( $cal );
$t->setEndMonth();
$fullEnd = $t->getTimestamp();
$t->setStartMonth();
$fullStart = $t->getStartDay();

$currentTs = $fullStart;
$dates = array();
while( $currentTs <= $fullEnd )
{
	$startDay = $t->getStartDay();
	$thisDate = $t->formatDate_Db();
	$weekDay = $t->getWeekday();
	$t->modify( '+1 day' );
	$endDay = $t->getTimestamp();

	$countApps = 0;
	$selectable = 0;
	$dates[ $thisDate ] = array( $startDay, $endDay, $weekDay, $countApps, $selectable );
	$currentTs = $endDay;
}
ntsLib::setVar( 'admin/manage:dates', $dates );

$selectedDate = $cal;
if( isset($calYear) )
	unset($calYear);

// ok now check my appointments
$dateStatus = array();
$dates2process = array();
reset( $dates );
foreach( $dates as $date => $da )
{
	$dateStatus[ $date ] = 0;
	$dates2process[ $date ] = 1;
}

$all_remain_dates = array_keys( $dates2process );
$init_start_date = $all_remain_dates[0];
$init_end_date = $all_remain_dates[ count($all_remain_dates) - 1 ];
$t->setDateDb( $init_start_date );
$init_start = $t->getStartDay();
$t->setDateDb( $init_end_date );
$init_end = $t->getEndDay();

$tm2->init( $init_start, $init_end );

if( ! isset($check_appointments) )
{
	$check_appointments = TRUE;
}

/* check available times */
if( $dates2process )
{
	reset( $dates2process );
	foreach( $dates2process as $checkDate => $dd )
	{
		$da = $dates[$checkDate];

		$tm2->dayMode = TRUE;
		if( ! $check_appointments )
		{
			$tm2->blockMode = TRUE;
		}
		$prepare_cal_times = $tm2->getAllTime( $da[0], $da[1] );
		if( $prepare_cal_times )
		{
			$dateStatus[$checkDate] = 1; // available
			unset( $dates2process[$checkDate] );
		}
		$tm2->dayMode = FALSE;
		if( ! $check_appointments )
		{
			$tm2->blockMode = FALSE;
		}
	}
}

/* count apps */
if( $check_appointments && $dates2process )
{
	$counts = array();
	$prefix = 'count_';
	reset( $dates2process );
	foreach( $dates2process as $checkDate => $dd )
	{
		$da = $dates[$checkDate];
		$day_start = $da[0];
		$day_end = $da[1];
		$counts[] = "COUNT(CASE WHEN starts_at >= $day_start AND (starts_at + duration) <= $day_end THEN 1 END) AS ${prefix}${checkDate}";
	}

	$ntsdb =& dbWrapper::getInstance();
	$what = join( ',', $counts );
	$where = array();

	if( $tm2->locationIds )
		$where['location_id'] = array( 'IN', $tm2->locationIds );
	if( $tm2->resourceIds )
		$where['resource_id'] = array( 'IN', $tm2->resourceIds );
//	if( $tm2->serviceIds )
//		$where['service_id'] = array( 'IN', $tm2->serviceIds );

//	$where['completed'] = array( 'NOT IN', array(HA_STATUS_CANCELLED, HA_STATUS_NOSHOW) );
	$where['completed'] = array( 'NOT IN', array(HA_STATUS_CANCELLED, HA_STATUS_NOSHOW, HA_STATUS_COMPLETED) );

//$ntsdb->_debug = TRUE;
	$count = $ntsdb->get_select( 
		$what,
		'appointments',
		$where
		);
//$ntsdb->_debug = FALSE;
//exit;

	if( isset($count[0]) )
	{
		foreach( $count[0] as $cdate => $date_count )
		{
			if( $date_count )
			{
				$date = substr( $cdate, strlen($prefix) );
				$dateStatus[$date] = 2; // fully booked
				unset( $dates2process[$date] );
			}
		}
	}
}

/* timeoffs */
if( $dates2process )
{
	$all_remain_dates = array_keys( $dates2process );
	$init_start_date = $all_remain_dates[0];
	$init_end_date = $all_remain_dates[ count($all_remain_dates) - 1 ];
	$t->setDateDb( $init_start_date );
	$init_start = $t->getStartDay();
	$t->setDateDb( $init_end_date );
	$init_end = $t->getEndDay();
	$tm2->init( $init_start, $init_end );

	reset( $dates2process );
	foreach( $dates2process as $checkDate => $dd )
	{
		$timeoffs = $tm2->getTimeoff( $checkDate );
		if( $timeoffs )
		{
			$dateStatus[$checkDate] = 3; // timeoff
			unset( $dates2process[$checkDate] );
		}
	}
}

$cssDates = array();
$okDates = array();
$linkedDates = array();
$labelDates = array();
$linkDates = array();

reset( $dateStatus );
foreach( $dateStatus as $date => $status )
{
	$linkedDates[] = $date;
	$dayClass = array();
	$dayLabel = '';
	switch( $status )
	{
		case 0:
			$dayClass[] = 'alert-archive';
			$dayLabel = M('Not Available');
			break;
		case 1:
			$dayClass[] = 'alert-available';
			$dayLabel = M('Available');
			$okDates[] = $date;
			break;
		case 2:
			$dayClass[] = 'alert-warning'; 
			$dayClass[] = 'alert-danger'; 
			$dayLabel = M('Fully Booked');
			break;
		case 3:
			$dayClass[] = 'alert-inverse';
			$dayLabel = M('Timeoff');
			break;
	}
	$cssDates[ $date ] = $dayClass;
	$labelDates[ $date ] = $dayLabel;
}
?>