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
	}
?>