<?php
$confirmKey = ntsLib::generateRand( 8 );

/* add restriction */
$restriction = $object->getProp( '_restriction' );
if( in_array('email_not_confirmed', $restriction) ){
	$actionResult = 1;
	}
else {
	$object->setProp( '_restriction', 'email_not_confirmed' );
	$object->setProp( '_confirmKey', $confirmKey );

	$this->runCommand( $object, 'update' );
	}
?>