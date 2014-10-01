<?php
$ff =& ntsFormFactory::getInstance();
$conf =& ntsConf::getInstance();
$currentLicense = $conf->get('licenseCode');
$installationId = $conf->get( 'installationId' );

$showLicenseForm = TRUE;
if( in_array(NTS_APP_LEVEL, array('lite')) OR (defined('NTS_APP_DEVELOPER') && NTS_APP_DEVELOPER) )
{
	$showLicenseForm = FALSE;
}
if( defined('NTS_DEVELOPMENT') )
{
	$showLicenseForm = TRUE;
}

$ri = ntsLib::remoteIntegration();
if( $ri && ($ri == 'wordpress') )
{
	$showLicenseForm = FALSE;
}

$NTS_VIEW['form'] = NULL;
$formFile = dirname( __FILE__ ) . '/form';
if( $showLicenseForm && file_exists($formFile . '.php') )
{
	$formParams = array(
		'licenseCode' => $currentLicense,
		);
	$NTS_VIEW['form'] =& $ff->makeForm( $formFile, $formParams );
}

switch( $action ){
	case 'update':
		if( $NTS_VIEW['form']->validate() ){
			$formValues = $NTS_VIEW['form']->getValues();
			$enteredLicense = $formValues['licenseCode'];
			$conf->set( 'licenseCode', $enteredLicense );

		/* continue to the list with anouncement */
			$forwardTo = ntsLink::makeLink( '-current-' );
			ntsView::redirect( $forwardTo );
			exit;
			}
		else {
		/* form not valid, continue to create form */
			}

		break;

	case 'upgrade':
		$appInfo = ntsLib::getAppInfo();
		$dgtCurrentVersion = ntsLib::parseVersion( $appInfo['current_version']  );
		$dgtFileVersion = ntsLib::parseVersion( ntsLib::getAppVersion() );

		if( $dgtFileVersion > $dgtCurrentVersion ){
		/* get upgrade script files */
			$runFiles = array();
			$upgradeDir = NTS_APP_DIR . '/upgrade';
			$upgradeFiles = ntsLib::listFiles( $upgradeDir, '.php' );
			foreach( $upgradeFiles as $uf ){
				$ver = substr( $uf, strlen('upgrade-'), 4 );
				if( $ver > $dgtCurrentVersion ){
					$runFiles[] = $uf;
					}
				}
		/* run upgrade files */
			foreach( $runFiles as $rf ){
				require( $upgradeDir . '/' . $rf );
				}

			$newVersion = ntsLib::getAppVersion();
			$conf->set('currentVersion', $newVersion );

			$NTS_VIEW['newVersion'] = $newVersion;
			$NTS_VIEW['display'] = dirname(__FILE__) . '/upgraded.php';
			$NTS_VIEW['runFiles'] = $runFiles;
			break;
			}

	default:
		break;
	}
?>