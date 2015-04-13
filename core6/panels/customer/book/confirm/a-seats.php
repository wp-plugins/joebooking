<?php
$ai = $_NTS['REQ']->getParam('ai');
$seats = $_NTS['REQ']->getParam('seats');
if( ! preg_match('/^[\d]*$/', $seats) ){
	$seats = 1;
}

$session = new ntsSession;
$apps = $session->userdata('apps');
if( ! $apps )
	$apps = array();

/* sort by starts_at */
usort( $apps, create_function(
	'$a, $b',
	'
	$return = ($a["starts_at"] - $b["starts_at"]);
	return $return;
	'
	)
);

if( isset($apps[$ai-1]) ){
	$apps[$ai-1]['seats'] = $seats;
}

$redirectAfterCheck = ntsLink::makeLink(
	'-current-/../confirm',
	'',
	array(
		'time'	=> '-reset-',
		)
	);

require( dirname(__FILE__) . '/_check.php' );

/* save */
$session->set_userdata( 'apps', $apps );
ntsView::redirect( $redirectAfterCheck );
exit;
?>