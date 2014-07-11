<?php
$ff =& ntsFormFactory::getInstance();
$object = ntsLib::getVar( 'admin/company/services/edit::OBJECT' );

$formFile = dirname( __FILE__ ) . '/form';
$NTS_VIEW['form'] =& $ff->makeForm( $formFile );

switch( $action )
{
	case 'merge':
		if( $NTS_VIEW['form']->validate() )
		{
			$formValues = $NTS_VIEW['form']->getValues();
			$mergeToId = $formValues['merge_to'];

			$cm =& ntsCommandManager::getInstance();
			$cm->runCommand( $object, 'merge_to', array('to' => $mergeToId) );

			if( $cm->isOk() )
			{
				ntsView::addAnnounce( M('Service') . ': ' . M('Merge') . ': ' . M('OK'), 'ok' );

			/* continue to the list with anouncement */
				$forwardTo = ntsLink::makeLink( '-current-/../../browse' );
				ntsView::redirect( $forwardTo );
				exit;
			}
			else
			{
				$errorText = $cm->printActionErrors();
				ntsView::addAnnounce( $errorText, 'error' );
			}
		}
		break;
	default:
		break;
}
?>