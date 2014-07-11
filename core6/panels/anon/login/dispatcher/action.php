<?php
$forwardTo = '';
if( isset($_SESSION['return_after_login']) && $_SESSION['return_after_login'] ){
	$go2panel = $_SESSION['return_after_login']['nts-panel'];
	$go2params = $_SESSION['return_after_login']['params'];
	$go2action = '';
	if( isset($go2params[NTS_PARAM_ACTION]) ){
		$go2action = $go2params[NTS_PARAM_ACTION];
		unset( $go2params[NTS_PARAM_ACTION] );
		}
	$forwardTo = ntsLink::makeLink( $go2panel, $go2action, $go2params );
	unset( $_SESSION['return_after_login'] );
	}
if( $NTS_CURRENT_USER->hasRole('superadmin') ){
	$forwardTo = ntsLink::makeLink( 'superadmin' );
	}
elseif( $NTS_CURRENT_USER->hasRole('admin') ){
	$forwardTo = ntsLink::makeLink( 'admin' );
	}

if( ! $forwardTo ){
	if( $NTS_CURRENT_USER->hasRole('superadmin') ){
		$forwardTo = ntsLink::makeLink( 'superadmin' );
		}
	elseif( $NTS_CURRENT_USER->hasRole('admin') ){
		$forwardTo = ntsLink::makeLink( 'admin' );
		}
	else {
		$forwardTo = ntsLink::makeLink( 'customer' );
		}
	}

ntsView::redirect( $forwardTo );
exit;
?>