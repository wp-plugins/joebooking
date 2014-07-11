<?php
$object->setProp( '_restriction', 'suspended' );
$this->runCommand( $object, 'update' );
$actionResult = 1;
?>