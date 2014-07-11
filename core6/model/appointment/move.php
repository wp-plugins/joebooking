<?php
$newLocId = $params['newLocation'];
$object->setProp( 'location_id', $newLocId );
$this->runCommand( $object, 'update' );
$actionResult = 1;
?>