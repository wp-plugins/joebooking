<?php
$app = ntsLib::getAppProduct();

if( isset($_REQUEST['nts-integrate-url']) )
{
	$from = $_REQUEST['nts-integrate-url'];
	$from = ntsLib::restoreUrl( $from );

	$GLOBALS['NTS_CONFIG'][$app]['BASE_URL'] = $from;
	$GLOBALS['NTS_CONFIG'][$app]['INDEX_PAGE'] = '';
}
elseif( isset($_REQUEST['nts-integrate-file']) )
{
	$from = $_REQUEST['nts-integrate-file'];
	$from = ntsLib::restoreUrl( $from );

	$GLOBALS['NTS_CONFIG'][$app]['FRONTEND_WEBPAGE'] = $from;
}

global $NTS_VIEW;
$NTS_VIEW['isInside'] = 1;
$NTS_VIEW['called_remotely'] = 1;

ob_start();
require( dirname(__FILE__) . '/controller.php' );

if( ntsLib::isAjax() )
{
	$out['content'] = ob_get_contents();
	ob_end_clean();
}
else
{
	ob_start();
	require( dirname(__FILE__) . '/views/head-content.php' );
	$out['head'] = ob_get_contents();
	ob_end_clean();

	ob_start();
	require( dirname(__FILE__) . '/views/normal-content.php' );
	$out['content'] = ob_get_contents();
	ob_end_clean();
}

$out = json_encode( $out );
echo $out;
?>