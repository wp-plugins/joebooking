<?php
global $NTS_VIEW;
if( isset($NTS_VIEW['output']) )
{
	echo $NTS_VIEW['output'];
}
else
{
	if( file_exists($NTS_VIEW['displayFile']) )
		require( $NTS_VIEW['displayFile'] );
}
?>