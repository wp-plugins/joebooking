<?php
$conf =& ntsConf::getInstance();
$lm =& ntsLanguageManager::getInstance();

switch( $action ){
	case 'save':
		$ff =& ntsFormFactory::getInstance();
		$formFile = dirname( __FILE__ ) . '/form';

		$NTS_VIEW['key'] = $_NTS['REQ']->getParam( 'key' );
		$NTS_VIEW['lang'] = $_NTS['REQ']->getParam( 'lang' );
		$NTS_VIEW['service'] = $_NTS['REQ']->getParam( 'service' );
		$formParams = array(
			'key'		=> $NTS_VIEW['key'],
			'lang'		=> $NTS_VIEW['lang'],
			'service'	=> $NTS_VIEW['service'],
			);

		$form =& $ff->makeForm( $formFile, $formParams );

		if( $form->validate() ){
			$formValues = $form->getValues();

			$NTS_VIEW['key'] = $_NTS['REQ']->getParam( 'key' );

			$lm =& ntsLanguageManager::getInstance();
			$tm =& ntsEmailTemplateManager::getInstance();

			$languages = $lm->getActiveLanguages();
			if( ! $NTS_VIEW['lang'] )
				$NTS_VIEW['lang'] = $languages[0];
			if( $NTS_VIEW['lang'] == 'en-builtin' )
				$NTS_VIEW['lang'] = 'en';

			$subject = $formValues['subject'];
			$body = $formValues['body'];

			$thisKey = $NTS_VIEW['key'];
			if( $NTS_VIEW['service'] )
			{
				$thisKey = $thisKey . '_' . $NTS_VIEW['service'];
			}
			$result = $tm->save( $NTS_VIEW['lang'], $thisKey, $subject, $body );

			if( $result ){
				ntsView::setAnnounce( M('Template') . ': ' . M('Update') . ': ' . M('OK'), 'ok' );
			/* continue  */
				$forwardTo = ntsLink::makeLink( '-current-', '', array('key' => $NTS_VIEW['key'], 'lang' => $NTS_VIEW['lang'], 'service' => $NTS_VIEW['service']) );
				ntsView::redirect( $forwardTo );
				exit;
				}
			else {
				echo '<BR>Database error:<BR>' . $tm->getError() . '<BR>';
				}
			}
		else {
		/* form not valid, continue to edit form */
			}
		break;

	case 'reset':
		$tm =& ntsEmailTemplateManager::getInstance();

		$NTS_VIEW['lang'] = $_NTS['REQ']->getParam( 'lang' );
		$NTS_VIEW['key'] = $_NTS['REQ']->getParam( 'key' );
		$NTS_VIEW['service'] = $_NTS['REQ']->getParam( 'service' );

		$thisKey = $NTS_VIEW['key'];
		if( $NTS_VIEW['service'] )
		{
			$thisKey = $thisKey . '_' . $NTS_VIEW['service'];
		}
		$result = $tm->reset( $NTS_VIEW['lang'], $thisKey );

		if( $result ){
			ntsView::setAnnounce( M('Template') . ': ' . M('Reset To Defaults') . ': ' . M('OK'), 'ok' );
		/* continue  */
			$forwardTo = ntsLink::makeLink( '-current-', '', array('key' => $NTS_VIEW['key'], 'lang' => $NTS_VIEW['lang'], 'service' => $NTS_VIEW['service']) );
			ntsView::redirect( $forwardTo );
			exit;
			}
		else {
			echo '<BR>Database error:<BR>' . $tm->getError() . '<BR>';
			}
		break;

	default:
		$lm =& ntsLanguageManager::getInstance();
		$tm =& ntsEmailTemplateManager::getInstance();

		$NTS_VIEW['key'] = $_NTS['REQ']->getParam( 'key' );
		$NTS_VIEW['service'] = $_NTS['REQ']->getParam( 'service' );

		$languages = $lm->getActiveLanguages();
		$NTS_VIEW['lang'] = $_NTS['REQ']->getParam( 'lang' );
		if( ! $NTS_VIEW['lang'] )
			$NTS_VIEW['lang'] = $languages[0];

		if( $NTS_VIEW['lang'] == 'en-builtin' )
			$NTS_VIEW['lang'] = 'en';

		if( $NTS_VIEW['lang'] != 'en' )
		{
			$languageConf = $lm->getLanguageConf( $NTS_VIEW['lang'] );
			if( isset($languageConf['charset']) )
			{
				header( 'Content-Type: text/html; charset=' . $languageConf['charset'] );
			}
		}

		$getKey = $NTS_VIEW['key'];
		if( $NTS_VIEW['service'] )
		{
			$tailoredKey = $getKey . '_' . $NTS_VIEW['service'];
			$template = $tm->getTemplate( $NTS_VIEW['lang'], $tailoredKey );
			if( ! $template )
			{
				$template = $tm->getTemplate( $NTS_VIEW['lang'], $getKey );
			}
		}
		else
		{
			$template = $tm->getTemplate( $NTS_VIEW['lang'], $getKey );
		}

	/* prepare form */
		$ff =& ntsFormFactory::getInstance();
		$formParams = array(
			'key'		=> $NTS_VIEW['key'],
			'lang'		=> $NTS_VIEW['lang'],
			'service'	=> $NTS_VIEW['service'],
			'subject'	=> $template['subject'],
			'body'		=> $template['body'],
			);
		$form =& $ff->makeForm( dirname(__FILE__) . '/form', $formParams );
		break;
	}
?>