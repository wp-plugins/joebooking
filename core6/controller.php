<?php
global $_NTS, $NTS_VIEW;

include_once( dirname(__FILE__) . '/model/init.php' );

if( 
	(! (isset($NTS_VIEW['called_remotely']) && $NTS_VIEW['called_remotely']) ) &&
	( isset($_REQUEST['nts-integrate-url']) OR isset($_REQUEST['nts-integrate-file']) )
	){
	require( dirname(__FILE__) . '/integrate.php' );
	exit;
}

$viewMode = isset($_REQUEST[NTS_PARAM_VIEW_MODE]) ? $_REQUEST[NTS_PARAM_VIEW_MODE] : '';
$NTS_VIEW[NTS_PARAM_VIEW_MODE] = $viewMode;

$viewRich = isset($_REQUEST[NTS_PARAM_VIEW_RICH]) ? $_REQUEST[NTS_PARAM_VIEW_RICH] : '';
$NTS_VIEW[NTS_PARAM_VIEW_RICH] = $viewRich;

require_once( dirname(__FILE__) . '/panels/init2.php' );

$is_remote = isset($NTS_VIEW['called_remotely']) && $NTS_VIEW['called_remotely'];

// if view mode was redefined
if( ntsLib::isAjax() ){
	require( dirname(__FILE__) . '/views/ajax.php' );
	if( ! $is_remote ){
		exit;
	}
}

$viewMode = $NTS_VIEW[NTS_PARAM_VIEW_MODE];

switch( $viewMode ){
	case 'print':
		require( dirname(__FILE__) . '/views/print.php' );
		exit;
		break;
	}
?>