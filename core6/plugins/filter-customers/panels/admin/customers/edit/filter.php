<?php
global $NTS_CURRENT_USER;
$thisId = $NTS_CURRENT_USER->getId();
if( $thisId <= 0 )
	return;

/* get the resources */
$filter_ids = ntsPluginFilterCustumers_AllowCustomers();

/* filter */
$id = $_NTS['REQ']->getParam( '_id' );

if( ! in_array($id, $filter_ids) )
{
	$msg = M('Customer') . ': ' . M('View') . ': ' . M('Permission Denied');
	ntsView::addAnnounce( $msg, 'error' );

	/* continue */
	$forwardTo = ntsLink::makeLink();
	ntsView::redirect( $forwardTo );
	exit;
}
?>