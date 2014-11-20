<?php
$ff =& ntsFormFactory::getInstance();
$formFile = dirname( __FILE__ ) . '/form_forgot';
$NTS_VIEW['form_forgot'] =& $ff->makeForm( $formFile );

switch( $action ){
	case 'reset':
		if( $NTS_VIEW['form_forgot']->validate() ){
			$formValues = $NTS_VIEW['form_forgot']->getValues();

		/* check if we have a user with this email */
			$email = $formValues[ 'email' ];

			$uif =& ntsUserIntegratorFactory::getInstance();
			$integrator =& $uif->getIntegrator();

			$where = array(
				'email'	=> array('=', $email)
				);
			$info = $integrator->getUsers( $where );

			if( $info ){
				reset( $info );
				foreach( $info as $i ){
					$object = new ntsUser();
					$object->setId( $i['id'] );

					$cm =& ntsCommandManager::getInstance();
					$cm->runCommand( $object, 'reset_password' );
					}
				if( $cm->isOk() ){
					ntsView::setAnnounce( M('Your new password has been sent to your email'), 'ok' );
					}
				else {
					$errorText = $cm->printActionErrors();
					ntsView::addAnnounce( $errorText, 'error' );
					}
				}
			else {
				ntsView::setAnnounce( M('Email') . ': ' . M('Not Registered'), 'error' );
				}

		/* continue to login page */
			$forwardTo = ntsLink::makeLink( '-current-' );
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