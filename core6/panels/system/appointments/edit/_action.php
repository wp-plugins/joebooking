<?php
$cm =& ntsCommandManager::getInstance();

$forwardTo = ntsLink::makeLink();

$id = $_NTS['REQ']->getParam( 'id' );
$authCode = $_NTS['REQ']->getParam( 'auth' ); 

$object = ntsObjectFactory::get( 'appointment' );
$object->setId( $id );
if( $object->notFound() ){
	ntsView::addAnnounce( 'Appointment not found', 'error' );
	ntsView::redirect( $forwardTo );
	exit;
	}

$appAuthCode = $object->getProp( 'auth_code' );
if( $appAuthCode != $authCode ){
	ntsView::addAnnounce( 'Invalid auth code', 'error' );
	ntsView::redirect( $forwardTo );
	exit;
	}
?>