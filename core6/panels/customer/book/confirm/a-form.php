<?php
$ff =& ntsFormFactory::getInstance();
$formFile = dirname(__FILE__) . '/form';
$formParams = array(
	'service_id'	=> $service_id
	);
$NTS_VIEW['form'] =& $ff->makeForm( $formFile, $formParams );

if( ! isset($view) )
	$view = array();

require( dirname(__FILE__) . '/_a_coupon.php' );

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