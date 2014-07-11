<?php
$cm =& ntsCommandManager::getInstance();
$ff =& ntsFormFactory::getInstance();
$object = ntsLib::getVar( 'admin/customers/edit::OBJECT' );

$formParams = $object->getByArray();
$formFile = dirname( __FILE__ ) . '/form';
$NTS_VIEW['form'] =& $ff->makeForm( $formFile, $formParams );

switch( $action ){
	case 'update':
		$removeValidation = array();
		if( NTS_ALLOW_NO_EMAIL && $_NTS['REQ']->getParam('noEmail') ){
			$removeValidation[] = 'email';
			}

		if( $NTS_VIEW['form']->validate($removeValidation) ){
			$formValues = $NTS_VIEW['form']->getValues();
			if( isset($formValues['noEmail']) && $formValues['noEmail'] )
				$formValues['email'] = '';

		/* update */
			$object->setByArray( $formValues );
			$cm->runCommand( $object, 'update' );
			if( $cm->isOk() ){
				$title = M('Customer') . ': ' . '<b>' . $object->getProp('first_name') . ' ' . $object->getProp('last_name') . '</b>';
				ntsView::setAnnounce( $title . ': ' . M('Update') . ': ' . M('OK'), 'ok' );

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
		/* form not valid, continue to edit form */
			}
		break;
	}
?>