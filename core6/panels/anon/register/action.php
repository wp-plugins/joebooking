<?php
if( defined('NTS_REGISTER_REDIRECT') && NTS_REGISTER_REDIRECT ){
	ntsView::redirect( NTS_REGISTER_REDIRECT );
	exit;
	}

$ff =& ntsFormFactory::getInstance();
$formFile = dirname( __FILE__ ) . '/form';

global $NTS_CURRENT_USER;
$formParams = array(
	'_timezone'	=> $NTS_CURRENT_USER->getTimezone() 
	);
$NTS_VIEW['no-index'] = TRUE;
$NTS_VIEW['form'] =& $ff->makeForm( $formFile, $formParams );

$conf =& ntsConf::getInstance();
switch( $action ){
	case 'register':
		$conf =& ntsConf::getInstance();

		$removeValidation = array();
		if( NTS_ALLOW_NO_EMAIL && $_NTS['REQ']->getParam('noEmail') ){
			$removeValidation[] = 'email';
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
			$cm->runCommand( $object, 'create' );
			$NTS_VIEW['object'] = $object;

			if( $cm->isOk() ){
				$lm =& ntsLanguageManager::getInstance(); 
				$activeLanguages = $lm->getActiveLanguages();
				if( $activeLanguages && count($activeLanguages) > 1 )
				{
					global $NTS_CURRENT_USER;
					$lng = $NTS_CURRENT_USER->getLanguage();
					$object->setLanguage( $lng );
				}
				$_SESSION['temp_customer_id'] = $object->getId();

			/* check if we need to require email validation */
				$userEmailConfirmation = $conf->get('userEmailConfirmation');
			/* or admin approval */
				$userAdminApproval = $conf->get('userAdminApproval');

				if( $userEmailConfirmation ){
					$cm->runCommand( $object, 'require_email_confirmation' );
					$cm->runCommand( $object, 'logout' );

					$display = 'emailConfirmation';
					$forwardTo = ntsLink::makeLink( '-current-', '', array('display' => $display) );
					ntsView::redirect( $forwardTo );
					exit;
					}
				elseif( $userAdminApproval ) {
					$cm->runCommand( $object, 'require_approval' );
					$cm->runCommand( $object, 'logout' );

					$display = 'waitingApproval';
					$forwardTo = ntsLink::makeLink( '-current-', '', array('display' => $display, ) );
					ntsView::redirect( $forwardTo );
					exit;
					}
				else {
				/* autoapprove */
					$cm->runCommand( $object, 'activate' );
					ntsView::addAnnounce( M('Congratulations, your account has been created and activated'), 'ok' );
				/* then login */
					$cm->runCommand( $object, 'login' );

				/* continue to login dispatcher */
					$forwardTo = ntsLink::makeLink( 'anon/login/dispatcher' );
					ntsView::redirect( $forwardTo );
					exit;
					}
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