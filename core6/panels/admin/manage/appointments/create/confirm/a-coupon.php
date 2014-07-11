<?php
$coupon = $_NTS['REQ']->getParam('coupon');

/* save */
$session->set_userdata( 'coupon', $coupon );

$forwardTo = ntsLink::makeLink('-current-');
ntsView::redirect( $forwardTo );
exit;
?>