<?php
require( dirname(__FILE__) . '/_a_init.php' );

$ai = $_NTS['REQ']->getParam('ai');
if( isset($apps[$ai-1]) )
{
	array_splice( $apps, $ai-1, 1 );
}

/* save */
$session->set_userdata( 'apps', $apps );

$forwardTo = ntsLink::makeLink('-current-');
ntsView::redirect( $forwardTo );
exit;
?>