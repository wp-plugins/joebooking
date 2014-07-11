<?php
$id = $_NTS['REQ']->getParam( '_id' );
ntsView::setPersistentParams( array('_id' => $id), 'admin/customers/edit' );

if( is_array($id) )
{
}
else
{
	$object = new ntsUser();
	$object->setId( $id );
	ntsLib::setVar( 'admin/customers/edit::OBJECT', $object );

	$tm2 = ntsLib::getVar( 'admin::tm2' );
	$where = array(
		'customer_id'	=> array( '=', $id ),
		'completed'		=> array( '>=', 0 ),
		);
	$totalCount = $tm2->countAppointments( $where );
}
?>