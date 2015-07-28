<?php
/* check if our customer has any limit */
$customer_id = $this->customerId;
if( ! $customer_id )
{
	return;
}

$plugin = 'limits';
$plm =& ntsPluginManager::getInstance();
$settings = $plm->getPluginSettings( $plugin );

$max = $settings['max'];
$per = $settings['per'];

if( ! $max )
	return;

list( $perQty, $perMeasure ) = explode( ' ', $per );

/* which periods to check */
$restrictPeriods = array();

$customerT = clone $this->customerT;

$customerT->setTimestamp( $ts );
switch( $perMeasure )
{
	case 'days':
		$customerT->setStartDay();
		break;
	case 'weeks':
		$customerT->setStartWeek();
		break;
	case 'months':
		$customerT->setStartMonth();
		break;
	case 'years':
		$customerT->setStartYear();
		break;
}

$modify = ( $perQty > 1 ) ? ($perQty - 1) . ' ' . $perMeasure : '';
if( $modify )
{
	$customerT->modify( '-' . $modify );
}
$restrictFrom = $customerT->getTimestamp();
$modify2 = '+' . $perQty . ' ' . $perMeasure;
$customerT->modify( $modify2 );
$restrictTo = $customerT->getTimestamp();

if( ! isset($this->plugins_data[$plugin]) )
	$this->plugins_data[$plugin] = array();

$count = 0;
if( ! isset($this->plugins_data[$plugin][$restrictFrom]) )
{
	// ok now count appointments
	$real_skip = array();
	foreach( $this->skip_id as $skip_id )
	{
		if( $skip_id > 0 )
			$real_skip[] = $skip_id;
	}

	$where = array(
		'(starts_at+duration+lead_out)'	=> array( '>=', $restrictFrom ),
		'(starts_at-lead_in)'			=> array( '<', $restrictTo ),
		'customer_id'					=> array( '=', $customer_id ),
		'completed'						=> array( 'IN', array(0, HA_STATUS_CANCELLED, HA_STATUS_NOSHOW) ),
		);
	if( $real_skip )
	{
		$where['id'] = array('NOT IN', array($real_skip) );
	}

	$count = $this->countAppointments( $where );

	/* has virtual apps? */
	global $NTS_VIRTUAL_APPOINTMENTS;
	foreach( $NTS_VIRTUAL_APPOINTMENTS as $va )
	{
		if( in_array($va['id'], $this->skip_id) )
			continue;
		if( $va['customer_id'] != $customer_id )
			continue;
		if( ($va['starts_at']-$va['lead_in']) >= $restrictTo )
			continue;
		if( ($va['starts_at']+$va['duration']+$va['lead_out']) < $restrictFrom )
			continue;
		$count++;
	}
	$this->plugins_data[$plugin][$restrictFrom] = $count;
}
$count = $this->plugins_data[$plugin][$restrictFrom];

if( $count >= $max )
{
	/* OK REMOVE */
	$return_seats = array();

	$text = M('Customer Limit') . ': ' . $max . ' ';
	$text .= ($max > 1) ? M('Appointments') : M('Appointment');
	$text .= ' / ' . $per;
	$error = array(
		'customer' => $text
		);
	$this->throwSlotError( $error );

	$this->addGlobalLimit(
		'time',
		array($restrictFrom,$restrictTo),
		$error
		);
}
?>