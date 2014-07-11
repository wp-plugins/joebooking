<?php
$params = array(
	'smtpSecure',
	'smtpHost',
	'smtpUser',
	'smtpPass',
	'emailSentFrom',
	'emailSentFromName',
//	'emailDebug',
	'emailDisabled',
	'emailCommonHeader',
	'emailCommonFooter'
	);
$myDir = dirname(__FILE__);
require( dirname(__FILE__) . '/../action_common.php' );
?>