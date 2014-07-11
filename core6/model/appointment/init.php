<?php
$actionDescription = join( ': ', array(M('Appointment'), 'init') );

/* check that we have mandatory data set */
if( ! $object->getProp('location_id') )
{
	$actionResult = 0;
	$actionError = join( ': ', array(M('Required'), M('Location')) );
	$actionStop = 1;
	return;
}

if( ! $object->getProp('resource_id') )
{
	$actionResult = 0;
	$actionError = join( ': ', array(M('Required'), M('Bookable Resource')) );
	$actionStop = 1;
	return;
}

if( ! $object->getProp('service_id') )
{
	$actionResult = 0;
	$actionError = join( ': ', array(M('Required'), M('Service')) );
	$actionStop = 1;
	return;
}

if( ! $object->getProp('customer_id') )
{
	$actionResult = 0;
	$actionError = join( ': ', array(M('Required'), M('Customer')) );
	$actionStop = 1;
	return;
}

if( ! $object->getProp('starts_at') )
{
	$actionResult = 0;
	$actionError = join( ': ', array(M('Required'), M('Time')) );
	$actionStop = 1;
	return;
}

$now = time();
$object->setProp( 'created_at', $now );
$object->setProp( 'approved', 0 );
$object->setProp( 'completed', 0 );

/* reminder */
$object->setProp( 'need_reminder', 1 );

/* auth code */
$authCode = ntsLib::generateRand( 
	8,
	array(
		'caps'	=> FALSE,
		'hex'	=> TRUE,
		)
	);
$object->setProp( 'auth_code', $authCode );

/* ref code */
$groupRef = $object->getProp( 'group_ref' );
if( ! $groupRef )
{
	$groupRef = ntsLib::generateRand( 
		16,
		array(
			'caps'	=> FALSE,
			'hex'	=> TRUE,
			)
		);
	$object->setProp( 'group_ref', $groupRef );
}

/* duration */
$service_id = $object->getProp('service_id');
$service = ntsObjectFactory::get( 'service' );
$service->setId( $service_id );
$object->setProp( 'duration', 	$service->getProp('duration') );
$object->setProp( 'lead_in',	$service->getProp('lead_in') );
$object->setProp( 'lead_out',	$service->getProp('lead_out') );

/* price */
$ntspm =& ntsPaymentManager::getInstance();
$price = $object->getProp( 'price' );
if( ! $price )
{
	$basePrice = $ntspm->getBasePrice( $object );
	$object->setProp( 'price', $basePrice );
}

$this->runCommand( $object, 'create' );

$price = $object->getProp( 'price' );
if( strlen($price) )
{
	$coupon = isset($params['coupon']) ? $params['coupon'] : '';
	/* now check if we can apply promotions  */
	$promotions = $ntspm->getPromotions( $object, $coupon );
	foreach( $promotions as $pro )
	{
		$this->runCommand( 
			$pro,
			'apply',
			array(
				'appointment'	=> $object,
				'coupon'		=> $coupon,
				)
			);
	}
}

$actionResult = 1;
?>
