<?php
$skipVersionCheck = false;
$skipPanels = array('admin/conf/upgrade', 'anon/login', 'admin/conf/backup' );

reset( $skipPanels );
foreach( $skipPanels as $sp )
{
	if( substr($_NTS['REQUESTED_PANEL'], 0, strlen($sp)) == $sp )
	{
		$skipVersionCheck = true;
		break;
	}
}

$conf =& ntsConf::getInstance();

$appInfo = ntsLib::getAppInfo();
$currentVersion = ntsLib::parseVersion( $appInfo['current_version'] );

if( ! $skipVersionCheck )
{
	$fileVersion = ntsLib::getAppVersion();
	$fileVersion = ntsLib::parseVersion( $fileVersion );
	if( $fileVersion > $currentVersion )
	{
		ntsLib::migrate();
	}
}
?>