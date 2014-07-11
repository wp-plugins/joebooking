<?php
$cm =& ntsCommandManager::getInstance();
$object = ntsLib::getVar( 'admin/customers/edit::OBJECT' );

$resultCount = 0;
$failedCount = 0;

$cm->runCommand( $object, 'suspend' );

if( $cm->isOk() ){
	$resultCount++;
	}
else {
	$errorText = $cm->printActionErrors();
	ntsView::addAnnounce( $errorText, 'error' );
	$failedCount++;
	$actionOk = false;
	}

if( $resultCount ){
	$title = M('Customer') . ': ' . '<b>' . $object->getProp('first_name') . ' ' . $object->getProp('last_name') . '</b>';
	$msg = $title . ': ' . M('Suspend') . ': ' . M('OK');
	ntsView::addAnnounce( $msg, 'ok' );
	}

/* continue to the list with anouncement */
$forwardTo = ntsLink::makeLink( '-current-' );
ntsView::redirect( $forwardTo );
exit;
?>