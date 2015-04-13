<?php
require( dirname(__FILE__) . '/_a_init.php' );

$ai = $_NTS['REQ']->getParam('ai');
$seats = $_NTS['REQ']->getParam('seats');
if( isset($apps[$ai-1]) ){
	$apps[$ai-1]['seats'] = $seats;
}

/* save */
$session->set_userdata( 'apps', $apps );

$forwardTo = ntsLink::makeLink('-current-');
ntsView::redirect( $forwardTo );
exit;
?>