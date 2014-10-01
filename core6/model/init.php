<?php
if( substr(str_replace('.', '', PHP_VERSION), 0, 2) < 52 ){
	echo "This software requires PHP version 5.2 at least, yours is " . PHP_VERSION;
	exit;
	}

ini_set( 'track_errors', 'On' );

define( 'NTS_APP_DIR', realpath(dirname(__FILE__) . '/../')  );
if( ! defined('NTS_RUN_DIR') )
	define( 'NTS_RUN_DIR', realpath(dirname(__FILE__) . '/../../')  );

if( ! defined('NTS_EXTENSIONS_DIR') )
	define( 'NTS_EXTENSIONS_DIR', realpath(dirname(__FILE__) . '/../../extensions')  );

define( 'NTS_LIB_DIR', NTS_APP_DIR );
include_once( NTS_LIB_DIR . '/lib/ntsLib.php' );

define( 'NTS_PARAM_VIEW_MODE', 'nts-view-mode' );

if( ntsLib::isAjax() )
{
	// suppressing errors on ajax
	ini_set( 'display_errors', 'Off' );
}

global $NTS_EXECUTION_START;
$NTS_EXECUTION_START = ntsLib::utime();

/* database */
if( 
	( 
	! (
		defined('NTS_DB_HOST') && 
		defined('NTS_DB_USER') && 
		defined('NTS_DB_PASS') && 
		defined('NTS_DB_NAME')
		)
	)
	&&
	( ! (isset($GLOBALS['NTS_IS_PLUGIN']) && ($GLOBALS['NTS_IS_PLUGIN'] == 'wordpress')) )
	&&
	( file_exists(NTS_APP_DIR . '/../db.php') )
	)
{
	include_once( NTS_APP_DIR . '/../db.php' );
}
/*
else
{
	echo "<p><b>db.php</b> file doesn't exist! Please rename the sample <b>db.rename_it.php</b> to <b>db.php</b>, then edit your MySQL database information there.";
	exit;
}
*/

if( defined('NTS_DEVELOPMENT') )
	$hclib_path = NTS_DEVELOPMENT . '/hclib';
else
	$hclib_path = dirname(__FILE__) . '/../happ/hclib';
include_once( $hclib_path . '/_bootstrap.php' );

include_once( dirname(__FILE__) . '/../app/constants.php' );

/* load base code files */
include_once( NTS_LIB_DIR . '/lib/ntsRequest.php' );
include_once( NTS_LIB_DIR . '/lib/ntsMysqlWrapper.php' );
include_once( NTS_LIB_DIR . '/lib/ntsView.php' );
include_once( NTS_LIB_DIR . '/lib/ntsObject.php' );
include_once( NTS_LIB_DIR . '/lib/ntsUser.php' );
include_once( NTS_LIB_DIR . '/lib/ntsCommandManager.php' );
include_once( NTS_LIB_DIR . '/lib/ntsPaymentManager.php' );
include_once( NTS_LIB_DIR . '/lib/ntsEmailTemplateManager.php' );
include_once( NTS_LIB_DIR . '/lib/ntsConf.php' );

include_once( NTS_LIB_DIR . '/lib/form/ntsForm.php' );
include_once( NTS_LIB_DIR . '/lib/form/ntsValidator.php' );

include_once( NTS_LIB_DIR . '/lib/ntsAccountingManager.php' );
include_once( NTS_LIB_DIR . '/lib/ntsObserverManager.php' );

include_once( NTS_APP_DIR . '/version.php' );

/* define param names */
define( 'NTS_PARAM_ACTION', 'nts-action' );
define( 'NTS_PARAM_PANEL', 'nts-panel' );
define( 'NTS_PARAM_RETURN', 'nts-return' );
define( 'NTS_PARAM_VIEW_RICH', 'nts-view-rich' );

$ntsdb =& dbWrapper::getInstance();
$conf =& ntsConf::getInstance();
$GLOBALS['NTS_CONF'] = $conf;

/* some essential configs */
/* if registration enabled */
$enableRegistration = $conf->get('enableRegistration');
define( 'NTS_ENABLE_REGISTRATION', $enableRegistration );

$timeUnit = $conf->get('timeUnit');
define( 'NTS_TIME_UNIT', $timeUnit );
$timeStarts = $conf->get('timeStarts');
define( 'NTS_TIME_STARTS', $timeStarts );
$timeEnds = $conf->get('timeEnds');
define( 'NTS_TIME_ENDS', $timeEnds );


/*
1, 'Allow To Set Own Timezone'
0, 'Only View The Timezone'
-1, 'Do Not Show The Timezone'
*/
$enableTimezones = $conf->get('enableTimezones');
define( 'NTS_ENABLE_TIMEZONES', $enableTimezones );

$allowNoEmail = $conf->get('allowNoEmail');
define( 'NTS_ALLOW_NO_EMAIL', $allowNoEmail );

define( 'NTS_TIME_FORMAT',		$conf->get('timeFormat') );
define( 'NTS_DATE_FORMAT', 		$conf->get('dateFormat') );
define( 'NTS_COMPANY_TIMEZONE', $conf->get('companyTimezone') );
date_default_timezone_set( NTS_COMPANY_TIMEZONE );

/* if email as username */
$ri = ntsLib::remoteIntegration();
$emailAsUsername = $ri ? 0 : $conf->get('emailAsUsername');
define( 'NTS_EMAIL_AS_USERNAME', $emailAsUsername );

/* if duplicate emails allowed */
$allowDuplicateEmails = $ri ? 0 : $conf->get('allowDuplicateEmails');
define( 'NTS_ALLOW_DUPLICATE_EMAILS', $allowDuplicateEmails );

include_once( NTS_APP_DIR . '/model/objectMapper.php' );
include_once( NTS_LIB_DIR . '/lib/datetime/ntsTime.php' );
include_once( NTS_APP_DIR . '/helpers/timeManager2.php' );

$appInfo = ntsLib::getAppInfo();
if( ! $appInfo['installed_version'] )
{
	return;
}

/* check how many locations do we have */
$locations = ntsObjectFactory::getAllIds( 'location' );
if( count( $locations ) == 1 ){
	define( 'NTS_SINGLE_LOCATION', $locations[0] );
	}
else {
	define( 'NTS_SINGLE_LOCATION', 0 );
	}

/* check how many resources do we have */
$resources = ntsObjectFactory::getAllIds( 'resource' );
if( count( $resources ) == 1 ){
	define( 'NTS_SINGLE_RESOURCE', $resources[0] );
	}
else {
	define( 'NTS_SINGLE_RESOURCE', 0 );
	}

/* run menu init */
$menuConfFile = NTS_APP_DIR . '/panels/menu_conf.php';
require( $menuConfFile );

/* run mods init scripts */
$plm =& ntsPluginManager::getInstance();
$activePlugins = $plm->getActivePlugins();
reset( $activePlugins );
foreach( $activePlugins as $plg )
{
	$plgInitFile = $plm->getPluginFolder( $plg ) . '/init.php';
	if( file_exists($plgInitFile) )
		require( $plgInitFile );
}

/* init folders */
global $NTS_CORE_DIRS, $NTS_FILE_LOOKUP_CACHE;
$NTS_CORE_DIRS = array();
$NTS_FILE_LOOKUP_CACHE = array();
/* plugins */
reset( $activePlugins );
foreach( $activePlugins as $plg ){
	$NTS_CORE_DIRS[] = $plm->getPluginFolder( $plg );
	}
/* normal */
$NTS_CORE_DIRS[] = NTS_APP_DIR;
/* base dir */
if( NTS_APP_DIR != NTS_APP_DIR )
	$NTS_CORE_DIRS[] = NTS_APP_DIR;
?>