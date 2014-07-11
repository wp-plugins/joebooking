<?php
$object = ntsLib::getVar( 'admin/manage/appointments/edit::OBJECT' );
$objId = $object->getId();
ntsView::setBack( ntsLink::makeLink('admin/manage/appointments/edit/notes', '', array('_id' => $objId)) );

ntsLib::setVar( 'admin/notes::PARENT', $object );

$appEdit = ntsLib::getVar( 'admin/manage:appEdit' );
$resourceId = $object->getProp('resource_id');
$iCanEdit = in_array($resourceId, $appEdit) ? TRUE : FALSE;
ntsLib::setVar( 'admin/notes::iCanEdit', $iCanEdit );

$alias = 'admin/notes';
?>