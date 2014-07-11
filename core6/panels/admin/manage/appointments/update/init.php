<?php
$id = $_NTS['REQ']->getParam( 'app_id' );
if( ! $id )
	$id = $_NTS['REQ']->getParam( '_id' );
$saveOn = array(
	'_id'		=> $id,
	);
ntsView::setPersistentParams( $saveOn, 'admin/manage/appointments' );

if( preg_match('/-/', $id) )
{
	$ids = explode( '-', $id );
	$ids = array_unique( $ids );
	$object = array();
	foreach( $ids as $iid )
	{
		$obj = ntsObjectFactory::get( 'appointment' );
		$obj->setId( $iid );
		$object[] = $obj;
	}
}
else
{
	$object = ntsObjectFactory::get( 'appointment' );
	$object->setId( $id );
}
ntsLib::setVar( 'admin/manage/appointments/update::OBJECT', $object );
?>