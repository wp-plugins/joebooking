<?php
$uif =& ntsUserIntegratorFactory::getInstance();
$integrator =& $uif->getIntegrator();

$userId = $object->getId();
$userPassword = $object->getProp('password');

$remember = isset($params['remember']) ? $params['remember'] : FALSE;

$integrator->login( $userId, $userPassword, $remember );
unset( $_SESSION['temp_customer_id'] );
?>