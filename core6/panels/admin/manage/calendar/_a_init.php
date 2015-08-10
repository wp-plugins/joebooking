<?php
$t = $NTS_VIEW['t'];

$locs = ntsLib::getVar( 'admin::locs' );
$ress = ntsLib::getVar( 'admin::ress' );
$sers = ntsLib::getVar( 'admin::sers' );

$locs2 = ntsLib::getVar( 'admin::locs2' );
$ress2 = ntsLib::getVar( 'admin::ress2' );
$sers2 = ntsLib::getVar( 'admin::sers2' );

$filter = ntsLib::getVar( 'admin/manage:filter' );

$ci = ntsLib::getCurrentUser();

$range = $_NTS['REQ']->getParam('range');
if( ! $range ){
	$range = $ci->getPreference( 'calendar_range' );
	if( ! $range ){
		$range = 'week'; // 'week' or 'month'
	}
}

/* save range in preferences */
$ci->setPreference( 'calendar_range', $range );

$display = $_NTS['REQ']->getParam('display');
if( ! $display )
	$display = 'calendar';

$date = $_NTS['REQ']->getParam('start');
$end_date = $_NTS['REQ']->getParam('end');

if( $date )
{
	$t->setDateDb( $date );
}
else
{
	$t->setNow();
}
$start_date = $t->formatDate_Db();

if( $display == 'calendar' )
{
	switch( $range )
	{
		case 'week':
			$t->setStartWeek();
			$start_date = $t->formatDate_Db();
			$t->setEndWeek();
			$end_date = $t->formatDate_Db();
			break;

		case 'month':
			$t->setStartMonth();
			$start_date = $t->formatDate_Db();
			$t->setEndMonth();
			$end_date = $t->formatDate_Db();
			break;
	}
}

if( $end_date < $start_date )
	$end_date = $start_date;

$saveOn = array(
	'start'		=> $start_date,
	'end'		=> $end_date,
	'display'	=> $display,
	'range'		=> $range
	);
ntsView::setPersistentParams( $saveOn, 'admin/manage/calendar' );
?>