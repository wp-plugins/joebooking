<?php
$id = $_NTS['REQ']->getParam( 'app_id' );
if( ! $id )
	$id = $_NTS['REQ']->getParam( '_id' );

$single = $_NTS['REQ']->getParam( 'single' );
$noheader = $_NTS['REQ']->getParam( 'noheader' );
$saveOn = array(
	'_id'		=> $id,
	'single'	=> $single,
	'noheader'	=> $noheader
	);

$hide = $_NTS['REQ']->getParam( 'hide' );
if( $hide )
{
	$saveOn['hide'] = $hide;
	$hide = explode( ':', $hide );
}
else
{
	$hide = array();
}
ntsLib::setVar( 'admin/manage/appointments/edit::hide', $hide );

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
ntsLib::setVar( 'admin/manage/appointments/edit::OBJECT', $object );

$current_user = ntsLib::getCurrentUser();
$canDelete = ( $current_user->getProp('_admin_level') == 'staff' ) ? FALSE : TRUE;
ntsLib::setVar( 'admin/manage/appointments/edit::canDelete', $canDelete );
?>