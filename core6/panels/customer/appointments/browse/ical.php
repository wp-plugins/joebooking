<?php
include_once( NTS_APP_DIR . '/helpers/ical.php' );

$ntsCal = new ntsIcal();
foreach( $NTS_VIEW['entries'] as $a ){
	$ntsCal->addAppointment( $a );
	}
$str = $ntsCal->printOut();
echo $str;
//echo _print_r( $str );		   
?>