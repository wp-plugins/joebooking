<?php
$conf =& ntsConf::getInstance();

$object->doRefund();
$object->setProp( 'completed', HA_STATUS_CANCELLED );

$object->_change_reason = isset($params['reason']) ? $params['reason'] : '';

$this->runCommand( $object, 'update' );
$actionResult = 1;
?>