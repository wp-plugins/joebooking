<?php
$id = $_NTS['REQ']->getParam( '_id' );
$object = ntsObjectFactory::get( 'timeoff' );
$object->setId( $id );
ntsLib::setVar( 'admin/manage/timeoff/edit::OBJECT', $object );

$schEdit = ntsLib::getVar( 'admin/manage:schEdit' );
$resId = $object->getProp( 'resource_id' );

$iCanEdit = in_array($resId, $schEdit );
if( ! $iCanEdit )
{
	$msg = M('Timeoff') . ': ' . M('Edit') . ': ' . M('Permission Denied');
	ntsView::addAnnounce( $msg, 'error' );
	$forwardTo = ntsLink::makeLink();
	ntsView::redirect( $forwardTo );
	exit;
}
?>