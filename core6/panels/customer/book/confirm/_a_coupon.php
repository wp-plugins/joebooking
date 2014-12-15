<?php
$session = new ntsSession;
$apps = $session->userdata( 'apps' );
$coupon = $session->userdata( 'coupon' );

/* COUPON */
$ntspm =& ntsPaymentManager::getInstance();
$show_coupon = $coupon ? TRUE : FALSE;
$coupon_valid = FALSE;

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

$ff =& ntsFormFactory::getInstance();
$formFile = dirname(__FILE__) . '/form-coupon';
$formParams = array(
	'coupon'	=> $coupon,
	);
$NTS_VIEW['form_coupon'] =& $ff->makeForm( $formFile, $formParams );
?>