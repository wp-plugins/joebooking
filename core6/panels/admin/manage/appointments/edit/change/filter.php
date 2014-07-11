<?php
$appEdit = ntsLib::getVar( 'admin/manage:appEdit' );
$object = ntsLib::getVar( 'admin/manage/appointments/edit::OBJECT' );
$rid = $object->getProp( 'resource_id' );

if( ! in_array($rid, $appEdit) )
{
	$msg = M('Appointment') . ': ' . M('Edit') . ': ' . M('Permission Denied');
	ntsView::addAnnounce( $msg, 'error' );

	/* continue */
	$forwardTo = ntsLink::makeLink( 'admin' );
	ntsView::redirect( $forwardTo );
	exit;
}
?>