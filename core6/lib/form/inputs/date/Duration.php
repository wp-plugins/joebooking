<?php
$durationOptions = array(
	array('1d', '1 Day'),
	array('3d', '3 Days'),
	array('1w', '1 Week'),
	array('2w', '2 Weeks'),
	array('1m', '1 Month'),
	array('2m', '2 Months'),
	array('3m', '3 Months'),
	array('4m', '4 Months'),
	array('6m', '6 Months'),
	array('1y', '1 Year'),
	array('2y', '2 Years'),
	array('-1', 'Lifetime'),
	);

reset( $durationOptions );
foreach( $durationOptions as $do ){
	$conf['options'][] = array( $do[0], $do[1] );
	}
require( dirname(__FILE__) . '/../select.php' );
?>