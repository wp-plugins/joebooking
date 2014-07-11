<?php
$ntsdb =& dbWrapper::getInstance();
$cm =& ntsCommandManager::getInstance();

$app_ids = ntsObjectFactory::getAllIds( 'appointment' );
reset( $app_ids );
$count = 0;
foreach( $app_ids as $app_id )
{
	$object = ntsObjectFactory::get( 'appointment' );
	$object->setId( $app_id );
	$cm->runCommand( $object, 'delete' );
	$count++;
}

ntsView::setAnnounce( $count . ' ' . M('Appointments') . ': '. M('Delete') . ': ' . M('OK'), 'ok' );

$forwardTo = ntsLink::makeLink();
ntsView::redirect( $forwardTo );
exit;
?>