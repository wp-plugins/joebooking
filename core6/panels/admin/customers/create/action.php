<?php
global $NTS_CURRENT_USER;
$class = 'customer';

$ff =& ntsFormFactory::getInstance();
$formFile = dirname( __FILE__ ) . '/form';
$NTS_VIEW['form'] =& $ff->makeForm( $formFile );

switch( $action ){
	case 'create-customer':
		$removeValidation = array();
		if( NTS_ALLOW_NO_EMAIL && $_NTS['REQ']->getParam('noEmail') ){
			$removeValidation[] = 'email';
			}
		if( (! NTS_ENABLE_REGISTRATION) && (! $_NTS['REQ']->getParam('login-details')) ){
			$removeValidation[] = 'username';
			}

		if( $NTS_VIEW['form']->validate($removeValidation) ){
			$formValues = $NTS_VIEW['form']->getValues();
			if( isset($formValues['noEmail']) && $formValues['noEmail'] )
				$formValues['email'] = '';

			$cm =& ntsCommandManager::getInstance();

		/* customer */
			$object = new ntsUser();
			unset( $formValues['password2'] );
			$object->setByArray( $formValues );
			$object->setProp('_role', array($class) );
			$object->setProp('_created_by', $NTS_CURRENT_USER->getId() );

			$cm->runCommand( $object, 'create' );
			if( $cm->isOk() ){
				if( isset($formValues['notify']) && $formValues['notify'] ){
					$cm->runCommand( $object, 'activate' );
					}

				$id = $object->getId();

			/* get back */
				$announce = array( M('Customer'), ntsView::objectTItle($object), M('Create'), M('OK') );
				$announce = join( ': ', $announce );
				ntsView::addAnnounce( $announce, 'ok' );

				$returnTo = ntsLib::getVar('admin::returnTo');
				if( $returnTo )
				{
					$forwardTo = ntsLink::makeLink(
						$returnTo,
						'',
						array(
							NTS_PARAM_RETURN	=> '-reset-',
							'customer_id'		=> $id,
							)
						);
				}
				else
				{
					$forwardTo = ntsLink::makeLink('-current-/../edit/edit', '', array('_id' => $id));
				}
				ntsView::redirect( $forwardTo, false, true );
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