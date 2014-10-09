<?php
$currentVersion = $conf->get('currentVersion');
if( ! $currentVersion ){
	$setupFile = NTS_APP_DIR . '/setup/setup.php';
	if( ! file_exists($setupFile) )
		$setupFile = NTS_APP_DIR . '/setup/setup.php';
	require( $setupFile );
	exit;
	}

$installationId = $conf->get( 'installationId' );
if( ! $installationId ){
	$installationId = md5(rand());
	$conf->set( 'installationId', $installationId );
	}

if( ! NTS_APP_WHITELABEL )
	$_NTS['DOWNLOAD_URL'] = 'http://www.hitappoint.com/upgrade/';
else
	$_NTS['DOWNLOAD_URL'] = '';

if( defined('NTS_DEVELOPMENT') )
{
	$_NTS['CHECK_LICENSE_URL'] = 'http://localhost/hitcode/customers/lic.php';
}
else
{
	$_NTS['CHECK_LICENSE_URL'] = 'http://www.hitcode.com/customers/lic.php';
}

$requested_panel = ( isset($_REQUEST[NTS_PARAM_PANEL]) ) ? $_REQUEST[NTS_PARAM_PANEL] : '';
$requested_panel = str_replace( '%252F', '/', $requested_panel );
$requested_panel = str_replace( '%2F', '/', $requested_panel );
$_NTS['REQUESTED_PANEL'] = $requested_panel;
$_NTS['WAS_REQUESTED_PANEL'] = $_NTS['REQUESTED_PANEL'];

if( isset($GLOBALS['NTS_CONFIG'][$app]['DEFAULT_PARAMS']) )
{
	reset( $GLOBALS['NTS_CONFIG'][$app]['DEFAULT_PARAMS'] );
	foreach( $GLOBALS['NTS_CONFIG'][$app]['DEFAULT_PARAMS'] as $k => $v )
	{
		switch( $k )
		{
			case 'fix_resource':
				$GLOBALS['NTS_FIX_RESOURCE'] = ntsLib::parseCommaSeparated( $v );
				break;

			case 'fix_service':
				$GLOBALS['NTS_FIX_SERVICE'] = ntsLib::parseCommaSeparated( $v );
				break;

			case 'fix_location':
				$GLOBALS['NTS_FIX_LOCATION'] = ntsLib::parseCommaSeparated( $v );
				break;
		}
	}
}




if( $_NTS['REQUESTED_PANEL'] == 'system/attach' )
{
	if( ob_get_length() )
	{
		ob_end_clean();
	}
	require( dirname(__FILE__) . '/attach.php' );
	exit;
}

global $NTS_PERSISTENT_PARAMS;
if( isset($_REQUEST['nts-theme']) )
{
	$theme = $_REQUEST['nts-theme'];
	$NTS_PERSISTENT_PARAMS[ '/' ][ 'nts-theme' ] = $theme;
}

if( isset($_REQUEST[NTS_PARAM_VIEW_RICH]) )
	$NTS_PERSISTENT_PARAMS[ '/' ][ NTS_PARAM_VIEW_RICH ] = $_REQUEST[NTS_PARAM_VIEW_RICH];

/* IF PULL JAVASCRIPT OR CSS */
if( $_NTS['REQUESTED_PANEL'] == 'system/pull' )
{
	if( ob_get_length() ){
		ob_end_clean();
		}
	require( dirname(__FILE__) . '/pull.php' );
	exit;
}

$thisPage = ntsLib::pureUrl( ntsLib::currentPageUrl() );
$app = ntsLib::getAppProduct();
if( ! isset($GLOBALS['NTS_CONFIG'][$app]['BASE_URL']) )
{
	$GLOBALS['NTS_CONFIG'][$app]['BASE_URL'] = $thisPage;
	$GLOBALS['NTS_CONFIG'][$app]['INDEX_PAGE'] = '';
}

if( ! isset($GLOBALS['NTS_CONFIG'][$app]['FRONTEND_WEBPAGE']) )
{
	$GLOBALS['NTS_CONFIG'][$app]['FRONTEND_WEBPAGE'] = $thisPage;
}

/* session start */
if( ! defined('NTS_SESSION_NAME') ){
	define( 'NTS_SESSION_NAME', 'ntssess_' . $installationId );
	}
if( ! isset($_SESSION) )
{
	session_name( NTS_SESSION_NAME );
	session_start();
}

/* run other file */
if( isset($_GET['nts-run']) ){
	$rootDir = realpath(NTS_APP_DIR . '/../');
	$file = $rootDir . '/' . $_GET['nts-run'] . '.php';
	if( file_exists($file) )
		require( $file );
	exit;
	}

/* reminder code */
if( isset($_GET['nts-reminder']) || isset($_GET['nts-cron']) ){
	require( dirname(__FILE__) . '/cron.php' );
	exit;
	}

/* sos code */
if( isset($_GET['nts-send-sos']) )
{
	require( dirname(__FILE__) . '/send-sos.php' );
	exit;
}
if( isset($_GET['nts-sos']) )
{
	$ntsSos = $_GET['nts-sos'];
	$sosSetting =  $conf->get( 'sosCode' );
	list( $sosCode, $sosCreated ) = explode( ':', $sosSetting );

	$now = time();
	if( $ntsSos == $sosCode  )
	{
		if( $now <= ($sosCreated + 24 * 60 * 60) )
		{
			ntsView::setAnnounce( 'SOS code ok', 'ok' );
			$_SESSION['nts_sos_user_id'] = -111;
		}
		else
		{
			ntsView::setAnnounce( 'SOS code expired', 'error' );
			if( isset($_SESSION['nts_sos_user_id']) )
				unset($_SESSION['nts_sos_user_id']);
		}
	}
	else
	{
		ntsView::setAnnounce( 'SOS code incorrect', 'error' );
		if( isset($_SESSION['nts_sos_user_id']) )
			unset($_SESSION['nts_sos_user_id']);
	}
}

/* request */
$_NTS['REQ'] = new ntsRequest;

/* sanitize */
$_NTS['REQ']->addSanitizer( 'service', '/^[\d-]*$/' );
$_NTS['REQ']->addSanitizer( 'resource', '/^[\d-a]*$/' );
$_NTS['REQ']->addSanitizer( 'time', '/^[\d-]*$/' );
$_NTS['REQ']->addSanitizer( 'key', '/^[a-zA-Z\d_-]*$/' );

/* now check current user id and type */
$uif =& ntsUserIntegratorFactory::getInstance();
$integrator =& $uif->getIntegrator();

if( ! ntsLib::getCurrentUserId() )
{
	if( isset($_SESSION['nts_sos_user_id']) )
	{
		ini_set( 'display_errors', 'On' );
//		error_reporting( E_ALL );
		$currentUserId = $_SESSION['nts_sos_user_id'];
	}
	else
	{
		$currentUserId = $integrator->currentUserId();
	}
	ntsLib::setCurrentUserId( $currentUserId );
}

$ri = ntsLib::remoteIntegration();
global $NTS_CURRENT_USER;
if( ! ( isset($NTS_CURRENT_USER) && $NTS_CURRENT_USER ) )
{
	$current_user_id = ntsLib::getCurrentUserId();
	$NTS_CURRENT_USER = new ntsUser();
	$NTS_CURRENT_USER->setId( $current_user_id );

	if( $ri == 'wordpress' )
	{
		$cm =& ntsCommandManager::getInstance();

		/* if it's admin and is not admin in our app then save it */
		$wp_user_data = get_userdata( $current_user_id );
		if( 
			isset($wp_user_data->roles) &&
			in_array('administrator', $wp_user_data->roles)
			)
		{
			if( ! $NTS_CURRENT_USER->hasRole('admin') )
			{
				$my_roles = $NTS_CURRENT_USER->getProp( '_role' );
				$my_roles[] = 'admin';
				$NTS_CURRENT_USER->setProp( '_role', $my_roles );

			/* save */
				$cm->runCommand( $NTS_CURRENT_USER, 'update' );
			}

		/* if it is solo then set permissions for all resources */
			$resourceSchedules = array();
			$resourceApps = array();
			$res_ids = ntsObjectFactory::getAllIds( 'resource' );
			reset( $res_ids );
			foreach( $res_ids as $rid )
			{
				$resourceSchedules[ $rid ]['view'] = 1;
				$resourceSchedules[ $rid ]['edit'] = 1;

				$resourceApps[ $rid ]['view'] = 1;
				$resourceApps[ $rid ]['edit'] = 1;
				$resourceApps[ $rid ]['notified'] = 1;
			}

		/* update user */
			$NTS_CURRENT_USER->setAppointmentPermissions( $resourceApps );
			$NTS_CURRENT_USER->setSchedulePermissions( $resourceSchedules );

			$cm->runCommand( $NTS_CURRENT_USER, 'update' );
		}
	}
}

/* language manager */
$lm =& ntsLanguageManager::getInstance(); 
$lm->setLanguage( $NTS_CURRENT_USER->getLanguage() );
$languageConf = $lm->getLanguageConf( $NTS_CURRENT_USER->getLanguage() );
if( isset($languageConf['charset']) )
{
	if( ! headers_sent() )
		header( 'Content-Type: text/html; charset=' . $languageConf['charset'] );
}

/* default panel */
if( ! $_NTS['REQUESTED_PANEL'] )
{
	if( $ri )
	{
		if( 
			$NTS_CURRENT_USER->hasRole('admin') && 
			isset($GLOBALS['NTS_CONFIG'][$app]['ADMIN_PANEL']) &&
			$GLOBALS['NTS_CONFIG'][$app]['ADMIN_PANEL']
			)
		{
			$_NTS['REQUESTED_PANEL'] = 'admin';
		}
		else
		{
			$_NTS['REQUESTED_PANEL'] = 'customer';
		}

	}
	else
	{
		if( $NTS_CURRENT_USER->hasRole('admin') )
		{
			$_NTS['REQUESTED_PANEL'] = 'admin';
		}
		else
		{
			$_NTS['REQUESTED_PANEL'] = 'customer';
		}
	}
}
?>