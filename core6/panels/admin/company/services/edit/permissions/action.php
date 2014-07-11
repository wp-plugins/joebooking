<?php
$ff =& ntsFormFactory::getInstance();
$object = ntsLib::getVar( 'admin/company/services/edit::OBJECT' );

$formParams = $object->getPermissions();

$formFile = dirname( __FILE__ ) . '/form';
$NTS_VIEW['form'] =& $ff->makeForm( $formFile, $formParams );

switch( $action ){
	case 'update':
		if( $NTS_VIEW['form']->validate() ){
			$formValues = $NTS_VIEW['form']->getValues();
			$object->setByArray( $formValues );

			$autoConfirmed = array();
			$notAllowed = array();

			$permissions = array();
			reset( $formValues );
			foreach( $formValues as $ctlName => $ctlValue ){
				$permissions[] = $ctlName . ':' . $ctlValue;
				}
			$object->setProp( '_permissions', $permissions );

			$cm =& ntsCommandManager::getInstance();
			$cm->runCommand( $object, 'update' );

			if( $cm->isOk() ){
				$msg = array( M('Service'), ntsView::objectTitle($object), M('Update'), M('OK') );
				$msg = join( ': ', $msg );
				ntsView::addAnnounce( $msg, 'ok' );

			/* continue to the list with anouncement */
				$forwardTo = ntsLink::makeLink( '-current-' );
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