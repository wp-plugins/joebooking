<?php
$object->doRefund();

$object->setProp( 'completed', HA_STATUS_NOSHOW );
$this->runCommand( $object, 'update' );
$actionResult = 1;
?>