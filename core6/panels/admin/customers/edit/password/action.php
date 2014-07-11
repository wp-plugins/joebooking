<?php
$ff =& ntsFormFactory::getInstance();
$cm =& ntsCommandManager::getInstance();
$object = ntsLib::getVar( 'admin/customers/edit::OBJECT' );

$formFile = dirname( __FILE__ ) . '/form';
$formParams['id'] = $object->getId();
$formParams['email'] = $object->getProp('email');
$formParams['username'] = $object->getProp('username');

$NTS_VIEW['form'] =& $ff->makeForm( $formFile, $formParams );

switch( $action ){
	case 'update_role':
		break;

	case 'update_password':
		if( $NTS_VIEW['form']->validate() ){
			$cm =& ntsCommandManager::getInstance();
			$formValues = $NTS_VIEW['form']->getValues();
			
			if( isset($formValues['username']) )
			{
				$object->setProp('username', $formValues['username']);
			}

		/* update password */
			$object->setProp( 'new_password', $formValues['password'] );
			$cm->runCommand( $object, 'update' );
			if( $cm->isOk() ){
				ntsView::addAnnounce( M('Password Changed'), 'ok' );

			/* continue to customer edit */
				$forwardTo = ntsLink::makeLink( '-current-/../edit' );
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
?>