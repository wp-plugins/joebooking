<?php
$time_unit = NTS_TIME_UNIT * 60;

$all_times = array();
if( $starts_at )
{
	$all_times = array( $starts_at );
}
else
{
	$t = $NTS_VIEW['t'];
	$t->setDateDb( $cal );
	$day_start = $t->getStartDay();
	$day_end = $t->getEndDay();

	$times_from = $day_start + NTS_TIME_STARTS;
	$times_to = $day_start + NTS_TIME_ENDS;
	for( $ts = $times_from; $ts < $times_to; $ts += $time_unit )
	{
		$all_times[] = $ts;
	}
}

/* CHECK AVAILABLE SLOTS */
$selectable = array();
if( $starts_at )
{
	$times = $tm2->getAllTime( $starts_at, $starts_at );
	foreach( $times as $ts => $slots )
	{
		foreach( $slots as $slot )
		{
			if( ! in_array($slot[0], $check_locs) )
				continue;
			if( ! in_array($slot[1], $check_ress) )
				continue;
			if( ! in_array($slot[2], $check_sers) )
				continue;

			$key = $ts . '-' . $slot[0] . '-' . $slot[1] . '-' . $slot[2];
			$selectable[ $key ] = 1;
		}
	}
}
else
{
	$chunk = 2 * 60 * 60;
	$rex_time = $day_start;

	while( $rex_time < $times_to )
	{
		$times = $tm2->getAllTime( $rex_time, $rex_time + $chunk - 1 );
		$rex_time += $chunk;

		foreach( $times as $ts => $slots )
		{
			foreach( $slots as $slot )
			{
				$key = $ts . '-' . $slot[0] . '-' . $slot[1] . '-' . $slot[2];
				$selectable[ $key ] = 1;
			}
		}
	}
}

/* BUILD AVAILABILITY */
$available = array(
	'customer'	=> 1,
	'location'	=> array(),
	'resource'	=> array(),
	'service'	=> array(),
	'time'		=> array(),
	);

$COUNT_SLOTS = 0;
$COUNT_CONTINUE = 0;
$COUNT_REAL_CHECK = 0;

reset( $all_times );
foreach( $all_times as $slot_time )
{
	foreach( $check_locs as $slot_lid )
	{
		foreach( $check_ress as $slot_rid )
		{
			foreach( $check_sers as $slot_sid )
			{
				$key = $slot_time . '-' . $slot_lid . '-' . $slot_rid . '-' . $slot_sid;
				$COUNT_SLOTS++;

				if( isset($selectable[$key]) )
				{
					$available['location'][$slot_lid] = 1;
					$available['resource'][$slot_rid] = 1;
					$available['service'][$slot_sid] = 1;
					$available['time'][$slot_time] = 1;
				}

				if( 
					( isset($available['location'][$slot_lid]) && (! is_array($available['location'][$slot_lid])) ) &&
					( isset($available['resource'][$slot_rid]) && (! is_array($available['resource'][$slot_rid])) ) &&
					( isset($available['service'][$slot_sid]) && (! is_array($available['service'][$slot_sid])) ) && 
					( isset($available['time'][$slot_time]) && (! is_array($available['time'][$slot_time])) )
				)
				{
					$COUNT_CONTINUE++;
					continue;
				}

				$slot = array(
					$slot_lid,
					$slot_rid,
					$slot_sid,
					array(
						($slot_time + $tm2->max_duration) => 1
						)
					);

				$COUNT_REAL_CHECK++;
				$slot_status = $tm2->checkSlot( $slot_time, $slot, TRUE );
				if( $slot_status )
				{
					if( ! (isset($available['location'][$slot_lid]) && ($available['location'][$slot_lid] === 1)) )
						$available['location'][$slot_lid] = 0;
					if( ! (isset($available['resource'][$slot_rid]) && ($available['resource'][$slot_rid] === 1)) )
						$available['resource'][$slot_rid] = 0;
					if( ! (isset($available['service'][$slot_sid]) && ($available['service'][$slot_sid] === 1)) )
						$available['service'][$slot_sid] = 0;
					if( ! (isset($available['time'][$slot_time]) && ($available['time'][$slot_time] === 1)) )
						$available['time'][$slot_time] = 0;
				}
				else
				{
					$slot_errors = $tm2->getSlotErrors();

					if( isset($slot_errors['customer']) )
					{
						$available['customer'] = $slot_errors;
					}

					if( ! isset($available['location'][$slot_lid]) )
						$available['location'][$slot_lid] = array();
					if( ! isset($available['resource'][$slot_rid]) )
						$available['resource'][$slot_rid] = array();
					if( ! isset($available['service'][$slot_sid]) )
						$available['service'][$slot_sid] = array();
					if( ! isset($available['time'][$slot_time]) )
						$available['time'][$slot_time] = array();

					if( $slot_errors )
					{
						foreach( $slot_errors as $k => $v )
						{
							if( is_array($available['location'][$slot_lid]))
							{
								if( ! isset($available['location'][$slot_lid][$k]) )
									$available['location'][$slot_lid][$k] = $v;
							}
							if( is_array($available['resource'][$slot_rid]))
							{
								if( ! isset($available['resource'][$slot_rid][$k]) )
									$available['resource'][$slot_rid][$k] = $v;
							}
							if( is_array($available['service'][$slot_sid]))
							{
								if( ! isset($available['service'][$slot_sid][$k]) )
									$available['service'][$slot_sid][$k] = $v;
							}
							if( is_array($available['time'][$slot_time]))
							{
								if( ! isset($available['time'][$slot_time][$k]) )
									$available['time'][$slot_time][$k] = $v;
							}
						}
					}
					else
					{
						if( is_array($available['location'][$slot_lid]) )
							$available['location'][$slot_lid] = 0;
						if( is_array($available['resource'][$slot_rid]) )
							$available['resource'][$slot_rid] = 0;
						if( is_array($available['service'][$slot_sid]) )
							$available['service'][$slot_sid] = 0;
						if( is_array($available['time'][$slot_time]) )
							$available['time'][$slot_time] = 0;
					}
				}
			}
		}
	}
}

//echo "<br>COUNT_SLOTS: $COUNT_SLOTS<br>";
//echo "<br>COUNT_REAL_CHECK: $COUNT_REAL_CHECK<br>";
//echo "<br>COUNT_CONTINUE: $COUNT_CONTINUE<br>";
?>