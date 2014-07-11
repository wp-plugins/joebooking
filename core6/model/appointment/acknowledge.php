<?php
$object->setProp( '_ack', 1 );
$this->runCommand( $object, 'update' );
$actionResult = 1;
?>