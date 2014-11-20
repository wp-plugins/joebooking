<?php
$tm2 = ntsLib::getVar('admin::tm2');
$cal = ntsLib::getVar( 'admin/manage/schedules:cal' );

$ff =& ntsFormFactory::getInstance();
$formFile = dirname( __FILE__ ) . '/form';
$fParams = array();
$NTS_VIEW['form'] =& $ff->makeForm( $formFile, $fParams );

if( $NTS_VIEW['form']->validate() )
{
	$formValues = $NTS_VIEW['form']->getValues();

	$schView = ntsLib::getVar( 'admin/manage:schView' );
	$schEdit = ntsLib::getVar( 'admin/manage:schEdit' );

	if( ! in_array($formValues['from-resource'],$schView) )
	{
		$msg = M('Schedules') . ': ' . M('View') . ': ' . M('Permission Denied');
		ntsView::addAnnounce( $msg, 'error' );

		/* continue */
		$forwardTo = ntsLink::makeLink( '-current-' );
		ntsView::redirect( $forwardTo );
		exit;
	}
	if( ! in_array($formValues['to-resource'],$schEdit) )
	{
		$msg = M('Schedules') . ': ' . M('Edit') . ': ' . M('Permission Denied');
		ntsView::addAnnounce( $msg, 'error' );

		/* continue */
		$forwardTo = ntsLink::makeLink( '-current-' );
		ntsView::redirect( $forwardTo );
		exit;
	}

	/* ok duplicate schedules */
	// first delete everything of to resource
	$whereTo = array(
		'resource_id'	=> array('=', $formValues['to-resource'] ),
		);
	$tm2->deleteBlocksByWhere( $whereTo );

	// now get ones of from resource
	$whereFrom = array(
		'resource_id'	=> array('=', $formValues['from-resource'] ),
		);
	$blocks = $tm2->getBlocksByWhere( $whereFrom );
	reset( $blocks );
	$newBlocks = array();
	foreach( $blocks as $b )
	{
		unset($b['group_id']);
		$b['resource_id'] = array($formValues['to-resource']);
		$newBlocks[] = $b;
	}
	reset( $newBlocks );
	foreach( $newBlocks as $b )
	{
		$tm2->addBlock( $b );
	}

	ntsView::addAnnounce( M('Schedules') . ': ' . M('Copy') . ': ' . M('OK'), 'ok' );
	$forwardTo = ntsLink::makeLink('-current-');
	ntsView::redirect( $forwardTo );
	exit;
}
else
{
/* form not valid, continue */
}
?>