<?php
$lm =& ntsLanguageManager::getInstance();
$lang = $_NTS['REQ']->getParam( 'language' );
$id = $_NTS['REQ']->getParam( 'id' );

$template_lang_conf = $lm->getLanguageConf( 'languageTemplate' );
$all_strings = array_keys( $template_lang_conf['interface'] );

$this_lang_conf = $lm->getLanguageConf( $lang );
$file_translate = $this_lang_conf['interface'];
$custom_translate = $lm->get_custom( $lang );

$what = $_NTS['REQ']->getParam( 'what' );
if( ! $what )
{
	$what = 'all'; // can be one, edit and stats
}

$view = array(
	'lang'				=> $lang,
	'all_strings'		=> $all_strings,
	'file_translate'	=> $file_translate,
	'custom_translate'	=> $custom_translate,
	);

switch( $what )
{
	case 'stats':
		$this->render( 
			dirname(__FILE__) . '/_stats.php',
			$view
			);
		break;

	case 'edit':
		/* edit string */
		$k = $all_strings[ $id - 1 ];
		if( isset($custom_translate[$k]) )
		{
			$v = $custom_translate[$k];
		}
		elseif( isset($file_translate[$k]) )
		{
			$v = $file_translate[$k];
		}
		else
		{
			$v = $k;
		}

		$view = array(
			'k'			=> $k,
			'id'		=> $id,
			'lang'		=> $lang,
			'value'		=> $v,
			);

		$ff =& ntsFormFactory::getInstance();
		$formFile = dirname( __FILE__ ) . '/form';
		$form =& $ff->makeForm( $formFile, $view );

		switch( $action )
		{
			case 'reset':
				$lm->reset_custom(
					$lang,
					$k
					);

			/* continue to the list with anouncement */
				$forwardTo = ntsLink::makeLink( 
					'-current-',
					'',
					array(
						'language'	=> $lang,
						)
					);
				ntsView::redirect2( $forwardTo );
				exit;

				break;

			case 'save':
				if( $form->validate() )
				{
					$formValues = $form->getValues();
					$new_value = $formValues['value'];
					$lm->set_custom(
						$lang,
						$k,
						$new_value
						);

					if( 1 )
					{
						$msg = array( M('Language'), M('Update'), M('OK') );
						$msg = join( ': ', $msg );
						ntsView::addAnnounce( $msg, 'ok' );
					}
					else
					{
//						_print_r( $formValues );
//						$errorText = $cm->printActionErrors();
//						ntsView::addAnnounce( $errorText, 'error' );
					}
				}
				else
				{
				/* form not valid, continue to create form */
				}

			/* continue to the list with anouncement */
				$forwardTo = ntsLink::makeLink( 
					'-current-',
					'',
					array(
						'language'	=> $lang,
						)
					);
				ntsView::redirect2( $forwardTo );
				exit;

				break;
		}

		$view['form'] = $form;

		/* edit this */
		$this->render( 
			dirname(__FILE__) . '/edit.php',
			$view
			);
		break;

	case 'one':
		$k = $all_strings[ $id - 1 ];
		if( isset($custom_translate[$k]) )
		{
			$v = $custom_translate[$k];
		}
		elseif( isset($file_translate[$k]) )
		{
			$v = $file_translate[$k];
		}
		else
		{
			$v = $k;
		}
		$view['k'] = $k;
		$view['ii'] = $id;

		$this->render( 
			dirname(__FILE__) . '/_one.php',
			$view
			);
		break;

	case 'all':
		$this->render( 
			dirname(__FILE__) . '/index.php',
			$view
			);
		break;
}
?>