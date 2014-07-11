<?php
global $NTS_VIEW, $NTS_CURRENT_USER;
$viewMode = isset($_REQUEST[NTS_PARAM_VIEW_MODE]) ? $_REQUEST[NTS_PARAM_VIEW_MODE] : '';
$display = isset($_REQUEST['display']) ? $_REQUEST['display'] : '';
$action = isset($_REQUEST[NTS_PARAM_ACTION]) ? $_REQUEST[NTS_PARAM_ACTION] : '';

switch( $action ){
	case 'export':
		require( dirname(__FILE__) . '/views/export.php' );
		break;
	default:
		switch( $display ){
			case 'print':
				require( dirname(__FILE__) . '/views/print.php' );
				break;
			default:
				require( dirname(__FILE__) . '/views/normal.php' );
				break;
			}
		break;
	}
?>