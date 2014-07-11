<?php
$skipVersionCheck = false;
$skipPanels = array('admin/conf/upgrade', 'anon/login', 'admin/conf/backup' );

reset( $skipPanels );
foreach( $skipPanels as $sp ){
	if( substr($_NTS['REQUESTED_PANEL'], 0, strlen($sp)) == $sp ){
		$skipVersionCheck = true;
		break;
		}
	}

$conf =& ntsConf::getInstance();

$appInfo = ntsLib::getAppInfo();
$currentVersion = ntsLib::parseVersion( $appInfo['current_version'] );

if( ! $skipVersionCheck ){
	$fileVersion = ntsLib::getAppVersion();
	$fileVersion = ntsLib::parseVersion( $fileVersion );

	if( $fileVersion > $currentVersion ){
		/* check if there are upgrade files to run */
		$runFiles = array();
		$upgradeDir = NTS_APP_DIR . '/upgrade';
		$upgradeFiles = ntsLib::listFiles( $upgradeDir, '.php' );
		foreach( $upgradeFiles as $uf ){
			$ver = substr( $uf, strlen('upgrade-'), 4 );
			if( $ver > $currentVersion ){
				$runFiles[] = $uf;
				}
			}

		// upgrade scripts run required 
		if( $runFiles ){
			ntsView::setAnnounce( M('New Version Files Uploaded, Upgrade Procedure Required'), 'ok' );

			/* redirect to upgrade screeen */
			$forwardTo = ntsLink::makeLink( 'admin/conf/upgrade' );
			ntsView::redirect( $forwardTo );
			exit;
			}
	// just update the installed version in the database
		else {
			$conf->set('currentVersion', ntsLib::getAppVersion() );
			}
		}
	}
?>