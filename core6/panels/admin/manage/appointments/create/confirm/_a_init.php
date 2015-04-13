<?php
/* init my apps in session */
$session = new ntsSession;
$apps = $session->userdata('apps');
if( ! $apps )
{
	$apps = array();
}

if( ! $apps )
{
	/* redirect to create */
	$forwardTo = ntsLink::makeLink(
		'-current-/..'
		);
	ntsView::redirect( $forwardTo );
	exit;
}

$cid = 0;
if( $apps && isset($apps[0]['customer_id']) )
{
	$cid = $apps[0]['customer_id'];
}

if( ! $cid )
{
	$cid = $_NTS['REQ']->getParam( 'customer_id' );
}

/* choose customer ? */
if( ! $cid )
{
	/* redirect to customer selection */
	$params = array(
		NTS_PARAM_RETURN	=> 'confirm_app',
		);
	$skip = $_NTS['REQ']->getParam('skip');
	if( $skip )
	{
		$params['skip'] = $skip;
	}
	$forwardTo = ntsLink::makeLink(
		'admin/customers/browse',
		'',
		$params
		);
	ntsView::redirect( $forwardTo );
	exit;
}

/* sort by starts_at */
usort( $apps, create_function(
	'$a, $b',
	'
	$return = ($a["starts_at"] - $b["starts_at"]);
	if( ($return == 0) && isset($a["_id"]) ){
		$return = ($b["_id"] - $a["_id"]);
	}
	return $return;
	'
	)
);

/* save */
require( dirname(__FILE__) . '/../_parse_apps.php' );

$session->set_userdata( 'apps', $apps );

/* set virtual appointments */
$tm2 = ntsLib::getVar('admin::tm2');
reset( $apps );
foreach( $apps as $a )
{
	$tm2->addVirtualAppointment( $a );
}
ntsLib::setVar('admin::tm2', $tm2);
?>