<?php
$ff =& ntsFormFactory::getInstance();
$object = ntsLib::getVar( 'admin/manage/appointments/edit::OBJECT' );

$cc = $object->getProp('_cc' );

$formParams = array();
$index = 1;
reset( $cc );
foreach( $cc as $cc_to )
{
	$formParams['cc_' . $index] = $cc_to;
	$index++;
}

$formFile = dirname(__FILE__) . '/form';
$NTS_VIEW['form'] =& $ff->makeForm( $formFile, $formParams );

switch( $action ){
	case 'save':
		if( $NTS_VIEW['form']->validate() ){
			$formValues = $NTS_VIEW['form']->getValues();

			$cc = array();
			$keys = array_keys($formValues);
			reset( $keys );
			foreach( $keys as $k )
			{
				$pref = 'cc_';
				if( substr($k, 0, strlen($pref)) == $pref )
				{
					$cc_to = trim($formValues[$k]);
					if( $cc_to )
						$cc[] = $cc_to;
					unset( $formValues[$k] );
				}
			}
			if( $cc )
			{
				$formValues['_cc'] = $cc;
			}

			$object->setByArray( $formValues );

			$cm =& ntsCommandManager::getInstance();
			$cm->runCommand( $object, 'update' );

			if( $cm->isOk() ){
				$msg = array( M('Appointment'), ntsView::objectTitle($object), M('Update'), M('OK') );
				$msg = join( ': ', $msg );
				ntsView::addAnnounce( $msg, 'ok' );

				/* continue */
				ntsView::getBack();
				exit;
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