<?php
$ff =& ntsFormFactory::getInstance();
$object = ntsLib::getVar( 'admin/manage/timeoff/edit::OBJECT' );

$formParams = $object->getByArray();

$t = $NTS_VIEW['t'];
$t->setTimestamp( $formParams['starts_at'] );
$cal = $t->formatDate_Db();
$dayStart = $t->setStartDay();
$time = $formParams['starts_at'] - $dayStart;
$formParams[ 'starts_at_date'] = $cal;
$formParams[ 'starts_at_time'] = $time;

$t->setTimestamp( $formParams['ends_at'] );
$cal = $t->formatDate_Db();
$dayStart = $t->setStartDay();
$time = $formParams['ends_at'] - $dayStart;
$formParams[ 'ends_at_date'] = $cal;
$formParams[ 'ends_at_time'] = $time;

/* check if there're any conflicting appointments */
$formParams['conflicts'] = $object->get_conflicts();

$formFile = dirname( __FILE__ ) . '/form';
$NTS_VIEW['form'] =& $ff->makeForm( $formFile, $formParams );

switch( $action ){
	case 'update':
		if( $NTS_VIEW['form']->validate() ){
			$formValues = $NTS_VIEW['form']->getValues();
			$object->setByArray( $formValues );

			$object->setProp('starts_at', $t->timestampFromDbDate( $formValues['starts_at_date'] ) + $formValues['starts_at_time'] );
			$object->setProp('ends_at', $t->timestampFromDbDate( $formValues['ends_at_date'] ) + $formValues['ends_at_time'] );

			$cm =& ntsCommandManager::getInstance();
			$cm->runCommand( $object, 'update' );

			if( $cm->isOk() ){
				$msg = array( M('Timeoff'), M('Update'), M('OK') );
				$msg = join( ': ', $msg );
				ntsView::addAnnounce( $msg, 'ok' );
				}
			else {
				$errorText = $cm->printActionErrors();
				ntsView::addAnnounce( $errorText, 'error' );
				}


//			ntsView::getBack( true, true );
			$forwardTo = ntsLink::makeLink('-current-/../..');
			ntsView::redirect( $forwardTo );
			exit;
			}
		else {
		/* form not valid, continue to create form */
			}

		break;
	default:
		break;
	}
?>