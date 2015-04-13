<?php
$tm2 = ntsLib::getVar( 'admin::tm2' );
//$cals = array( $start_date );
//_print_r( $cals );

$slotsArray = array();
foreach( $cals as $cal ){
	/* build lrs */
	$t->setDateDb( $cal );
	$dayStart = $t->getStartDay();
	$dayEnd = $t->getEndDay();

	$daySlotsStart = $dayStart + NTS_TIME_STARTS;
	$daySlotsEnd = $dayStart + NTS_TIME_ENDS;

	$slots = array();
	$index = array();
	for( $li = 0; $li < count($list); $li++ )
	{
		$slots[$li] = array( array() );
		$index[$li] = -1;
	}

	/* working times */
	$chunk = 2 * 60 * 60;
	$rex_time = $dayStart;
	while( $rex_time < $dayEnd )
	{
		$times = $tm2->getAllTime( $rex_time, $rex_time + $chunk - 1 );
		$rex_time = $rex_time + $chunk;

		reset( $times );
		foreach( $times as $ts => $slts )
		{
			reset( $slts );
			foreach( $slts as $sl )
			{
				$lid = $sl[ $tm2->SLT_INDX['location_id'] ];
				$rid = $sl[ $tm2->SLT_INDX['resource_id'] ];
				$sid = $sl[ $tm2->SLT_INDX['service_id'] ];
				$duration = $tm2->services[$sid]['duration'];

				for( $li = 0; $li < count($list); $li++ )
				{
					$thisLocs = $list[$li][1][0];
					$thisRess = $list[$li][1][1];
					$thisSers = $list[$li][1][2];
					if( ! (in_array($lid, $thisLocs) && in_array($rid, $thisRess) && in_array($sid, $thisSers)) )
					{
						continue;
					}

					if( isset($slots[$li][0][$index[$li]]) && ($ts <= $slots[$li][0][$index[$li]][1]) )
						$glue = true;
					else
						$glue = false;

					if( $glue )
					{
						if( $ts < $slots[$li][0][$index[$li]][0] )
							$slots[$li][0][$index[$li]][0] = $ts;
						if( ($ts + $duration) > $slots[$li][0][$index[$li]][1] )
							$slots[$li][0][$index[$li]][1] = ($ts + $duration);
						if( ! in_array($sid, $slots[$li][0][$index[$li]][3]) )
							$slots[$li][0][$index[$li]][3][] = $sid;
					}
					else
					{
						$index[$li]++;
						$slots[$li][0][ $index[$li] ] = array( 
							$ts, 
							$ts + $duration,
							HA_SLOT_TYPE_WO,
							array($sid)
							);
					}
				}
			}
		}
	}

	/* appointments */
	$where = array(
		'(starts_at + duration + lead_out)'	=> array('>', $dayStart),
		'starts_at'							=> array('<', $dayEnd)
		);

//	$apps = $tm2->getAppointments( $where, 'ORDER BY starts_at ASC' );
	$this_apps = isset($apps[$cal]) ? $apps[$cal] : array();
	reset( $this_apps );

	$slotApps = array();
	$index = array();
	for( $li = 0; $li < count($list); $li++ )
	{
		$slotApps[$li] = array();
		$lrst[$li] = array();
	}

	/* timeoffs */
	if( ($list_by == 'resource') OR (count($ress) <= 1) )
	{
		$toffs = $tm2->getTimeoff( $cal );
		reset( $toffs );
		foreach( $toffs as $toff )
		{
			if( ! in_array($toff['resource_id'], $ress) )
				continue;

			for( $li = 0; $li < count($list); $li++ )
			{
				$thisLocs = $list[$li][1][0];
				$thisRess = $list[$li][1][1];
				$thisSers = $list[$li][1][2];
				if( ! (in_array($toff['resource_id'], $thisRess)) ){
					continue;
					}

				$thisStart = ( $toff['starts_at'] > $daySlotsStart ) ? $toff['starts_at'] : $daySlotsStart;
				$thisEnd = ( $toff['ends_at'] < $daySlotsEnd ) ? $toff['ends_at'] : $daySlotsEnd;
				$slotApps[$li][] = array( $thisStart, $thisEnd, HA_SLOT_TYPE_TOFF, $toff['id'] );
			}
		}
	}

	foreach( $this_apps as $app ){
		$app = $app->getByArray();
		if( ! isset($app['duration_break']) ){
			$app['duration_break'] = 0;
		}
		if( ! isset($app['duration2']) ){
			$app['duration2'] = 0;
		}

		if( ! in_array($app['location_id'], $locs) )
			continue;
		if( ! in_array($app['resource_id'], $ress) )
			continue;
		if( ! in_array($app['service_id'], $sers) )
			continue;

		for( $li = 0; $li < count($list); $li++ ){
			$thisLocs = $list[$li][1][0];
			$thisRess = $list[$li][1][1];
			$thisSers = $list[$li][1][2];
			if( ! (in_array($app['location_id'], $thisLocs) && in_array($app['resource_id'], $thisRess) && in_array($app['service_id'], $thisSers)) ){
				continue;
			}

			$thisStart = ( $app['starts_at'] > $daySlotsStart ) ? $app['starts_at'] : $daySlotsStart;
			$appEnd = $app['starts_at'] + $app['duration'];
			$thisEnd = ( $appEnd < $daySlotsEnd ) ? $appEnd : $daySlotsEnd;

			if( $app['duration2'] ){
				$appEndLeadout = $app['starts_at'] + $app['duration'];
			}
			else {
				$appEndLeadout = $app['starts_at'] + $app['duration'] + $app['lead_out'];
			}
			$thisCheckEnd = ( $appEndLeadout < $daySlotsEnd ) ? $appEndLeadout : $daySlotsEnd;

			$slotApps[$li][] = array(
				$thisStart, 
				array( $thisEnd, $thisCheckEnd ),
				HA_SLOT_TYPE_APP_BODY,
				$app['id']
				);

			/* second part */
			if( isset($app['duration2']) && $app['duration2'] ){
				$thisStart = ( ($app['starts_at'] + $app['duration'] + $app['duration_break']) > $daySlotsStart ) ? ($app['starts_at'] + $app['duration'] + $app['duration_break']) : $daySlotsStart;
				$appEnd = $app['starts_at'] + $app['duration'] + $app['duration_break'] + $app['duration2'];
				$thisEnd = ( $appEnd < $daySlotsEnd ) ? $appEnd : $daySlotsEnd;

				$appEndLeadout = $app['starts_at'] + $app['duration'] + $app['duration_break'] + $app['duration2'] + $app['lead_out'];
				$thisCheckEnd = ( $appEndLeadout < $daySlotsEnd ) ? $appEndLeadout : $daySlotsEnd;

				$slotApps[$li][] = array(
					$thisStart, 
					array( $thisEnd, $thisCheckEnd ),
					HA_SLOT_TYPE_APP_BODY,
					$app['id'] . '_'
					);
			}
		}
	}

	$sortFunc = create_function('$a, $b', 'return ($a[0] - $b[0]);');

	//for( $li = 0; $li < count($list); $li++ ){
	//	usort( $slotApps[$li], $sortFunc );
	//	}

	/* now add appointments to slots checking overlap */
	for( $li = 0; $li < count($list); $li++ ){
		reset( $slotApps[$li] );
		foreach( $slotApps[$li] as $sa ){
			// check by rows
			$foundRow = -1;
			$addSlot = true;

			for( $row = 0; $row < count($slots[$li]); $row++ ){
				$ok = true;
				reset($slots[$li][$row]);
				for( $si = 0; $si < count($slots[$li][$row]); $si++ ){
					$slot = $slots[$li][$row][$si];

//					if( ($sa[0] == $slot[0]) && ($sa[1] == $slot[1]) && is_array($sa[3]) ){
					if( ($sa[0] == $slot[0]) && is_array($sa[3]) )
					{
						$thisAppSid = $sa[3][0]['service_id'];
					}

					$check_slot_end = is_array($slot[1]) ? $slot[1][1] : $slot[1];
					$check_sa_end = is_array($sa[1]) ? $sa[1][1] : $sa[1];

					if( ($sa[0] < $check_slot_end) && ($check_sa_end > $slot[0]) )
					{
						// overlaps
						$ok = false;
						break;
					}
					else
					{
					}
	//				elseif( ($slot[2] == HA_SLOT_TYPE_WO) && is_array($sa[3]) && (count($slot[3]) < 2) ){
	//					$ok = false;
	//					break;
	//					}
					}
				if( $ok ){
					$foundRow = $row;
					break;
					}
				}

			if( $addSlot ){
				if( $foundRow >= 0 )
				{
					$slots[$li][ $foundRow ][] = $sa;
				}
				else
				{
					$slots[$li][] = array( $sa );
					$foundRow = count( $slots[$li] ) - 1;
				}
				

				/* if lead out the add the slot for that */
				if( is_array($sa[1]) && ($sa[1][1] > $sa[1][0]) )
				{
					$lead_out_slot = array(
						$sa[1][0],
						$sa[1][1],
						HA_SLOT_TYPE_APP_LEAD,
						$sa[3]
						);
					$slots[$li][ $foundRow ][] = $lead_out_slot;
				}

				}
			}
		}

	for( $li = 0; $li < count($list); $li++ ){
		for	($r = 0; $r < count($slots[$li]); $r++ ){
			usort( $slots[$li][$r], $sortFunc );
			}
		}

	/* ok now add slots to fill unavailable time */
	for( $li = 0; $li < count($list); $li++ ){
		$oldSlots = $slots[$li];
		$slots[$li] = array( array() );

		for( $r = 0; $r < count($oldSlots); $r++ ){
			$slots[$li][$r] = array();
			$slotCount = count($oldSlots[$r]);
			if( $slotCount ){
				for( $ii = 0; $ii < $slotCount; $ii++ ){
					if( $ii == 0 )
					{
						$checkStart = $daySlotsStart;
					}
					else
					{
						if( is_array($oldSlots[$r][$ii-1][1]) )
							$checkStart = $oldSlots[$r][$ii-1][1][0];
						else
							$checkStart = $oldSlots[$r][$ii-1][1];
					}
					$checkEnd = ( $ii == ($slotCount-1) ) ? $daySlotsEnd : $oldSlots[$r][$ii+1][0];

					if( $oldSlots[$r][$ii][0] > $checkStart ){
						$slots[$li][$r][] = array($checkStart, $oldSlots[$r][$ii][0], HA_SLOT_TYPE_NA);
						}

					$slots[$li][$r][] = $oldSlots[$r][$ii];
					$oldSlots[$r][$ii][0] = $checkStart;

					if( is_array($oldSlots[$r][$ii][1]) )
					{
						if( $oldSlots[$r][$ii][1][0] < $checkEnd )
						{
							$slots[$li][$r][] = array($oldSlots[$r][$ii][1][0], $checkEnd, HA_SLOT_TYPE_NA);
						}
						$oldSlots[$r][$ii][1][0] = $checkEnd;
					}
					else
					{
						if( $oldSlots[$r][$ii][1] < $checkEnd )
						{
							$slots[$li][$r][] = array($oldSlots[$r][$ii][1], $checkEnd, HA_SLOT_TYPE_NA);
						}
						$oldSlots[$r][$ii][1] = $checkEnd;
					}
					}
				}
			else {
				$slots[$li][$r][] = array($daySlotsStart, $daySlotsEnd, HA_SLOT_TYPE_NA);
				}
			}
		}

	/* add width property */
	$totalDuration = ($daySlotsEnd - $daySlotsStart);
	for( $li = 0; $li < count($list); $li++ ){
		for( $r = 0; $r < count($slots[$li]); $r++ ){
			$alreadyWidth = 0;
			$slotCount = count($slots[$li][$r]);
			for( $ss = 0; $ss < $slotCount; $ss++ ){
				if( $ss < ($slotCount-1) ){
					$this_end = is_array($slots[$li][$r][$ss][1]) ? $slots[$li][$r][$ss][1][0] : $slots[$li][$r][$ss][1];
					$this_duration = $this_end - $slots[$li][$r][$ss][0];
					$width = ( $totalDuration > 0 ) ? floor(99 * 100 * (($this_duration)/$totalDuration ))/100 : 0;
					}
				else {
					$width = floor(100 * (99 - $alreadyWidth)) / 100;
					}
				$slots[$li][$r][$ss][4] = $width;
				$alreadyWidth += $width;
				}
			}
		}

	$slotsArray[ $cal ] = $slots;
	}
?>