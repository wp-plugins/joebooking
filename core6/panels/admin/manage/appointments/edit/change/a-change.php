<?php
$object = ntsLib::getVar( 'admin/manage/appointments/edit::OBJECT' );

$selected = array(
	'location_id'	=> $_NTS['REQ']->getParam('location_id'),
	'resource_id'	=> $_NTS['REQ']->getParam('resource_id'),
	'service_id'	=> $_NTS['REQ']->getParam('service_id'),
	'starts_at'		=> $_NTS['REQ']->getParam('starts_at'),
	);

foreach( $selected as $k => $v )
{
	if( $v )
	{
		$object->setProp( $k, $v );
	}
}

$cm =& ntsCommandManager::getInstance();
$cm->runCommand( $object, 'change' );

if( $cm->isOk() )
{
	$msg = array( M('Appointment'), ntsView::objectTitle($object), M('Change'), M('OK') );
	$msg = join( ': ', $msg );
	ntsView::addAnnounce( $msg, 'ok' );

/* continue to the list with anouncement */
	$forwardTo = ntsLink::makeLink( '-current-/../overview' );
	ntsView::redirect( $forwardTo );
	exit;
}
else
{
	$errorText = $cm->printActionErrors();
	ntsView::addAnnounce( $errorText, 'error' );
}
?>