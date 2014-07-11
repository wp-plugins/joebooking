<?php
$conf =& ntsConf::getInstance();

$params = array(
	'remindOfBackup',
	);

$ff =& ntsFormFactory::getInstance();

$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile );

if( $form->validate() ){
	$formValues = $form->getValues();

	reset( $params );
	foreach( $params as $p ){
		$conf->set( $p, $formValues[$p] );
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
?>