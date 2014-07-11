<?php
$cm =& ntsCommandManager::getInstance();
$ff =& ntsFormFactory::getInstance();
$object = ntsLib::getVar( 'admin/customers/edit::OBJECT' );

$formParams = $object->getByArray();
$formFile = dirname( __FILE__ ) . '/form';
$NTS_VIEW['form'] =& $ff->makeForm( $formFile, $formParams );

switch( $action ){
	case 'send':
		if( $NTS_VIEW['form']->validate() ){
			$formValues = $NTS_VIEW['form']->getValues();

		/* send */
			$cm->runCommand( $object, 'email', array('body' => $formValues['body'], 'subject' => $formValues['subject']) );

			if( $cm->isOk() ){
				$title = M('Customer') . ': ' . '<b>' . $object->getProp('first_name') . ' ' . $object->getProp('last_name') . '</b>';
				ntsView::setAnnounce( $title . ': ' . M('Send Email') . ': ' . M('OK'), 'ok' );

			/* continue to the list with anouncement */
				$forwardTo = ntsLink::makeLink( '-current-/..' );
				ntsView::redirect( $forwardTo );
				exit;
				}
			else {
				$errorText = $cm->printActionErrors();
				ntsView::addAnnounce( $errorText, 'error' );
				}
			}
		else {
		/* form not valid, continue to edit form */
			}
		break;
	}
?>