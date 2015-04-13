<?php
$schEdit = ntsLib::getVar( 'admin/manage:schEdit' );
$tm2 = ntsLib::getVar('admin::tm2');

$groupId = ntsLib::getVar( 'admin/manage/schedules/edit::groupId' );
$blocks = ntsLib::getVar( 'admin/manage/schedules/edit::blocks' );

$ff =& ntsFormFactory::getInstance();
$formFile = dirname( __FILE__ ) . '/form';

$fParams = $blocks[0];
$takeFrom = array('applied_on', 'location_id', 'resource_id', 'service_id');
reset( $takeFrom );
foreach( $takeFrom as $tf )
	$fParams[$tf] = array();

reset( $blocks );
foreach( $blocks as $b ){
	reset( $takeFrom );
	foreach( $takeFrom as $tf )
		$fParams[$tf][] = $b[$tf];
}
reset( $takeFrom );
foreach( $takeFrom as $tf ){
	$fParams[$tf] = array_unique($fParams[$tf]);
}

$NTS_VIEW['form'] =& $ff->makeForm( $formFile, $fParams );

if( ! $action ){
	return;
}

/* update */
if( $NTS_VIEW['form']->validate() ){
	$formValues = $NTS_VIEW['form']->getValues();
	if( ! isset($formValues['max_capacity']) ){
		$formValues['max_capacity'] = 1;
	}

	$params = array(
		'resource_id'	=> $fParams['resource_id'],
		'location_id'	=> $formValues['location_id'],
		'service_id'	=> $formValues['service_id'],
		'applied_on'	=> $formValues['applied_on'],
		'week_applied_on'	=> $formValues['week_applied_on'],
		'capacity'		=> $formValues['capacity'],
		'max_capacity'		=> $formValues['max_capacity'],
		'valid_from'	=> $formValues['valid_from'],
		'valid_to'		=> $formValues['valid_to'],
		'starts_at'		=> $formValues['starts_at'],
		'min_from_now'	=> $formValues['min_from_now'],
		'max_from_now'	=> $formValues['max_from_now'],
		);
	if( $params['max_capacity'] > $params['capacity'] ){
		$params['max_capacity'] = $params['capacity'];
	}
	$params['ends_at'] = ( isset($formValues['ends_at']) ) ? $formValues['ends_at'] : 0;
	$params['selectable_every'] = ( isset($formValues['selectable_every']) ) ? $formValues['selectable_every'] : 0;
	$tm2->updateBlocks( $groupId, $params );
	ntsView::addAnnounce( M('Schedules') . ': ' . M('Update') . ': ' . M('OK'), 'ok' );

	$forwardTo = ntsLink::makeLink('-current-/..');
	ntsView::redirect( $forwardTo );
	exit;
	}
else {
	}
?>