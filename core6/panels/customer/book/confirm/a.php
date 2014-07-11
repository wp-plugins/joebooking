<?php
$view = array();

$session = new ntsSession;
$apps = $session->userdata( 'apps' );

if( ! $apps )
{
	$forwardTo = ntsLink::makeLink('-current-/..');
	ntsView::redirect( $forwardTo );
	exit;
}

/* check if we have custom form */
$service_id = $_NTS['REQ']->getParam('service');
$om =& objectMapper::getInstance();
$form_id = $om->isFormForService( $service_id );

require( dirname(__FILE__) . '/_a_coupon.php' );

$view['coupon'] = $coupon;
$view['show_coupon'] = $show_coupon;
$view['coupon_valid'] = $coupon_valid;
$view['coupon_promotions'] = $coupon_promotions;

if( $form_id )
{
	require( dirname(__FILE__) . '/a-form.php' );
	return;
}

if( ! ntsLib::getCurrentUserId() )
{
	$forwardTo = ntsLink::makeLink('-current-/../login');
	ntsView::redirect( $forwardTo );
	exit;
}

/* display confirm button */
require( dirname(__FILE__) . '/a-form.php' );
?>