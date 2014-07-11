<?php
$groupId = $_NTS['REQ']->getParam( 'gid' );
ntsView::setPersistentParams( array('gid' => $groupId), 'admin/manage/schedules/edit' );

$tm2 = ntsLib::getVar('admin::tm2');
$blocks = $tm2->getBlocksByGroupId( $groupId );
ntsLib::setVar( 'admin/manage/schedules/edit::blocks', $blocks );
ntsLib::setVar( 'admin/manage/schedules/edit::groupId', $groupId );
?>