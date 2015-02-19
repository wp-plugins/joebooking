<?php
$ntsdb =& dbWrapper::getInstance();
$conf =& ntsConf::getInstance();

$params = array(
	'allowNoEmail',
	'enableTimezones',
	'userEmailConfirmation',
	'userAdminApproval',
	'userLoginRequired',
	'enableRegistration',
	'emailAsUsername',
	'allowDuplicateEmails',
	'firstTimeSplash',
	'customerCanCancel',
	'customerCanReschedule',
	);

switch( $action ){
	case 'update':
		$ff =& ntsFormFactory::getInstance();

		$formFile = dirname( __FILE__ ) . '/form';
		$form =& $ff->makeForm( $formFile );

		if( $form->validate() ){
			$formValues = $form->getValues();

			reset( $params );
			foreach( $params as $p )
			{
				if( array_key_exists($p, $formValues) )
				{
					$conf->set( $p, $formValues[$p] );
				}
			}

		/* if customers not allowed to set timezone, delete _timezone from objectmeta */
			if( ($formValues['enableTimezones'] < 1) )
			{
				$ntsdb->delete(
					'objectmeta',
					array(
						'meta_name'	=> array('=', '_timezone'),
						'obj_class'	=> array('=', 'user'),
						)
					);
			}

			if( ! ($error = $conf->getError()) ){
				ntsView::setAnnounce( M('Settings') . ': ' . M('Update') . ': ' . M('OK'), 'ok' );

			/* continue to delivery options form */
				$forwardTo = ntsLink::makeLink( '-current-' );
				ntsView::redirect( $forwardTo );
				exit;
				}
			else {
				echo '<BR>Database error:<BR>' . $error . '<BR>';
				}
			}
		else {
		/* form not valid, continue to create form */
			}

		break;
	default:
		$default = array();
		reset( $params );
		foreach( $params as $p ){
			$default[ $p ] = $conf->get( $p );
			}

		$ff =& ntsFormFactory::getInstance();
		$formFile = dirname( __FILE__ ) . '/form';
		$form =& $ff->makeForm( $formFile, $default );
		break;
	}
?>