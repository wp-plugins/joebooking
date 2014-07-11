<?php
$object = ntsLib::getVar( 'admin/customers/edit::OBJECT' );
$other_id = $_NTS['REQ']->getParam('merge_to');

$cm =& ntsCommandManager::getInstance();
$cm->runCommand( $object, 'merge_to', array('to' => $other_id) );

if( $cm->isOk() )
{
	ntsView::addAnnounce( M('Customers') . ': ' . M('Merge') . ': ' . M('OK'), 'ok' );

/* continue to the list with anouncement */
	$forwardTo = ntsLink::makeLink( 
		'-current-/../edit',
		'',
		array(
			'_id'	=> $other_id
			)
		);
}
else
{
	$errorText = $cm->printActionErrors();
	ntsView::addAnnounce( $errorText, 'error' );

	$forwardTo = ntsLink::makeLink( 
		'-current-'
		);
}
ntsView::redirect( $forwardTo );
exit;
?>