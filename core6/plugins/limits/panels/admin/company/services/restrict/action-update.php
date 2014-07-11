<?php
$plugin = 'limits';

$ntsdb =& dbWrapper::getInstance();
$conf =& ntsConf::getInstance();
$plm =& ntsPluginManager::getInstance();

$defaults = $plm->getPluginSettings( $plugin );

$ff =& ntsFormFactory::getInstance();
$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile, $defaults );

$confPrefix = 'plugin-' . $plugin . '-';

$ff =& ntsFormFactory::getInstance();
$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile );
if( $form->validate() ){
	$formValues = $form->getValues();

/* add theese settings to the database */
	$result = true;
	reset( $formValues );
	foreach( $formValues as $pName => $pValue ){
		$pName = $confPrefix . $pName;
		$conf->set( $pName, $pValue );
		}

	if( ! ($error = $conf->getError()) ){
		ntsView::setAnnounce( M('Settings')  . ': ' . M('Update') . ': ' . M('OK'), 'ok' );

		$forwardTo = ntsLink::makeLink( '-current-' );
		ntsView::redirect( $forwardTo );
		exit;
		}
	}
else {
/* form not valid, continue to edit form */
	}
?>