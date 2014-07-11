<?php
$alreadyApproved = $object->getProp( 'approved' );
$completed = $object->getProp( 'completed' );

if( $alreadyApproved && (! $completed) )
{
	$actionResult = 0;
	$actionError = M('Appointment') . ' (id=' . $object->getId() .  '): ' . M('Already Approved');
	$actionStop = true;
	return;
}
else
{
	$object->setProp( 'completed', 0 );
	$object->setProp( 'approved', 1 );
	$this->runCommand( $object, 'update' );
	$actionResult = 1;
}
?>