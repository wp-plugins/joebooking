<?php
$conf =& ntsConf::getInstance();
$plm =& ntsPluginManager::getInstance();

/* plugin */
$plugin = $_NTS['REQ']->getParam( 'plugin' );

$plgFolder = $plm->getPluginFolder( $plugin );
$formFile = $plgFolder . '/settingsForm.php';
if( ! file_exists($formFile) ){
/* continue to really activate */
	$forwardTo = ntsLink::makeLink( '-current-/..', 'activate', array('plugin' => $plugin) );
	ntsView::redirect( $forwardTo );
	exit;
	}

$new = $_NTS['REQ']->getParam( 'new' );
if( ! $new )
	$new = 0;

$NTS_VIEW['plugin'] = $plugin;
$NTS_VIEW['new'] = $new;

$defaults = $plm->getPluginSettings( $plugin );
$defaults['plugin'] = $plugin;
$defaults['new'] = $new;

$ff =& ntsFormFactory::getInstance();
$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile, $defaults );

$confPrefix = 'plugin-' . $plugin . '-';

switch( $action ){
	case 'update':
	case 'activate':
		$ff =& ntsFormFactory::getInstance();
		$formFile = dirname( __FILE__ ) . '/form';
		$form =& $ff->makeForm( $formFile, array('plugin' => $plugin, 'new' => $new) );
		if( $form->validate() ){
			$formValues = $form->getValues();

		/* add theese settings to the database */
			$result = true;
			reset( $formValues );
			foreach( $formValues as $pName => $pValue ){
				$pName = $confPrefix . $pName;
				$conf->set( $pName, $pValue );
				}
			$actionError = $conf->getError() ? false : true;
			}
		else {
		/* form not valid, continue to edit form */
			$actionError = false;
			}
		break;

	default:
		break;
	}

switch( $action ){
	case 'update':
		if( $actionError ){
			ntsView::setAnnounce( M('Plugin')  . ': ' . M('Update') . ': ' . M('OK'), 'ok' );

		/* continue to the list with anouncement */
			$forwardTo = ntsLink::makeLink( '-current-', '', array('plugin' => $plugin) );
			ntsView::redirect( $forwardTo );
			exit;
			}
		break;

	case 'activate':
		if( $actionError ){
		/* continue to really activate */
			$forwardTo = ntsLink::makeLink( '-current-/..', 'activate', array('plugin' => $plugin) );
			ntsView::redirect( $forwardTo );
			exit;
			}
		break;

	default:
		break;
	}
?>