<?php
$saveOn = array();
$cal = $_NTS['REQ']->getParam('cal');
if( $cal )
{
	$saveOn['cal'] = $cal;
}
ntsView::setPersistentParams( $saveOn, 'admin/manage/schedules' );
?>