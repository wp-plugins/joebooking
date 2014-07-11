<?php
$om =& objectMapper::getInstance();

/* delete all restrictions */
$om->deleteMeta( $object, '_restriction' );
$actionResult = 1;
?>