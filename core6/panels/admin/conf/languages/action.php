<?php
$conf =& ntsConf::getInstance();
$lm =& ntsLanguageManager::getInstance();

switch( $action ){
	case 'activate':
		$newLanguage = $_NTS['REQ']->getParam( 'language' );

		$setting = $lm->languageActivate( $newLanguage );

		$newValue = $conf->set( 'languages', $setting );

		if( ! ($error = $conf->getError()) ){
			ntsView::setAnnounce( M('Language') . ': ' . M('Activate') . ': ' . M('OK'), 'ok' );
		/* continue to import zip codes */
			$forwardTo = ntsLink::makeLink( 'admin/conf/languages' );
			ntsView::redirect( $forwardTo );
			exit;
			}
		else {
			echo '<BR>Database error:<BR>' . $error . '<BR>';
			}
		break;

	case 'disable':
		$disableLanguage = $_NTS['REQ']->getParam( 'language' );
		$setting = $lm->languageDisable( $disableLanguage );

		$newValue = $conf->set( 'languages', $setting );

		if( ! ($error = $conf->getError()) ){
			ntsView::setAnnounce( 'Language Disabled', 'ok' );
		/* continue to import zip codes */
			$forwardTo = ntsLink::makeLink( 'admin/conf/languages' );
			ntsView::redirect( $forwardTo );
			exit;
			}
		else {
			echo '<BR>Database error:<BR>' . $error . '<BR>';
			}
		break;

	default:
		break;
	}
?>