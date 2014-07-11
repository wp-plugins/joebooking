<?php
$cm =& ntsCommandManager::getInstance();
$object = ntsLib::getVar( 'admin/company/services/edit::OBJECT' );
$cm->runCommand( $object, 'move_down' );
if( $cm->isOk() ){
	ntsView::setAnnounce( join( ': ', array(ntsView::objectTitle($object), M('Down'), M('OK')) ), 'ok' );
	}
else {
	$errorText = $cm->printActionErrors();
	ntsView::addAnnounce( $errorText, 'error' );
	}
/* continue to the list with anouncement */
$forwardTo = ntsLink::makeLink( '-current-/../../browse' );
ntsView::redirect( $forwardTo );
exit;
?>