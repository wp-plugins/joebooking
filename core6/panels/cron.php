<?php
global $_NTS;
if( 
	isset($_NTS['CURRENT_PANEL']) && 
	( substr($_NTS['CURRENT_PANEL'], 0, strlen('admin/conf')) == 'admin/conf' ) 
	)
{
	return;
}

$conf =& ntsConf::getInstance();
$now = time();
$cronLastRun = $conf->get( 'cronLastRun' );
if( 
	(! $cronLastRun) OR
	( ($now - $cronLastRun) > 60 * 60 )
	)
{
	$cm =& ntsCommandManager::getInstance();
	$cm->act_as = -1; // system
	$cronDir = dirname(__FILE__) . '/../cron';
	require( $cronDir . '/reminder.php' );
	require( $cronDir . '/auto-complete.php' );
	require( $cronDir . '/auto-reject2.php' );

	$cm =& ntsCommandManager::getInstance();
	$cm->act_as = 0; // back to user

	$conf->set( 'cronLastRun', $now );
}
?>