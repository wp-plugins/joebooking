<?php
$ntsdb =& dbWrapper::getInstance();
$object = ntsLib::getVar( 'admin/company/services/edit::OBJECT' );

$ff =& ntsFormFactory::getInstance();
$NTS_VIEW['form'] =& $ff->makeForm( dirname(__FILE__) . '/form' );

/* count how many appointments already exists for this */
$id = $object->getId();
$where = array();
$where['service_id'] = array( '=', $id );

$NTS_VIEW['appsCount'] = $ntsdb->count( 'appointments', $where );

switch( $action ){
	case 'delete':
		$cm =& ntsCommandManager::getInstance();
		$cm->runCommand( $object, 'delete' );

		if( $cm->isOk() ){
			ntsView::setAnnounce( M('Service') . ': ' . ntsView::objectTitle($object) . ': '. M('Delete') . ': ' . M('OK'), 'ok' );
			}
		else {
			$errorText = $cm->printActionErrors();
			ntsView::addAnnounce( $errorText, 'error' );
			}

		/* continue to list */
		$forwardTo = ntsLink::makeLink( '-current-/../../browse' );
		ntsView::redirect( $forwardTo );
		exit;
		break;
	}
?>