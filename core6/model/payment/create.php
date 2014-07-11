<?php
$conf =& ntsConf::getInstance();

/* init some params */
$paidAt = $object->getProp( 'paid_at' );
if( ! $paidAt )
	$object->setProp( 'paid_at', time() );

$currency = $object->getProp( 'currency' );
if( ! $currency )
	$object->setProp( 'currency', $conf->get('currency') );
?>