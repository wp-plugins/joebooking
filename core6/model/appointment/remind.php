<?php
$need_reminder = $object->getProp( 'need_reminder' );
if( $need_reminder )
{
	$object->setProp( 'need_reminder', 0 );
	$this->runCommand( $object, 'update' );
}
$object->setProp( 'really_need_reminder', $need_reminder );
$actionResult = 1;
?>