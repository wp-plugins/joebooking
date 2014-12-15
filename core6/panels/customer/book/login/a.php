<?php
$session = new ntsSession;
$apps = $session->userdata( 'apps' );
if( ! $apps )
{
	$forwardTo = ntsLink::makeLink('-current-/..');
	ntsView::redirect( $forwardTo );
	exit;
}

$ff =& ntsFormFactory::getInstance();

$formFile = dirname(__FILE__) . '/_form_register';
$formParams = array();
$NTS_VIEW['form_register'] =& $ff->makeForm( $formFile, $formParams );

$formFile = dirname(__FILE__) . '/_form_login';
$formParams = array();
$NTS_VIEW['form_login'] =& $ff->makeForm( $formFile, $formParams );

$formFile = dirname(__FILE__) . '/_form_forgot';
$formParams = array();
$NTS_VIEW['form_forgot'] =& $ff->makeForm( $formFile, $formParams );

$view = array();

/* COUPON */
$coupon = $session->userdata( 'coupon' );
$ntspm =& ntsPaymentManager::getInstance();
$show_coupon = $coupon ? TRUE : FALSE;
$coupon_valid = FALSE;

$ff =& ntsFormFactory::getInstance();
$formFile = dirname(__FILE__) . '/../confirm/form-coupon';
$formParams = array(
	'coupon'	=> $coupon,
	);
$NTS_VIEW['form_coupon'] =& $ff->makeForm( $formFile, $formParams );
$coupon_promotions = array();

if( $coupon )
{
	$coupon_valid = TRUE;
	/* check if valid */
	foreach( $apps as $r )
	{
//		$coupon_promotions = $ntspm->getPromotions( $r, $coupon, TRUE );
		$coupon_promotions = $ntspm->getPromotions( $r, $coupon );
		if( $coupon_promotions )
		{
			break;
		}
	}
	if( ! $coupon_promotions )
	{
		$coupon_valid = FALSE;
		$NTS_VIEW['form_coupon']->errors['coupon'] = M('Not Valid');
	}
}
else
{
	foreach( $apps as $r )
	{
		$coupon_promotions = $ntspm->getPromotions( $r, '', TRUE );
		if( $coupon_promotions )
		{
			$show_coupon = TRUE;
			break;
		}
	}
}

$view['coupon'] = $coupon;
$view['show_coupon'] = $show_coupon;
$view['coupon_valid'] = $coupon_valid;
$view['coupon_promotions'] = $coupon_promotions;

$view['auto_resource'] = $auto_resource;
$view['auto_location'] = $auto_location;

$this->render( 
	dirname(__FILE__) . '/index.php',
	$view
	);
?>