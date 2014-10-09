<?php
$theme_file = $NTS_VIEW['isTheme'];

ob_start();
require( dirname(__FILE__) . '/head-content.php' );
$out['head'] = ob_get_contents();
ob_end_clean();

ob_start();
require( dirname(__FILE__) . '/normal-content.php' );
$out['content'] = ob_get_contents();
ob_end_clean();

ob_start();
require( $theme_file );
$return = ob_get_contents();
ob_end_clean();

$pos1 = strpos( $return, '<head' );
if( $pos1 !== FALSE )
{
	$pos2 = strpos( $return, '>', $pos1 );
	$return = str_replace( '</head>', $out['head'] . '</head>', $return );
}

$pos1 = strpos( $return, '[hitappoint]' );
if( $pos1 !== FALSE )
{
	$return = substr_replace( $return, $out['content'], $pos1, strlen('[hitappoint]') );
}
echo $return;
?>