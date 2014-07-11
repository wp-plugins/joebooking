<?php
$cal = $_NTS['REQ']->getParam('cal');

$schEdit = ntsLib::getVar( 'admin/manage:schEdit' );
$tm2 = ntsLib::getVar('admin::tm2');

$t = $NTS_VIEW['t'];
$ntsdb =& dbWrapper::getInstance();

$ff =& ntsFormFactory::getInstance();
$formFile = dirname( __FILE__ ) . '/form';
$fParams = array();

$fParams['cal'] = $cal;
$fParams['location_id'] = 0;
$fParams['service_id'] = 0;

$when = array();

/*
variants:
1) cal given: a) this date b) range this weekday
2) no cal, applied on given: a) range this weekday
3) nothing given: a) range choose weekday(s) 
*/

if( $cal ){
	$when[] = 'range';
	$when[] = 'date';

	$fParams['date'] = $cal;
	$t->setDateDb( $cal );
	$appliedOn = $t->getWeekday();
	$fParams['applied_on'] = array( $appliedOn );

	$fParams['valid_from'] = $t->formatDate_Db();
	$t->modify( '+1 year' );
	$fParams['valid_to'] = $t->formatDate_Db();
	}
else {
	$when[] = 'range';
	$fParams['date'] = '';

	$appliedOn = $_NTS['REQ']->getParam('applied_on');
	if( is_array($appliedOn) ){
		$fParams['applied_on'] = $appliedOn;
		}
	else {
		if( strlen($appliedOn) ){
			$fParams['applied_on'] = array( $appliedOn );
			}
		else {
			$fParams['applied_on'] = array();
			}
		}

	$t->setNow();
	$fParams['valid_from'] = $t->formatDate_Db();
	$t->modify( '+1 year' );
	$fParams['valid_to'] = $t->formatDate_Db();
	}

$fParams['applied_on'] = $appliedOn;
$fParams['showWhen'] = $when;
$fParams['when'] = $when[0];
$fParams['slot_type'] = 'range';

$NTS_VIEW['form'] =& $ff->makeForm( $formFile, $fParams );

if( ! $action ){
	return;
	}
/* create */
$when = $_NTS['REQ']->getParam('when');
$slot_type = $_NTS['REQ']->getParam('slot_type');

$removeValidation = array();
if( $when == 'date' )
{
	$removeValidation[] = 'applied_on';
}
if( $slot_type == 'fixed' )
{
	$removeValidation[] = 'starts_at_range';
	$removeValidation[] = 'ends_at_range';
}


if( $NTS_VIEW['form']->validate($removeValidation) ){
	$formValues = $NTS_VIEW['form']->getValues();
	$resId = $formValues['resource_id'];
	$iCanEdit = in_array($resId, $schEdit);

	if( ! $iCanEdit ){
		ntsView::addAnnounce( M('Schedules') . ': ' . M('Update') . ': ' . M('Permission Denied'), 'error' );
		}
	else {
		$newBlock['resource_id'] = $formValues['resource_id'];
		$newBlock['location_id'] = $formValues['location_id'];
		$newBlock['service_id'] = $formValues['service_id'];
		$newBlock['capacity'] = $formValues['capacity'];

		$newBlock['min_from_now'] = $formValues['min_from_now'];
		$newBlock['max_from_now'] = $formValues['max_from_now'];

		if( $formValues['slot_type'] == 'range' ){
			$newBlock['starts_at'] = $formValues['starts_at_range'];
			$newBlock['ends_at'] = $formValues['ends_at_range'];
			$newBlock['selectable_every'] = $formValues['selectable_every'];
			}
		else {
			$newBlock['starts_at'] = $formValues['starts_at_fixed'];
			$newBlock['ends_at'] = 0;
			$newBlock['selectable_every'] = 0;
			}

		switch( $formValues['when'] ){
			case 'date':
				$newBlock['valid_from'] = $formValues['date'];
				$newBlock['valid_to'] = $formValues['date'];
				break;
			case 'range':
				$newBlock['valid_from'] = $formValues['valid_from'];
				$newBlock['valid_to'] = $formValues['valid_to'];
				break;
			}

		switch( $when ){
			case 'date':
				$newBlock['valid_from'] = $formValues['date'];
				$newBlock['valid_to'] = $formValues['date'];
				break;
			case 'range':
				$newBlock['valid_from'] = $formValues['valid_from'];
				$newBlock['valid_to'] = $formValues['valid_to'];
				break;
			}

		switch( $when ){
			case 'date':
				$t->setDateDb( $formValues['date'] );
				$newBlock['applied_on'] = $t->getWeekday();
				break;
			case 'range':
				$newBlock['applied_on'] = $formValues['applied_on'];
				break;
			}

		$tm2->addBlock( $newBlock );
		ntsView::addAnnounce( M('Schedules') . ': ' . M('Update') . ': ' . M('OK'), 'ok' );
		}

	$forwardTo = ntsLink::makeLink('-current-/..');
	ntsView::redirect( $forwardTo );
	exit;
	}
else {
//	_print_r( $NTS_VIEW['form']->errors );
	}
?>