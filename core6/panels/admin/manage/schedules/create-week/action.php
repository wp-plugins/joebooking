<?php
$cal = $_NTS['REQ']->getParam('cal');

$schEdit = ntsLib::getVar( 'admin/manage:schEdit' );
$tm2 = ntsLib::getVar('admin::tm2');

$t = $NTS_VIEW['t'];
$ntsdb =& dbWrapper::getInstance();

$ff =& ntsFormFactory::getInstance();
$formFile = dirname( __FILE__ ) . '/form';

/* check if we have defaults */
global $_NTS;
$defaults = isset( $_NTS['defaults']['schedules/create'] ) ? $_NTS['defaults']['schedules/create'] : array();

//$fParams = array();
$fParams = $defaults;
$fParams['cal'] = $cal;

if( ! array_key_exists('location_id', $fParams) ){
	//$fParams['location_id'] = 0;
	$fParams['location_id'] = $locs;
}
if( ! array_key_exists('service_id', $fParams) ){
	//$fParams['service_id'] = 0;
	$fParams['service_id'] = $sers;
}

$when = array();

/*
variants:
1) cal given: a) this date b) range this weekday
2) no cal, applied on given: a) range this weekday
3) nothing given: a) range choose weekday(s) 
*/

$valid_to_modify = '+1 year';
if( array_key_exists('valid_to', $fParams) ){
	$valid_to_modify = $fParams['valid_to'];
}

if( $cal ){
	$when[] = 'range';
	$when[] = 'date';

	$fParams['date'] = $cal;
	$t->setDateDb( $cal );
	$appliedOn = $t->getWeekday();
	$fParams['applied_on'] = array( $appliedOn );

	$fParams['valid_from'] = $t->formatDate_Db();
	$t->modify( $valid_to_modify );
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
	$t->modify( $valid_to_modify );
	$fParams['valid_to'] = $t->formatDate_Db();
	}

$fParams['applied_on'] = $appliedOn;
$fParams['showWhen'] = $when;
$fParams['when'] = $when[0];


for( $di = 0; $di <= 6; $di++ )
{
	$fParams['slot_type_' . $di] = 'none';
}

$NTS_VIEW['form'] =& $ff->makeForm( $formFile, $fParams );

if( ! $action ){
	return;
	}
/* create */
$removeValidation = array();

$something_on = FALSE;
for( $di = 0; $di <= 6; $di++ )
{
	$slot_type = $_NTS['REQ']->getParam('slot_type_' . $di);
	if( $slot_type != 'none' )
	{
		$something_on = TRUE;
	}

	if( $slot_type == 'fixed' )
	{
		$removeValidation[] = 'starts_at_range_' . $di;
		$removeValidation[] = 'ends_at_range_' . $di;
	}
}

if( ! $something_on )
{
	ntsView::addAnnounce( M('Please choose at least 1 option'), 'error' );
	return;
//	$forwardTo = ntsLink::makeLink('-current-');
//	ntsView::redirect( $forwardTo );
//	exit;
}


if( $NTS_VIEW['form']->validate($removeValidation) )
{
	$formValues = $NTS_VIEW['form']->getValues();
	if( ! isset($formValues['max_capacity']) ){
		$formValues['max_capacity'] = 1;
	}

	$newBlocks = array();

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
		$newBlock['max_capacity'] = $formValues['max_capacity'];
		if( $newBlock['max_capacity'] > $newBlock['capacity'] ){
			$newBlock['max_capacity'] = $newBlock['capacity'];
		}
		$newBlock['min_from_now'] = $formValues['min_from_now'];
		$newBlock['max_from_now'] = $formValues['max_from_now'];
		$newBlock['valid_from'] = $formValues['valid_from'];
		$newBlock['valid_to'] = $formValues['valid_to'];

		for( $di = 0; $di <= 6; $di++ )
		{
			$this_block = $newBlock;
			$this_slot_type = $formValues['slot_type_' . $di];

			if( $this_slot_type == 'none' )
			{
				continue;
			}
			elseif( $this_slot_type == 'range' )
			{
				$this_block['starts_at'] = $formValues['starts_at_range_' . $di];
				$this_block['ends_at'] = $formValues['ends_at_range_' . $di];
				$this_block['selectable_every'] = $formValues['selectable_every_' . $di];
			}
			else
			{
				$this_block['starts_at'] = $formValues['starts_at_fixed_' . $di];
				$this_block['ends_at'] = 0;
				$this_block['selectable_every'] = 0;
			}

			$this_block['applied_on'] = $di;
			$this_block['week_applied_on'] = $formValues['week_applied_on_' . $di];
			$newBlocks[] = $this_block;
		}

		foreach( $newBlocks as $nb )
		{
			$tm2->addBlock( $nb );
		}
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