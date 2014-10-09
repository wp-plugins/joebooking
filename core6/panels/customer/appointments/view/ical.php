<?php
include_once( NTS_APP_DIR . '/helpers/ical.php' );

$ntsCal = new ntsIcal();
foreach( $objects as $a )
{
	// skip if other status
	$completedStatus = $a->getProp('completed');
//	if( in_array($completedStatus, array(HA_STATUS_CANCELLED, HA_STATUS_NOSHOW) ) )
	if( $completedStatus )
	{
		continue;
	}

	$approvedStatus = $a->getProp('approved');
	if( $approvedStatus != HA_STATUS_APPROVED )
	{
		continue;
	}

	$ntsCal->addAppointment( $a );
}
$str = $ntsCal->printOut();
echo $str;
//echo _print_r( $str );
?>