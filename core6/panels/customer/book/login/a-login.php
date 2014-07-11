<?php
$ff =& ntsFormFactory::getInstance();
$formFile = dirname( __FILE__ ) . '/_form_login';
$form =& $ff->makeForm( $formFile );

if( $form->validate() )
{
	$formValues = $form->getValues();
	$remember = isset($formValues['remember']) ? $formValues['remember'] : FALSE;

/* local handler */
	$object = new ntsUser();
	if( NTS_EMAIL_AS_USERNAME )
		$object->setProp( 'email', $formValues['login_email'] );
	else
		$object->setProp( 'username', $formValues['login_username'] );
	$object->setProp( 'password', $formValues['login_password'] );

	$cm =& ntsCommandManager::getInstance();
	$cm->runCommand( $object, 'check_password' );

	if( ! $cm->isOk() )
	{
	/* wrong password */
		$msg = $cm->printActionErrors();

		ntsView::addAnnounce( $msg, 'error' );
		$forwardTo = ntsLink::makeLink('-current-');
		ntsView::redirect( $forwardTo );
		exit;
	}

/* check user restrictions if any */
	$restrictions = $object->getProp('_restriction');

/* restrictions apply */
	if( $restrictions )
	{
		$display = '';
		if( in_array('email_not_confirmed', $restrictions) )
		{
			$msg = M('Your email is not yet confirmed');
		}
		elseif( in_array('not_approved', $restrictions) )
		{
			$msg = M('Your account is not yet approved');
		}
		elseif( in_array('suspended', $restrictions) )
		{
			$msg = M('Your account is suspended');
		}
		else 
		{
			$msg = M('There is a problem with your account');
		}

		ntsView::addAnnounce( $msg, 'error' );
		$forwardTo = ntsLink::makeLink('-current-');
		ntsView::redirect( $forwardTo );
		exit;
	}
	else
	{
	/* complete actions */
		$params = array(
			'remember'	=> $remember 
			);
		$cm->runCommand( $object, 'login', $params );
	}
}
else 
{
/* form not valid, continue to login form */
	$forwardTo = ntsLink::makeLink('-current-');
	ntsView::redirect( $forwardTo );
	exit;
}

require( dirname(__FILE__) . '/a-finalize.php' );
?>