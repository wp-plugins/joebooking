<?php
$appEdit = ntsLib::getVar( 'admin/manage:appEdit' );
$object = ntsLib::getVar( 'admin/manage/appointments/update::OBJECT' );

if( ! is_array($object) )
	$object = array( $object );

reset( $object );
foreach( $object as $obj )
{
	$rid = $obj->getProp( 'resource_id' );

	if( ! in_array($rid, $appEdit) )
	{
		$msg = M('Appointment') . ': ' . M('Edit') . ': ' . M('Permission Denied');
		ntsView::addAnnounce( $msg, 'error' );

		/* continue */
		ntsView::getBack( true );
		exit;
	}
}
?>