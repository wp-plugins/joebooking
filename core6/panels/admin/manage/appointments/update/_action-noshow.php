<?php
$resultCount = 0;
for( $ii = 0; $ii < count($object); $ii++ ){
	$cm->runCommand( $object[$ii], 'noshow' );
	if( $cm->isOk() ){
		$resultCount++;
		}
	else {
		$errorText = $cm->printActionErrors();
		ntsView::addAnnounce( $errorText, 'error' );
		$failedCount++;
		$actionOk = false;
		}
	}

if( $resultCount ){
	if( count($object) == 1 )
		$msg = array( M('Appointment'), ntsView::objectTitle($object[0]) );
	else
		$msg = array( $resultCount . ' ' . M('Appointments') );
	$msg[] = M('No Show');
	$msg[] = M('OK');
	$msg = join( ': ' , $msg );
	ntsView::addAnnounce( $msg, 'ok' );
	}
?>