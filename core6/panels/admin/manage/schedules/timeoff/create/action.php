<?php
$schEdit = ntsLib::getVar( 'admin/manage:schEdit' );
$tm2 = ntsLib::getVar('admin::tm2');
$cm =& ntsCommandManager::getInstance();

$t = $NTS_VIEW['t'];
$ntsdb =& dbWrapper::getInstance();

$ff =& ntsFormFactory::getInstance();
$formFile = dirname( __FILE__ ) . '/form';
$fParams = array();

$cal = $_NTS['REQ']->getParam('cal');
if( ! $cal )
{
	$t->setNow();
	$cal = $t->formatDate_Db();
}

$fParams['starts_at_date'] = $cal;
$fParams['ends_at_date'] = $cal;

$NTS_VIEW['form'] =& $ff->makeForm( $formFile, $fParams );

if( ! $action ){
	return;
	}

if( $NTS_VIEW['form']->validate() ){
	$formValues = $NTS_VIEW['form']->getValues();
	$resId = $formValues['resource_id'];
	$iCanEdit = in_array($resId, $schEdit);

	if( ! $iCanEdit ){
		ntsView::addAnnounce( M('Timeoff') . ': ' . M('Add') . ': ' . M('Permission Denied'), 'error' );
		}
	else {
		$new = array();
		$new['resource_id'] = $formValues['resource_id'];
		$new['location_id'] = 0;
		$new['starts_at'] = $t->timestampFromDbDate( $formValues['starts_at_date'] ) + $formValues['starts_at_time'];
		$new['ends_at'] = $t->timestampFromDbDate( $formValues['ends_at_date'] ) + $formValues['ends_at_time'];
		$new['description'] = $formValues['description'];
		
		$object = ntsObjectFactory::get( 'timeoff' );
		$object->setByArray( $new );

		$cm->runCommand( $object, 'create' );

		if( $cm->isOk() ){
			ntsView::addAnnounce( M('Timeoff') . ': ' . M('Add') . ': ' . M('OK'), 'ok' );
			}
		else {
			$errorText = $cm->printActionErrors();
			ntsView::addAnnounce( $errorText, 'error' );
			}
		}

	$params = array(
		NTS_PARAM_VIEW_RICH => '-reset-'
		);
	$forwardTo = ntsLink::makeLink('-current-/../..', '', $params);
	ntsView::redirect( $forwardTo );
	exit;
	}
else {
	}
?>