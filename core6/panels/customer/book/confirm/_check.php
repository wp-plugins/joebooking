<?php
$tm2 = $NTS_VIEW['tm2'];

$error_msg = array();
$delete_apps = array();

/* final check */
for( $ii = 0; $ii < count($apps); $ii++ )
{
	if( isset($apps[$ii]['location_id']) && $apps[$ii]['location_id'] )
		$tm2->setLocation( $apps[$ii]['location_id'] );
	if( isset($apps[$ii]['resource_id']) && $apps[$ii]['resource_id'] )
		$tm2->setResource( $apps[$ii]['resource_id'] );
	if( isset($apps[$ii]['service_id']) && $apps[$ii]['service_id'] )
		$tm2->setService( $apps[$ii]['service_id'] );

	$times = $tm2->getAllTime( $apps[$ii]['starts_at'], $apps[$ii]['starts_at'] + 1 );
	if( ! isset($times[$apps[$ii]['starts_at']]) )
	{
		// not available
		$t->setTimestamp( $apps[$ii]['starts_at'] );
		$error_msg[] = join( ': ', array('Not Available', $t->formatFull()) );
		$delete_apps[] = $ii;
		continue;
	}

	/* get slots and fill-in default location, resource, service if needed */
	$slots = $times[$apps[$ii]['starts_at']];

	$lids = array();
	$rids = array();
	$sids = array();

	foreach( $slots as $slot )
	{
		$lid = $slot[ $tm2->SLT_INDX['location_id'] ];
		$rid = $slot[ $tm2->SLT_INDX['resource_id'] ];
		$sid = $slot[ $tm2->SLT_INDX['service_id'] ];
		if( ! in_array($lid, $lids) )
			$lids[] = $lid;
		if( ! in_array($rid, $rids) )
			$rids[] = $rid;
		if( ! in_array($sid, $sids) )
			$sids[] = $sid;
	}

	if( (! isset($apps[$ii]['customer_id'])) OR (! $apps[$ii]['customer_id']) )
	{
		if( ntsLib::getCurrentUserId() )
		{
			$apps[$ii]['customer_id'] = ntsLib::getCurrentUserId();
		}
	}

	if( (! isset($apps[$ii]['location_id'])) OR (! $apps[$ii]['location_id']) )
	{
		if( count($lids) == 1 )
			$apps[$ii]['location_id'] = $lids[0];
		else
		{
			$error_msg[] = join( ': ', array(M('Required'), M('Location')) );
			continue;
		}
	}

	if( (! isset($apps[$ii]['resource_id'])) OR (! $apps[$ii]['resource_id']) )
	{
		if( count($rids) == 1 )
			$apps[$ii]['resource_id'] = $rids[0];
		else
		{
			$error_msg[] = join( ': ', array(M('Required'), M('Bookable Resource')) );
			continue;
		}
	}

	if( (! isset($apps[$ii]['service_id'])) OR (! $apps[$ii]['service_id']) )
	{
		if( count($sids) == 1 )
			$apps[$ii]['service_id'] = $sids[0];
		else
		{
			$error_msg[] = join( ': ', array(M('Required'), M('Service')) );
			continue;
		}
	}
}

if( $delete_apps )
{
	$final_apps = array();
	for( $ii = 0; $ii < count($apps); $ii++ )
	{
		if( ! in_array($ii, $delete_apps) )
		{
			$final_apps[] = $apps[$ii];
		}
	}
	$session->set_userdata( 'apps', $final_apps );
}

if( $error_msg )
{
	$error_msg = join( '<br/>', $error_msg );
	ntsView::addAnnounce( $error_msg, 'error' );
	$forwardTo = ntsLink::makeLink('-current-/..');
	ntsView::redirect( $forwardTo );
	exit;
}
?>