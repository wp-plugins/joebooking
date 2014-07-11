<?php
$id = $_NTS['REQ']->getParam( '_id' );
ntsView::setPersistentParams( array('_id' => $id), 'admin/company/services/edit' );

$object = ntsObjectFactory::get( 'service' );
$object->setId( $id );
ntsLib::setVar( 'admin/company/services/edit::OBJECT', $object );
?>