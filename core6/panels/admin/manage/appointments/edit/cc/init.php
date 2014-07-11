<?php
$object = ntsLib::getVar( 'admin/manage/appointments/edit::OBJECT' );
$objId = $object->getId();

ntsView::setBack( ntsLink::makeLink('admin/manage/appointments/edit/cc', '', array('_id' => $objId)) );
?>