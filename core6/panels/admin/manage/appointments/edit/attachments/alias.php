<?php
$object = ntsLib::getVar( 'admin/manage/appointments/edit::OBJECT' );
$objId = $object->getId();
ntsView::setBack( ntsLink::makeLink('admin/manage/appointments/edit/attachments', '', array('_id' => $objId)) );

ntsLib::setVar( 'admin/attachments::PARENT', $object );

$iCanEdit = TRUE;
$appEdit = ntsLib::getVar( 'admin/manage:appEdit' );
$rid = $object->getProp( 'resource_id' );
if( ! in_array($rid, $appEdit) )
{
	$iCanEdit = FALSE;
}
ntsLib::setVar( 'admin/attachments::iCanEdit', $iCanEdit );

$alias = 'admin/attachments';
?>