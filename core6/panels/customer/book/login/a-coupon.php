<?php
$supplied = $_NTS['REQ']->getParam('coupon');

$session = new ntsSession;
/* save */
$session->set_userdata( 'coupon', $supplied );

$forwardTo = ntsLink::makeLink('-current-');
ntsView::redirect( $forwardTo );
exit;
?>