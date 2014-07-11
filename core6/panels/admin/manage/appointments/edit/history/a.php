<?php
$object = ntsLib::getVar( 'admin/manage/appointments/edit::OBJECT' );
$logs = $object->getLogs();

$view = array(
	'object'	=> $object,
	'logs'		=> $logs,
	);

$this->render(
	dirname(__FILE__) . '/index.php',
	$view
	);
?>