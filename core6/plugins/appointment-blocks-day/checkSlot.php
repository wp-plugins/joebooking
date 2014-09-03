<?php
if( ! $slot_rid )
	return;
$plugin = 'appointment-blocks-day';

$this->companyT->setTimestamp( $ts );
$dayStart = $this->companyT->getStartDay();

if( ! isset($this->plugins_data[$plugin]) )
	$this->plugins_data[$plugin] = array();

if( ! isset($this->plugins_data[$plugin][$dayStart][$slot_rid]) )
{
	$dayEnd = $this->companyT->getEndDay();
	// ok now count appointments
	$where = array(
		'(starts_at+duration+lead_out)'	=> array( '>=', $dayStart ),
		'(starts_at-lead_in)'			=> array( '<', $dayEnd ),
		'completed'						=> array( 'IN', array(0, HA_STATUS_CANCELLED, HA_STATUS_NOSHOW) ),
		'resource_id'					=> array( '=', $slot_rid )
		);
	$apps = $this->getAppointments( $where, 'ORDER BY starts_at ASC' );
	$this->plugins_data[$plugin][$dayStart][$slot_rid] = $apps;
}

$count = count( $this->plugins_data[$plugin][$dayStart][$slot_rid] );

if( $count > 0 )
{
	$removeSeats = $slot_seats;
	$text = M('Appointment Blocks Day');
	$this->throwSlotError( array('time' => $text) );
}
?>