<?php
$object->redoRefund();

$object->setProp( 'completed', 0 );
$this->runCommand( $object, 'update' );
$actionResult = 1;
?>