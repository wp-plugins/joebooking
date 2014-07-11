<?php
$object = ntsLib::getVar( 'admin/manage/appointments/edit::OBJECT' );
$status_actions = $object->getStatusActions();

$view = array(
	'status_actions'	=> $status_actions,
	'object'			=> $object,
	);

$this->render(
	dirname(__FILE__) . '/index.php',
	$view
	);
?>