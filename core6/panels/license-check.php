<?php
$skipCheck = 0;
$skipPanels = array('customer', 'admin/conf/upgrade', 'user', 'anon', 'admin/conf/backup' );

reset( $skipPanels );
foreach( $skipPanels as $sp )
{
	if( substr($_NTS['REQUESTED_PANEL'], 0, strlen($sp)) == $sp )
	{
		$skipCheck = 1;
		break;
	}
	if( 0 && ($_SERVER['SERVER_NAME'] == 'localhost') )
	{
		$skipCheck = 1;
	}
	if( defined('NTS_APP_DEVELOPER') && (! defined('NTS_DEVELOPMENT')) )
	{
		$skipCheck = 1;
	}
}

$ri = ntsLib::remoteIntegration();
if( $ri && ($ri == 'wordpress') )
{
	$skipCheck = 1;
}

$currentLicense = $conf->get('licenseCode');
if( (! $skipCheck) && (! $currentLicense) && ( ! in_array(NTS_APP_LEVEL, array('lite')) ) )
{
	ntsView::setAnnounce( M('Please Enter Your License Code'), 'ok' );

	/* redirect to license screeen */
	$forwardTo = ntsLink::makeLink( 'admin/conf/upgrade' );
	ntsView::redirect( $forwardTo );
	exit;
}
?>