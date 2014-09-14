<?php
$ntsConf =& ntsConf::getInstance();
$weekStartsOn = $ntsConf->get('weekStartsOn');

$conf['options'] = array();
for( $i = 0; $i < 7; $i++ )
{
	$di = $weekStartsOn + $i;
	$di = $di % 7;

	$timeView = ntsTime::weekdayLabelShort( $di );
	$readonly = ( isset($conf['freeze']) && in_array($di, $conf['freeze']) ) ? true : false;
	$conf['options'][] = array( $di, $timeView, $readonly );
}
require( NTS_LIB_DIR . '/lib/form/inputs/checkboxSet.php' );
?>