<?php
global $_NTS, $NTS_VIEW, $NTS_CURRENT_USER;

$ri = ntsLib::remoteIntegration();
$i_need_footer = FALSE;
$i_inside = isset($NTS_VIEW['isInside']) ? $NTS_VIEW['isInside'] : FALSE;

if( ! $ri )
{
	if( isset($NTS_VIEW['headFile']) && $NTS_VIEW['headFile'] && file_exists($NTS_VIEW['headFile']) )
	{
		ob_start();
		require( dirname(__FILE__) . '/head.php' );
		$head = ob_get_contents();
		ob_end_clean();

		ob_start();
		require( $NTS_VIEW['headFile'] );
		$append_head = ob_get_contents();
		ob_end_clean();

		$head = str_replace( '<head>', '<head>' . $append_head, $head );
		echo $head;
	}
	else
	{
		require( dirname(__FILE__) . '/head.php' );
	}
	$i_need_footer = TRUE;
}
?>
<!-- HEADER -->
<?php if( isset($NTS_VIEW['headerFile']) && file_exists($NTS_VIEW['headerFile']) ) : ?>
	<?php 
	require( $NTS_VIEW['headerFile'] );
	?>
<?php endif; ?>

<?php
require( dirname(__FILE__) . '/normal-content.php' );
?>

<!-- FOOTER IF ANY -->
<?php if( isset($NTS_VIEW['footerFile']) && file_exists($NTS_VIEW['footerFile']) ) : ?>
	<?php require( $NTS_VIEW['footerFile'] ); ?>
<?php endif; ?>

<?php
if( $i_need_footer )
	require( dirname(__FILE__) . '/footer.php' );
?>