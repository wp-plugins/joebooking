<?php
if( defined('NTS_LOGIN_REDIRECT') && NTS_LOGIN_REDIRECT ){
	ntsView::redirect( NTS_LOGIN_REDIRECT );
	exit;
	}
$ff =& ntsFormFactory::getInstance();
$formFile = dirname( __FILE__ ) . '/form_forgot';
$NTS_VIEW['form_forgot'] =& $ff->makeForm( $formFile );

$formFile = dirname( __FILE__ ) . '/form';
$NTS_VIEW['form'] =& $ff->makeForm( $formFile );

$user = $_NTS['REQ']->getParam('user');
$NTS_VIEW['user'] = $user;

switch( $action ){
	case 'login':
		if( $NTS_VIEW['form']->validate() ){
			$formValues = $NTS_VIEW['form']->getValues();
			$remember = isset($formValues['remember']) ? $formValues['remember'] : FALSE;

		/* local handler */
			$object = new ntsUser();
			if( NTS_EMAIL_AS_USERNAME )
				$object->setProp( 'email', $formValues['email'] );
			else
				$object->setProp( 'username', $formValues['username'] );
			$object->setProp( 'password', $formValues['password'] );

			$cm =& ntsCommandManager::getInstance();
			$cm->runCommand( $object, 'check_password' );

			if( ! $cm->isOk() ){
				$errorText = $cm->printActionErrors();
				ntsView::addAnnounce( $errorText, 'error' );

			/* continue to login form */
				$forwardTo = ntsLink::makeLink( '-current-' );
				ntsView::redirect( $forwardTo );
				exit;
				}

		/* check user restrictions if any */
			$restrictions = $object->getProp('_restriction');

		/* restrictions apply */
			if( $restrictions ){
				$display = '';
				if( in_array('email_not_confirmed', $restrictions) ){
					$display = 'emailNotConfirmed';
					}
				elseif( in_array('not_approved', $restrictions) ){
					$display = 'notApproved';
					}
				elseif( in_array('suspended', $restrictions) ){
					$display = 'suspended';
					}
				else {
					$msg = M('There is a problem with your account');
					}

				if( $display ){
					$forwardTo = ntsLink::makeLink( '-current-', '', array('display' => $display) );
					}
				else {
					ntsView::addAnnounce( $msg, 'error' );
					$forwardTo = ntsLink::makeLink();
					}

				ntsView::redirect( $forwardTo );
				exit;
				}
			else {
			/* complete actions */
				$params = array(
					'remember'	=> $remember 
					);
				$cm->runCommand( $object, 'login', $params );
			/* continue to login dispatcher */
				$forwardTo = ntsLink::makeLink( '-current-/dispatcher' );
				ntsView::redirect( $forwardTo );
				exit;
				}
			}
		else {
		/* form not valid, continue to login form */
			}
		break;

	default:
		break;
	}
?>