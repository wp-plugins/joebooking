<?php
$om =& objectMapper::getInstance();

/* delete restriction */
$om->deleteMeta( $object, '_restriction', 'email_not_confirmed' );
$object->deleteProp( '_restriction', 'email_not_confirmed' );
?>