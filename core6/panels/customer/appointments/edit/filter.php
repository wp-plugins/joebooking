<?php
$id = $_NTS['REQ']->getParam( '_id' );

$object = ntsObjectFactory::get( 'appointment' );
$object->setId( $id );
$customer_id = $object->getProp('customer_id');

$current_user_id = ntsLib::getCurrentUserId();

if( $current_user_id != $customer_id )
{
	ntsView::setAnnounce( M('Access Denied'), 'error' );
	$forwardTo = ntsLink::makeLink();
	ntsView::redirect( $forwardTo );
	exit;
}
?>