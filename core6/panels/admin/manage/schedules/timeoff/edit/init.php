<?php
$id = $_NTS['REQ']->getParam( '_id' );
ntsView::setPersistentParams( array('_id' => $id), 'admin/manage/schedules/timeoff/edit' );

$object = ntsObjectFactory::get( 'timeoff' );
$object->setId( $id );
ntsLib::setVar( 'admin/manage/timeoff/edit::OBJECT', $object );
?>