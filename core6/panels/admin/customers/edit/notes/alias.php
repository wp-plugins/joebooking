<?php
$object = ntsLib::getVar( 'admin/customers/edit::OBJECT' );
$objId = $object->getId();
ntsView::setBack( ntsLink::makeLink('admin/customers/edit/notes', '', array('_id' => $objId)) );

ntsLib::setVar( 'admin/notes::PARENT', $object );

$iCanEdit = TRUE;
ntsLib::setVar( 'admin/notes::iCanEdit', $iCanEdit );

$alias = 'admin/notes';
?>