<?php
$ff =& ntsFormFactory::getInstance();

$object = ntsObjectFactory::get( 'service' );

$formFile = dirname( __FILE__ ) . '/form';
$NTS_VIEW['form'] =& $ff->makeForm( $formFile, $object->getByArray() );

switch( $action ){
	case 'create':
		if( $NTS_VIEW['form']->validate() ){
			$formValues = $NTS_VIEW['form']->getValues();

			$cm =& ntsCommandManager::getInstance();

		/* location */
			$object = ntsObjectFactory::get( 'service' );
			$object->setByArray( $formValues );
			$cm->runCommand( $object, 'create' );

			if( $cm->isOk() ){
				$id = $object->getId();

				$msg = array( M('Service'), ntsView::objectTitle($object), M('Create'), M('OK') );
				$msg = join( ': ', $msg );
				ntsView::addAnnounce( $msg, 'ok' );

			/* continue to the list with anouncement */
				$forwardTo = ntsLink::makeLink( '-current-/../browse' );
				ntsView::redirect( $forwardTo );
				exit;
				}
			else {
				$errorText = $cm->printActionErrors();
				ntsView::addAnnounce( $errorText, 'error' );
				}
			}
		else {
		/* form not valid, continue to create form */
			}
		break;
	default:
		break;
	}
?>