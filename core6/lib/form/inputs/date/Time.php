<?php
$myTimeUnit = defined('NTS_TIME_UNIT') ? NTS_TIME_UNIT : 15;

$t = new ntsTime;
$t->setDateDb( 20120416 );

$startsAt = isset($conf['conf']['min']) ? $conf['conf']['min'] : 0;
$endsAt = isset($conf['conf']['max']) ? $conf['conf']['max'] : 24 * 60 * 60;

$conf['options'] = array();
if( $startsAt > 0 )
{
	$t->modify( '+' . $startsAt . ' seconds' );
}
for( $i = 0; $i <= ($endsAt - $startsAt)/(60*$myTimeUnit); $i++ )
{
	$this_ts = $i * $myTimeUnit * 60;
	$timeView = $t->formatTime();
	if( $this_ts > 24 * 60 * 60 ) // next day after midnight
	{
		$timeView = ' -> ' . $timeView;
	}
	$conf['options'][] = array( $startsAt + $this_ts, $timeView );
	$t->modify( '+' . $myTimeUnit . ' minutes' );
}

require( NTS_LIB_DIR . '/lib/form/inputs/select.php' );
?>