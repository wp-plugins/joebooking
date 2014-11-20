<?php
$list = ntsLib::getVar( 'admin/manage/calendar::list' );
$tm2 = ntsLib::getVar( 'admin::tm2' );

$locs = ntsLib::getVar( 'admin::locs' );
$ress = ntsLib::getVar( 'admin::ress' );
$sers = ntsLib::getVar( 'admin::sers' );

$tm2->setLocation( $locs );
$tm2->setResource( $ress );
$tm2->setService( $sers );

$t = $NTS_VIEW['t'];

$lastDate = $show_dates[count($show_dates) - 1];
$t->setDateDb( $lastDate );
$t->modify( '+1 day' );
$fullEnd = $t->getTimestamp();

$firstDate = $show_dates[0];
$t->setDateDb( $firstDate );
$fullStart = $t->getStartDay();

$currentDate = $firstDate;
$dates = array();
while( $currentDate <= $lastDate )
{
	$startDay = $t->getStartDay();
	$thisDate = $t->formatDate_Db();
	$weekDay = $t->getWeekday();
	$t->modify( '+1 day' );
	$endDay = $t->getTimestamp();

	$countApps = 0;
	$selectable = 0;
	$dates[ $thisDate ] = array( $startDay, $endDay, $weekDay, $countApps, $selectable );
	$currentDate = $t->formatDate_Db();
}

$appointments = array();
$availability = array();
$timeoffs = array();

// check appointments
$dates2process = array();
reset( $dates );
foreach( $dates as $date => $da )
{
	$dates2process[ $date ] = 1;
	for( $ii = 0; $ii < count($list); $ii++ )
	{
		$appointments[$ii][$date] = 0;
		$availability[$ii][$date] = 0;
		$timeoffs[$ii][$date] = 0;
	}
}

$where = array(
	'(starts_at + duration + lead_out)'	=> array( '>', $fullStart ),
	'starts_at'							=> array( '<', $fullEnd ),
	'location_id'						=> array( 'IN', $locs ),
	'service_id'						=> array( 'IN', $sers ),
	'resource_id'						=> array( 'IN', $ress ),
//		'completed'							=> array( 'NOT IN', array(HA_STATUS_CANCELLED, HA_STATUS_NOSHOW) ),
	);

$totalCount = $tm2->countAppointments( $where );

$perQuery = 100;
$startOne = 0;
$lastOne = $startOne + $perQuery;
$checkStart = $fullStart;

$processDatesWithApps = array();
while( $startOne < $totalCount )
{
	$apps = $tm2->getAppointments( $where, "ORDER BY starts_at ASC LIMIT $startOne, $perQuery" );

	reset( $apps );
	foreach( $apps as $a )
	{
		if( ! $dates2process ){
			break;
			}
		if( ($a['starts_at'] + $a['duration'] + $a['lead_out']) < $checkStart )
			continue;

		if( ! in_array($a['location_id'], $locs) )
			continue;
		if( ! in_array($a['resource_id'], $ress) )
			continue;
		if( ! in_array($a['service_id'], $sers) )
			continue;

		reset( $dates2process );
		foreach( array_keys($dates2process) as $date )
		{
			$da = $dates[$date];
			if( $da[0] >= ($a['starts_at'] + $a['duration'] + $a['lead_in']) )
				continue;
			if( $da[1] <= ($a['starts_at'] - $a['lead_in']) )
				continue;

			for( $ii = 0; $ii < count($list); $ii++ )
			{
				$list_lrs = $list[$ii][1];

				if( ! in_array($a['location_id'], $list_lrs[0]) )
					continue;
				if( ! in_array($a['resource_id'], $list_lrs[1]) )
					continue;
				if( ! in_array($a['service_id'], $list_lrs[2]) )
					continue;
				$appointments[$ii][$date]++;
			}

			$dateStatus[$date] = 2;
			$processDatesWithApps[$date] = 1;

			break;
		}
	}
	if( ! $dates2process )
	{
		break;
	}
	$startOne += $perQuery;
	$lastOne = $startOne + $perQuery;
}


/* now see if I have dates without appointments and timeoffs so we check if timeblocks are defined */
$times = $tm2->getAllTime( $fullStart, $fullEnd );

reset( $dates2process );
foreach( array_keys($dates2process) as $date )
{
	$da = $dates[$date];
	reset( $times );
	foreach( $times as $ts => $slots )
	{
		if( $ts >= $da[1] )
		{
			break;
		}
		if( $ts < $da[0] )
		{
			continue;
		}

		for( $ii = 0; $ii < count($list); $ii++ )
		{
			$list_lrs = $list[$ii][1];
			reset( $slots );
			foreach( $slots as $slot )
			{
				if( ! in_array($slot[$tm2->SLT_INDX['location_id']], $list_lrs[0]) )
					continue;
				if( ! in_array($slot[$tm2->SLT_INDX['resource_id']], $list_lrs[1]) )
					continue;
				if( ! in_array($slot[$tm2->SLT_INDX['service_id']], $list_lrs[2]) )
					continue;
				$availability[$ii][$date]++;
				break;
			}
		}
	}
}

/* get timeoffs */
$toffs = $tm2->getTimeoff( $firstDate, $lastDate );

reset( $dates2process );
foreach( array_keys($dates2process) as $date )
{
	$da = $dates[$date];
	reset( $times );
	foreach( $toffs as $to )
	{
		if( $to['starts_at'] >= $da[1] )
		{
			break;
		}
		if( $to['ends_at'] < $da[0] )
		{
			continue;
		}

		for( $ii = 0; $ii < count($list); $ii++ )
		{
			$list_lrs = $list[$ii][1];
			if( ! in_array($to['resource_id'], $list_lrs[1]) )
				continue;
			$timeoffs[$ii][$date]++;
			break;
		}
	}
}
?>
