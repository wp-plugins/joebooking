<?php
ntsLib::setVar( 'admin/manage/timeoff:ress', $cal );

/* redefine calendar */
$cal = $_NTS['REQ']->getParam( 'cal' );
ntsLib::setVar( 'admin/manage/timeoff:cal', $cal );

ntsLib::setVar( 'admin/manage/timeoff:own', TRUE );

ntsView::setBack( ntsLink::makeLink('admin/manage/schedules') );
?>