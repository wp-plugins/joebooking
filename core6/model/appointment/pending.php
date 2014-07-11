<?php
$alreadyApproved = $object->getProp( 'approved' );
if( ! $alreadyApproved ){
	$actionResult = 0;
	$actionError = M('Appointment') . ' (id=' . $object->getId() .  '): ' . M('Pending');
	$actionStop = true;
	return;
	}
else {
	$object->setProp( 'approved', 0 );
	$this->runCommand( $object, 'update' );
	$actionResult = 1;
	}
?>