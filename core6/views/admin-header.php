<?php
$now = time();
$check = $now - 1 * 60 * 60;
$conf =& ntsConf::getInstance();

$installedVersion = $conf->get('currentVersion');
$installedVersionNumber = ntsLib::parseVersion( $installedVersion );

$checkUrl2 = ntsLib::checkLicenseUrl();

$checkLicense = 0;
$homeCall = 0;
if( (! isset($_SESSION['home_call'])) || $_SESSION['home_call'] )
{
	if( $NTS_CURRENT_USER->hasRole('admin') && (! $NTS_CURRENT_USER->isPanelDisabled('admin/conf/upgrade')) )
	{
		$checkLicense = 1;
		$homeCall = 1;
		if( defined('NTS_APP_DEVELOPER') && (! defined('NTS_DEVELOPMENT')) )
		{
			$checkLicense = 0;
		}
		elseif( defined('NTS_APP_LEVEL') && (NTS_APP_LEVEL == 'lite') )
		{
			$checkLicense = 0;
			$homeCall = 0;
		}
	}
	else
	{
		$checkLicense = 0;
	}

	$ri = ntsLib::remoteIntegration();
	if( $ri && ($ri == 'wordpress') )
	{
		$checkLicense = 0;
	}

	$_SESSION['home_call'] = 0;
}

$skipPanels = array('admin/conf/upgrade');
reset( $skipPanels );
foreach( $skipPanels as $sp )
{
	if( substr($_NTS['CURRENT_PANEL'], 0, strlen($sp)) == $sp )
	{
		$checkLicense = false;
		break;
	}
}
$licenseLink = ntsLink::makeLink( 'admin/conf/upgrade' );

if( ($checkLicense OR $homeCall) && file_exists(dirname(__FILE__) . '/admin-header-license.php') )
{
	require( dirname(__FILE__) . '/admin-header-license.php' );
}
?>