<?php
$ff =& ntsFormFactory::getInstance();
$groupId = ntsLib::getVar( 'admin/manage/schedules/edit::groupId' );
$tm2 = ntsLib::getVar('admin::tm2');

$formFile = dirname( __FILE__ ) . '/form';
$NTS_VIEW['form'] =& $ff->makeForm( $formFile );

switch( $action )
{
	case 'delete':
		$tm2->deleteBlocks( $groupId );
		ntsView::addAnnounce( M('Schedules') . ': ' . M('Update') . ': ' . M('OK'), 'ok' );

		$forwardTo = ntsLink::makeLink('-current-/../..');
		ntsView::redirect( $forwardTo );
		exit;
		break;
}
?>