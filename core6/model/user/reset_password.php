<?php
/* new password */
$newPassword = ntsLib::generateRand( 8 );

$object->setProp( 'new_password', $newPassword );

$this->silent = true;
$this->runCommand( $object, 'update' );
$this->silent = false;

$actionResult = 1;
?>