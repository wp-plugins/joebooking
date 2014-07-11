<?php
/* this file is here to indicate that the menu hierarchy starts here */
$conf =& ntsConf::getInstance();
$userLoginRequired = $conf->get('userLoginRequired');

$firstTimeSplash = $conf->get('firstTimeSplash');
if( $firstTimeSplash && (! isset($_COOKIE['ntsFirstTimeSplash'])) && ($_NTS['CURRENT_PANEL'] != 'customer/splash') ){
	$forwardTo = ntsLink::makeLink( 'customer/splash' );
	ntsView::redirect( $forwardTo );
	exit;
	}

/* also check permissions and set default panel */
$allow_nologin = array(
	'customer/splash',
	'customer/book',
	'customer/invoices/view',
	);

if( (! ntsLib::getCurrentUserId()) && $userLoginRequired && (! in_array($_NTS['CURRENT_PANEL'], $allow_nologin)) )
{
	if( $_NTS['CURRENT_PANEL'] != 'customer' )
	{
		$requestParams = $_NTS['REQ']->getGetParams();
		$returnPage = array(
			NTS_PARAM_PANEL		=> $_NTS['CURRENT_PANEL'],
			NTS_PARAM_ACTION	=> $requestParams,
			'params'	=> $requestParams,
			);
		$_SESSION['return_after_login'] = $returnPage;
	}
	/* redirect to login page */
	$_NTS['REQUESTED_PANEL'] = 'anon/login';
}

if( ! isset($_NTS['CURRENT_PANEL']) )
	$_NTS['CURRENT_PANEL'] = 'customer';

global $NTS_VIEW;
$t = new ntsTime();
global $NTS_CURRENT_USER;
$t->setTimezone( $NTS_CURRENT_USER->getTimezone() );
$NTS_VIEW['t'] = $t;
?>