<?php
$params = array(
	'cronEnabled',
	'remindBefore',
	'autoComplete',
	'autoReject',
	);
$myDir = dirname(__FILE__);
$getBack = true;
ntsView::setBack( ntsLink::makeLink('admin/conf/cron') );
require( dirname(__FILE__) . '/../action_common.php' );
?>