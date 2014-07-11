<?php
$object = ntsLib::getVar( 'admin/customers/edit::OBJECT' );
$objId = $object->getId();
ntsView::setBack( ntsLink::makeLink('admin/customers/edit/attachments', '', array('_id' => $objId)) );

ntsLib::setVar( 'admin/attachments::PARENT', $object );

$iCanEdit = TRUE;
ntsLib::setVar( 'admin/attachments::iCanEdit', $iCanEdit );

$alias = 'admin/attachments';
?>