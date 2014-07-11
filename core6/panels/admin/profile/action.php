<?php
$ntsdb =& dbWrapper::getInstance();
$ff =& ntsFormFactory::getInstance();
$cm =& ntsCommandManager::getInstance();

$id = ntsLib::getCurrentUserId();

switch( $action ){
	case 'update':
		$formFile = dirname( __FILE__ ) . '/form';

		$object = new ntsUser();
		$object->setId( $id );
		$customerInfo = $object->getByArray();
		$customerInfo['object'] = $object;

		$form =& $ff->makeForm( $formFile, $customerInfo );

		$removeValidation = array();
		if( NTS_ALLOW_NO_EMAIL && $_NTS['REQ']->getParam('noEmail') ){
			$removeValidation[] = 'email';
			}

		if( $form->validate($removeValidation) ){
			$formValues = $form->getValues();
			if( isset($formValues['noEmail']) && $formValues['noEmail'] )
				$formValues['email'] = '';

		/* update customer */
			$object = new ntsUser();
			$object->setId( $id );
			$object->setByArray( $formValues );

			$cm->runCommand( $object, 'update' );
			if( $cm->isOk() ){
				ntsView::setAnnounce( M('Settings') . ': ' . M('Update') . ': ' . M('OK'), 'ok' );

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

	case 'update_password':
		$ff =& ntsFormFactory::getInstance();
		$passwordFormFile = dirname( __FILE__ ) . '/passwordForm';
		$passwordForm =& $ff->makeForm( $passwordFormFile, array('id' => $id) );

		if( $passwordForm->validate() ){
			$cm =& ntsCommandManager::getInstance();
			$formValues = $passwordForm->getValues();

		/* update password */
			$user = new ntsUser();
			$user->setId( $id );
			$user->setProp( 'new_password', $formValues['password'] );

			$cm->runCommand( $user, 'update' );
			if( $cm->isOk() ){
				ntsView::addAnnounce( M('Password') . ': ' . M('Update') . ': ' . M('OK'), 'ok' );

			/* continue to customer edit */
				$forwardTo = ntsLink::makeLink( '-current-' );
				ntsView::redirect( $forwardTo );
				exit;
				}
			else {
				$actionError = true;
				$errorString = $cm->printActionErrors();
				}
			}
		else {
		/* form not valid, continue to edit form */
			}
		break;
	}

/* customer info */
$object = new ntsUser();
$object->setId( $id );

$customerInfo = $object->getByArray();
$customerInfo['object'] = $object;

if( ! isset($form) ){
	$formFile = dirname( __FILE__ ) . '/form';
	$form =& $ff->makeForm( $formFile, $customerInfo );
	}

if( ! isset($passwordForm) ){
	$passwordFormFile = dirname( __FILE__ ) . '/passwordForm';
	$passwordForm =& $ff->makeForm( $passwordFormFile, array('id' => $id) );
	}
?>