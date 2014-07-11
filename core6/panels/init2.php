<?php
/* bootstrap */
require( dirname(__FILE__) . '/_init1.php' );

/* check current version */
require( dirname(__FILE__) . '/version-check.php' );

/* check current license */
require( dirname(__FILE__) . '/license-check.php' );

$_NTS['ROOT_INFO'] = array();

/* shortcuts */
switch( $_NTS['REQUESTED_PANEL'] )
{
	case 'admin':
		$_NTS['REQUESTED_PANEL'] .= '/manage';
		break;

	case 'customer':
		if( ntsLib::getCurrentUserId() )
		{
			global $NTS_CURRENT_USER;
			if( ! $NTS_CURRENT_USER->hasRole('admin') )
			{
				$_NTS['REQUESTED_PANEL'] .= '/appointments/view';
			}
			else
			{
				$_NTS['REQUESTED_PANEL'] .= '/book';
			}
		}
		else
		{
			$_NTS['REQUESTED_PANEL'] .= '/book';
		}
		break;
}

$requested_panel = $_NTS['REQUESTED_PANEL'];
$requested_action = $_NTS['REQ']->getRequestedAction();
$rootInfo = array();

$action_file = 'panels/' . $requested_panel . '/a.php';
if( $action_file = ntsLib::fileInCoreDirs($action_file))
{
	$_NTS['CURRENT_PANEL'] = $_NTS['REQUESTED_PANEL'];
	if( substr($requested_panel, 0, strlen('admin')) == 'admin' )
	{
		$root_file = NTS_APP_DIR . '/panels/admin/root.php';
		if( file_exists($root_file) )
		{
			require( $root_file );
			$rootPath = 'admin';
			$rootInfo = array(
				$root_file,
				'admin'
				);
		}
	}
	if( substr($requested_panel, 0, strlen('customer')) == 'customer' )
	{
		$root_file = NTS_APP_DIR . '/panels/customer/root.php';
		if( file_exists($root_file) )
		{
			require( $root_file );
			$rootPath = 'customer';
			$rootInfo = array(
				$root_file,
				'customer'
				);
		}
	}

	$_NTS['ROOT_INFO'] = $rootInfo;

//	echo 'RUN CONTROLLER';
	$controller = new ntsActionController( $requested_panel, $requested_action );
	$controller->run();

	$currentPanel = $_NTS['REQUESTED_PANEL'];
	require( dirname(__FILE__) . '/_init_menu.php' );

	/* pull housekeeping */
	require( dirname(__FILE__) . '/cron.php' );
	return;
}

/****************************/
/* ACTIONS					*/
/****************************/
$saveRequestedPanel = $_NTS['REQUESTED_PANEL'];
$requestedAction = $_NTS['REQ']->getRequestedAction();
$_NTS['REQUESTED_ACTION'] = $requestedAction;

$apm =& ntsAdminPermissionsManager::getInstance();
$allPanels = $apm->getPanels();

while( $_NTS['REQUESTED_PANEL'] ){
	/* GET CURRENT PANEL */
	$currentPanel = '';
	$requestedAction = $_NTS['REQUESTED_ACTION'];

	$checkPanels = array();

	/* folder exists? */
	if( ntsLib::fileInCoreDirs('panels/' . $_NTS['REQUESTED_PANEL']) ){
		$checkPanels[] = $_NTS['REQUESTED_PANEL'];
		}
	else {
		$parent = ntsLib::getParentPath( $_NTS['REQUESTED_PANEL'] );
		while( ! ntsLib::fileInCoreDirs('panels/' . $parent) ){
			$parent = ntsLib::getParentPath( $parent );
			}
		if( $parent ){
			$checkPanels[] = $parent;
			}
		}

	/* PRE-ACTION FILES */
	$preActionFiles = array();
	reset( $checkPanels );
	foreach( $checkPanels as $checkPanel ){
		$rootInfo = ntsLib::findClosestFile( $checkPanel, 'root.php' );
		if( $rootInfo && ( ! in_array($rootInfo[0], $preActionFiles)) ){
			$preActionFiles[] = $rootInfo[0];
			}
		$initInfo = ntsLib::findAllFiles( $checkPanel, 'init.php' );
		foreach( $initInfo as $fi ){
			if( ! in_array($fi[0], $preActionFiles) ){
				$preActionFiles[] = $fi[0];
				}
			}
		$filterInfo = ntsLib::findAllFiles( $checkPanel, 'filter.php' );
		foreach( $filterInfo as $fi ){
			if( ! in_array($fi[0], $preActionFiles) ){
				$preActionFiles[] = $fi[0];
				}
			}
		}

	$_NTS['ROOT_INFO'] = $rootInfo;

	/* before action nullify the requested panel */
	$_NTS['CURRENT_PANEL'] = $_NTS['REQUESTED_PANEL'];

	$_NTS['CURRENT_ACTION'] = $requestedAction;
	$action = $_NTS['CURRENT_ACTION'];

	reset( $preActionFiles );
	foreach( $preActionFiles as $preActionFile ){
		if( file_exists($preActionFile) ){
			require_once( $preActionFile );
			}
		}

/* redefine checkPanels */		
	$checkPanels = array();

	/* folder exists? */
	if( ntsLib::fileInCoreDirs('panels/' . $_NTS['REQUESTED_PANEL']) ){
		$checkPanels[] = $_NTS['REQUESTED_PANEL'];
		}
	else {
		$parent = ntsLib::getParentPath( $_NTS['REQUESTED_PANEL'] );
		while( ! ntsLib::fileInCoreDirs('panels/' . $parent) ){
			$parent = ntsLib::getParentPath( $parent );
			}
		if( $parent ){
			$checkPanels[] = $parent;
			}
		}

	$preActionFiles2 = array();
	/* alias? */
	if( $aliasInfo = ntsLib::findClosestFile($_NTS['REQUESTED_PANEL'], 'alias.php')){
		list( $aliasFile, $aliasPath ) = $aliasInfo;
		$NTS_VIEW['aliasFile'] = $aliasFile;
		require( $aliasFile );
		$checkPanel = $alias . substr($_NTS['REQUESTED_PANEL'], strlen($aliasPath));

		/* check if it is also alias */
		if( $aliasInfo2 = ntsLib::findClosestFile($checkPanel, 'alias.php')){
			list( $aliasFile2, $aliasPath2 ) = $aliasInfo2;
			require( $aliasFile2 );
			$checkPanel = $alias . substr($checkPanel, strlen($aliasPath2));
			}

		$checkPanels[] = $checkPanel;

		/* pre-action for alias */
		$rootInfo = ntsLib::findClosestFile( $checkPanel, 'root.php' );
		if( $rootInfo && ( ! in_array($rootInfo[0], $preActionFiles)) ){
			$preActionFiles2[] = $rootInfo[0];
			}
		$filterInfo = ntsLib::findAllFiles( $checkPanel, 'filter.php' );
		foreach( $filterInfo as $fi ){
			if( ! in_array($fi[0], $preActionFiles) ){
				$preActionFiles2[] = $fi[0];
				}
			}

		reset( $preActionFiles2 );
		foreach( $preActionFiles2 as $preActionFile ){
			if( file_exists($preActionFile) ){
				require_once( $preActionFile );
				}
			}
		}

	/* first try without expand */
	reset( $checkPanels );
	foreach( $checkPanels as $checkPanel ){
		/* action or index files exist */
		$checkFiles = array( 'action.php', 'index.php' );
		if( $requestedAction ){
			$checkFiles[] = 'action-' . $requestedAction . '.php';
			}

		$stayHere = false;
		reset( $checkFiles );
		foreach( $checkFiles as $cf ){
			if( ntsLib::fileInCoreDirs('panels/' . $checkPanel . '/' . $cf) ){
				break;
				}
			}
		$currentPanel = $_NTS['REQUESTED_PANEL'];
		break;
		}

	if( ! $currentPanel ){
		ntsView::redirect( ntsLib::getRootWebpage() );
		exit;
		}
	if( $NTS_CURRENT_USER->isPanelDisabled($currentPanel) ){
		ntsView::setAnnounce( M('Permission Denied'), 'error' );
		ntsView::redirect( ntsLib::getRootWebpage() );
		exit;
		}

	/* FIND PRE-ACTION FILES AFTER EXPAND */
	$preActionFiles3 = array();
	$preActionFiles4 = array();

	/* before action nullify the requested panel */
	$_NTS['REQUESTED_PANEL'] = '';
	$_NTS['CURRENT_PANEL'] = $currentPanel;

	$_NTS['CURRENT_ACTION'] = $requestedAction;
	$action = $_NTS['CURRENT_ACTION'];

	reset( $preActionFiles4 );
	foreach( $preActionFiles4 as $preActionFile ){
		if( file_exists($preActionFile) ){
			require_once( $preActionFile );
			}
		}

	/* FIND ACTION FILES */
	$actionFiles = array();
	reset( $checkPanels );
	$checkFiles = array();
	foreach( $checkPanels as $checkPanel ){
		if( $requestedAction )
			$checkFiles[] = $checkPanel . '/action-' . $requestedAction . '.php';
		$checkFiles[] = $checkPanel . '/action.php';
		}
	reset( $checkFiles );
	foreach( $checkFiles as $cf ){
		if( $actionFile = ntsLib::fileInCoreDirs('panels/' . $cf) ){
			$actionFiles[] = $actionFile;
			break;
			}
		}

	/* HANDLE ACTION */	
	reset( $actionFiles );
	foreach( $actionFiles as $actionFile ){
		if( file_exists($actionFile) ){
//			$controller = new ntsActionController( $actionFile, $action );
//			$controller->run();
			require( $actionFile );
			break;
			}
		}
	}

/****************************/
/* END OF ACTIONS			*/
/****************************/
/*
_print_r( $preActionFiles );
_print_r( $preActionFiles2 );
_print_r( $preActionFiles3 );
_print_r( $preActionFiles4 );
*/

/* FIND DISPLAY FILES */
$displayFiles = array();
reset( $checkPanels );
$checkFiles = array();
$customDisplay = $_NTS['REQ']->getParam('display');

foreach( $checkPanels as $checkPanel ){
	if( $customDisplay )
		$checkFiles[] = $checkPanel . '/' . $customDisplay . '.php';
	if( $requestedAction )
		$checkFiles[] = $checkPanel . '/index-' . $requestedAction . '.php';
	if( ! isset($NTS_VIEW['no-index']) )
		$checkFiles[] = $checkPanel . '/index.php';
	}

reset( $checkFiles );
foreach( $checkFiles as $cf ){
	if( $displayFile = ntsLib::fileInCoreDirs('panels/' . $cf) ){
		$displayFiles[] = $displayFile;
		break;
		}
	}
if( isset($NTS_VIEW['form']) ){
	$displayFiles[] = dirname(__FILE__) . '/index-form.php';
	}

$NTS_VIEW['displayFile'] = '';
reset( $displayFiles );
foreach( $displayFiles as $displayFile ){
	if( file_exists($displayFile) ){
		$NTS_VIEW['displayFile'] = $displayFile;
		break;
		}
	}

/* IF PULL ICAL */
if( $_NTS['CURRENT_PANEL'] == 'system/appointments/export' ){
	if( ob_get_length() ){
		ob_end_clean();
		}
	require( dirname(__FILE__) . '/../views/export.php' );
	exit;
	}

/* if no display file exists then it is an error, redirect to home page */
if( ! ($NTS_VIEW['displayFile'] OR isset($NTS_VIEW['output'])) ){
	/* continue to home page */
	ntsView::redirect( ntsLib::getRootWebpage() );
	exit;
	}

require( dirname(__FILE__) . '/_init_menu.php' );

/* pull housekeeping */
require( dirname(__FILE__) . '/cron.php' );

class ntsActionController
{
	var $action_file;
	var $action;
	var $panel;
	var $data;

	function __construct( $panel, $action = '' )
	{
		$this->panel = $panel;
		$this->action = $action;
		$this->data = array();
	}

	function run()
	{
		global $_NTS, $NTS_VIEW;

	/* init files */
		$initInfo = ntsLib::findAllFiles( $this->panel, 'init.php' );
		foreach( $initInfo as $fi )
		{
			require( $fi[0] );
		}

	/* filter files */
		$filterInfo = ntsLib::findAllFiles( $this->panel, 'filter.php' );
		foreach( $filterInfo as $fi )
		{
			require( $fi[0] );
		}

		$action = $this->action;
		if( $action )
		{
			$action_file = 'panels/' . $this->panel . '/a-' . $action . '.php';
			$action_file = ntsLib::fileInCoreDirs($action_file);
			if( $action_file )
				$this->action_file = $action_file;
			else
				$this->action_file = '';
		}

		if( ! $this->action_file )
		{
			$action_file = 'panels/' . $this->panel . '/a.php';
			$this->action_file = ntsLib::fileInCoreDirs($action_file);
		}
		require( $this->action_file );
	}

	function render_file( $view_file = '', $view_params = array() )
	{
		global $NTS_VIEW;
		if( ! $view_file )
			$view_file = dirname($this->action_file) . '/index.php';

		if( $view_params )
		{
			extract($view_params);
		}

		ob_start();
		require( $view_file );
		$output = ob_get_contents();
		ob_end_clean();
		$output = trim( $output );
		return $output;
	}

	function render( $view_file = '', $view_params = array() )
	{
		global $NTS_VIEW;
		$NTS_VIEW['output'] = $this->render_file( $view_file, $view_params );
	}
}
?>