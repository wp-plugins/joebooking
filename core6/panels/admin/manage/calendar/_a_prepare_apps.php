<?php
$stats = array(
	'duration'		=> 0,
	'status_count'	=> array(
		'active'	=> array(),
		'completed'	=> array()
		),
	);

/* preload customers */
$preload = array(
	'user'		=> array(),
	'location'	=> array(),
	'resource'	=> array(),
	'service'	=> array(),
	);

$apps = array();
reset( $all_apps );
foreach( $all_apps as $a )
{
	$preload['appointment'][$a['id']] = 1;
	$preload['user'][$a['customer_id']] = 1;
	$preload['location'][$a['location_id']] = 1;
	$preload['resource'][$a['resource_id']] = 1;
	$preload['service'][$a['service_id']] = 1;
}

foreach( $preload as $class => $ids )
{
//	if( $class == 'appointment' )
//		continue;
	ntsObjectFactory::preload( $class, array_keys($ids) );
}


reset( $all_apps );
foreach( $all_apps as $a )
{
	$app = ntsObjectFactory::get('appointment');
//	$app->setByArray( $a );
	$app->setId( $a['id'] );
	$t->setTimestamp( $a['starts_at'] );

	switch( $split_by )
	{
		case 'day':
			$this_date = $t->formatDate_Db();
			$this_date_start = $t->getStartDay();
			$this_date_end = $t->getEndDay();
			break;
		case 'month':
			$this_date = $t->formatMonth_Db();
			$this_date_start = $t->getStartMonth();
			$this_date_end = $t->getEndMonth();
			break;
	}

	$t->setTimestamp( $a['starts_at'] );
	$app_start_date = $t->formatDate_Db();
	if( isset($start_date) )
	{
		if( $app_start_date < $start_date )
			$app_start_date = $start_date;
	}

	$t->setTimestamp( $a['starts_at'] + $a['duration'] );
	$app_end_date = $t->formatDate_Db();
	if( $app_end_date > $app_start_date ){
		$tod = $t->getTimeOfDay();
		if( $tod == 0 ){
			$t->modify('-1 day');
			$app_end_date = $t->formatDate_Db();
		}
	}
	if( isset($end_date) )
	{
		if( $app_end_date > $end_date )
			$app_end_date = $end_date;
	}

	$t->setDateDb( $app_start_date );
	$rex_date = $app_start_date;
	while( $rex_date <= $app_end_date )
	{
		switch( $split_by )
		{
			case 'day':
				$this_date = $t->formatDate_Db();
				break;
			case 'month':
				$this_date = $t->formatMonth_Db();
				break;
		}

		if( ! isset($apps[$this_date]) )
			$apps[$this_date] = array();
		$apps[$this_date][] = $app;

		switch( $split_by )
		{
			case 'day':
				$t->modify( '+1 day' );
				break;
			case 'month':
				$t->modify( '+1 month' );
				break;
		}
		$rex_date = $t->formatDate_Db();
	}

	// stats
	$completedStatus = $app->getProp('completed');
	$approvedStatus = $app->getProp('approved');

	if( ! in_array($completedStatus, array(HA_STATUS_NOSHOW,HA_STATUS_CANCELLED)) ){
		$stats['duration'] += $a['duration'];
		if( isset($a['duration2']) ){
			$stats['duration'] += $a['duration2'];
		}
	}

	if( $completedStatus )
	{
		if( ! isset($stats['status_count']['completed'][$completedStatus]) )
			$stats['status_count']['completed'][$completedStatus] = 0;
		$stats['status_count']['completed'][$completedStatus]++;
	}
	else
	{
		if( ! isset($stats['status_count']['active'][$approvedStatus]) )
			$stats['status_count']['active'][$approvedStatus] = 0;
		$stats['status_count']['active'][$approvedStatus]++;
	}
}

/* also add timeoffs */
$added_timeoff = FALSE;
if( ! 
	(
		(isset($group_ref) && $group_ref) OR 
		(isset($customer_id) && $customer_id)
	)
)
{
	$t->setDateDb( $start_date );
	$period_start = $t->getStartDay();
	$t->setDateDb( $end_date );
	$period_end = $t->getEndDay();

	reset( $all_timeoffs );
	foreach( $all_timeoffs as $to )
	{
		if( 
			($to['starts_at'] > $period_end) OR
			($to['ends_at'] < $period_start)
			)
		{
			continue;
		}

		$toff = ntsObjectFactory::get('timeoff');
		$toff->setByArray( $to );

		$t->setTimestamp( $to['starts_at'] );
		$toff_start_date = $t->formatDate_Db();
		if( $toff_start_date < $start_date )
			$toff_start_date = $start_date;

		$t->setTimestamp( $to['ends_at'] );
		$toff_end_date = $t->formatDate_Db();
		if( $toff_end_date > $end_date )
			$toff_end_date = $end_date;

		switch( $split_by )
		{
			case 'day':
				$this_date = $t->formatDate_Db();
				$this_date_start = $t->getStartDay();
				$this_date_end = $t->getEndDay();
				break;
			case 'month':
				$this_date = $t->formatMonth_Db();
				$this_date_start = $t->getStartMonth();
				$this_date_end = $t->getEndMonth();
				break;
		}

		$t->setDateDb( $toff_start_date );
		$rex_date = $toff_start_date;
		while( $rex_date <= $toff_end_date )
		{
			switch( $split_by )
			{
				case 'day':
					$this_date = $t->formatDate_Db();
					break;
				case 'month':
					$this_date = $t->formatMonth_Db();
					break;
			}

			if( ! isset($apps[$this_date]) )
				$apps[$this_date] = array();
			$apps[$this_date][] = $toff;
			$added_timeoff = TRUE;

			switch( $split_by )
			{
				case 'day':
					$t->modify( '+1 day' );
					break;
				case 'month':
					$t->modify( '+1 month' );
					break;
			}
			$rex_date = $t->formatDate_Db();
		}
	}
}

$am =& ntsAccountingManager::getInstance();
if( isset($preload['appointment']) )
{
	$am->load_postings( 'appointment', array_keys($preload['appointment']) );
}

/* if split by month then sort within month descending */
if( $split_by == 'month' OR $added_timeoff )
{
	$sortFunc = create_function('$a, $b', 'return ($a->getProp("starts_at") - $b->getProp("starts_at"));');
	foreach( array_keys($apps) as $date )
	{
		usort( $apps[$date], $sortFunc );
	}
}
?>