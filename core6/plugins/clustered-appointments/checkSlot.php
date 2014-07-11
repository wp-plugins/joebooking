<?php
$plugin = 'clustered-appointments';
$plm =& ntsPluginManager::getInstance();
$settings = $plm->getPluginSettings( $plugin );
$gap_before = $settings['gap_before'];
$gap_after = $settings['gap_after'];

$this->companyT->setTimestamp( $ts );
$dayStart = $this->companyT->getStartDay();
$dayEnd = $this->companyT->getEndDay();

$slot_starts = $ts;
$service_min_duration = $this->services[$slot_sid]['duration'];
$slot_ends = $slot_starts + $service_min_duration;

$removeThisSlot = FALSE;
reset( $this->apps );
foreach( $this->apps as $a )
{
	if( $slot_rid != $a['resource_id'] )
	{
		continue;
	}

	if( $this->skip_id && in_array($a['id'], $this->skip_id) )
	{
		continue;
	}

	$app_starts = $a['starts_at'];

	if( $app_starts >= $dayEnd )
		break;

	if( $app_starts < $dayStart )
		continue;

	$app_ends = $a['starts_at'] + $a['duration'];
	if( isset($a['lead_out']) )
		$app_ends += $a['lead_out'];

	$removeThisSlot = TRUE;
	if( ($slot_starts >= $app_ends) && ( $slot_starts <= ($app_ends + $gap_after) ) )
	{
		$removeThisSlot = FALSE;
		break;
	}
	if( ($slot_ends <= $app_starts) && ( $slot_ends >= ($app_starts - $gap_before) ) )
	{
		$removeThisSlot = FALSE;
		break;
	}
}

if( $removeThisSlot )
{
	/* OK REMOVE */
	$return_seats = array();

	$text = M('Clustered Appointments Only');
	$error = array(
		'time' => $text
		);
	$this->throwSlotError( $error );
}
?>