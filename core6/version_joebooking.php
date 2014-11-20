<?php
require( dirname(__FILE__) . '/_version_disable.php' );
$modify_version = -5000;
$modify_version = 0;
$app_short = 'jbk';
$order_link = 'http://www.joebooking.com/order/';

/* features configuration */
$my_disable = array(
	'common',
	'attach',

	'appointment_flow',
	'hooks',
	'payment',
//	'language',
	'packs',
	'promotions',
	'custom_fields',
	'sms',

	'resources',
	'locations',

	'joomla',
	'wordpress_pro',
	'check_license',
	'jquery',
	);

$skip = array();
foreach( $my_disable as $d )
{
	$skip = array_merge( $skip, $disable[$d]['panels'] );
}

$disabled_features = array(
//	'class'			=> 1,
//	'flex_service'	=> 1,
//	'capacity'	=> 1,
	);
?>