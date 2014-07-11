<?php
/* add restriction */
$object->setProp( '_restriction', 'not_approved' );
$this->runCommand( $object, 'update' );
?>