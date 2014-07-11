<?php
$appEdit = ntsLib::getVar( 'admin/manage:appEdit' );
$object = ntsLib::getVar( 'admin/manage/appointments/edit::OBJECT' );
$resourceId = $object->getProp('resource_id');

$iCanEdit = in_array($resourceId, $appEdit) ? true : false;

$object = ntsLib::getVar( 'admin/manage/appointments/edit::OBJECT' );
$ff =& ntsFormFactory::getInstance();
$fParams = array(
	'starts_at'		=> $object->getProp('starts_at'),
	'max_duration'	=> $object->getProp('duration') + 12 * 60 * 60,
	'duration'		=> $object->getProp('duration'),
	'end_time'		=> $object->getProp('starts_at') + $object->getProp('duration'),
	'lead_out'		=> $object->getProp('lead_out'),
	);
$NTS_VIEW['formEndTime'] =& $ff->makeForm( dirname(__FILE__) . '/formEndTime', $fParams );
$NTS_VIEW['formEndTime']->readonly = $iCanEdit ? false : true;

$fParams = array_merge( $fParams, $object->getByArray() );

$availability_status = $object->get_availability_status();
$NTS_VIEW['availability_status'] = $availability_status;

$conflicts = $object->get_conflicts();
$NTS_VIEW['conflicts'] = $conflicts;

$NTS_VIEW['customForm'] =& $ff->makeForm( dirname(__FILE__) . '/custom-form', $fParams );
$NTS_VIEW['customForm']->readonly = $iCanEdit ? false : true;

switch( $action ){
	case 'update':
		if( $NTS_VIEW['customForm']->validate() ){
			$formValues = $NTS_VIEW['customForm']->getValues();
			$object->setByArray( $formValues );

			$cm =& ntsCommandManager::getInstance();
			$cm->runCommand( $object, 'update' );

			if( $cm->isOk() ){
				$msg = array( M('Appointment'), ntsView::objectTitle($object), M('Update'), M('OK') );
				$msg = join( ': ', $msg );
				ntsView::addAnnounce( $msg, 'ok' );

			/* continue to the list with anouncement */
				$forwardTo = ntsLink::makeLink( '-current-' );
				ntsView::redirect( $forwardTo );
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

	case 'endtime':
		if( ! $iCanEdit ){
			$errorText = M('Access Denied');
			ntsView::addAnnounce( $errorText, 'error' );

		/* continue to the list with anouncement */
			$forwardTo = ntsLink::makeLink( '-current-' );
			ntsView::redirect( $forwardTo );
			exit;
			}

		if( $NTS_VIEW['formEndTime']->validate() ){
			$formValues = $NTS_VIEW['formEndTime']->getValues();

			$newDuration = $formValues['end_time'] - $object->getProp('starts_at');
			if( $object->getProp('duration') == $newDuration ){
				$errorText = M('Not Changed');
				ntsView::addAnnounce( $errorText, 'error' );
				}
			else {
				$object->setProp( 'duration', $newDuration );
				$cm =& ntsCommandManager::getInstance();
				$cm->runCommand( $object, 'change' );

				if( $cm->isOk() ){
					$msg = array( M('Appointment'), ntsView::objectTitle($object), M('Update'), M('OK') );
					$msg = join( ': ', $msg );
					ntsView::addAnnounce( $msg, 'ok' );
					}
				else {
					$errorText = $cm->printActionErrors();
					ntsView::addAnnounce( $errorText, 'error' );
					}
				}
//			$forwardTo = ntsLink::makeLink( '-current-' );
//			ntsView::redirect( $forwardTo );
			ntsView::getBack( true, true );
			exit;
			}
		else {
			// get back
			}
		break;
	}
?>