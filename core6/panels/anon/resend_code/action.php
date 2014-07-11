<?php
switch( $action ){
	case 'resend':
		$ff =& ntsFormFactory::getInstance();
		$formFile = dirname( __FILE__ ) . '/form';
		$form =& $ff->makeForm( $formFile );

		if( $form->validate() ){
			$formValues = $form->getValues();

		/* check if we have a user with this email */
			$email = $formValues[ 'email' ];

			$uif =& ntsUserIntegratorFactory::getInstance();
			$integrator =& $uif->getIntegrator();
			$where = array(
				'email'	=> array('=', $email)
				);
			$info = $integrator->getUsers( $where );

			if( $info ){
				$object = new ntsUser();
				$object->setId( $info[0]['id'] );

				$restriction = $object->getProp( '_restriction' );
				if( in_array('email_not_confirmed', $restriction) ){
					$cm =& ntsCommandManager::getInstance();
					$cm->runCommand( $object, 'require_email_confirmation' );

					if( $cm->isOk() ){
						$display = 'emailConfirmation';
						$forwardTo = ntsLink::makeLink( 'anon/register', '', array('display' => $display) );
						ntsView::redirect( $forwardTo );
						exit;
						}
					else {
						$errorText = $cm->printActionErrors();
						ntsView::addAnnounce( $errorText, 'error' );

					/* continue to reset pass form */
						$forwardTo = ntsLink::makeLink( '-current-' );
						}
					}
				else {
					ntsView::setAnnounce( M('Email') . ': ' . M('Already Confirmed'), 'ok' );

				/* continue to login page */
					$forwardTo = ntsLink::makeLink( 'anon/login' );
					}

				ntsView::redirect( $forwardTo );
				exit;
				}
			else {
				ntsView::setAnnounce( M('Email') . ': ' . M('Not Registered'), 'error' );
			/* continue to reset pass form */
				$forwardTo = ntsLink::makeLink( '-current-' );
				ntsView::redirect( $forwardTo );
				exit;
				}

		/* continue to login page */
			$forwardTo = ntsLink::makeLink( 'anon/login' );
			ntsView::redirect( $forwardTo );
			exit;
			}
		else {
		/* form not valid, continue to create form */
			}

		break;

	default:
		break;
	}
?>