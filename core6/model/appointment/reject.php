<?php
$conf =& ntsConf::getInstance();

$object->doRefund();

$object->setProp( 'completed', HA_STATUS_CANCELLED );
$this->runCommand( $object, 'update' );
$actionResult = 1;
?>