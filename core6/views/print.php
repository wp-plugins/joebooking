<?php
$ri = ntsLib::remoteIntegration();
$i_need_footer = FALSE;
if( 1 OR ! $ri ){
	if( isset($NTS_VIEW['headFile']) && $NTS_VIEW['headFile'] && file_exists($NTS_VIEW['headFile']) )
		require( $NTS_VIEW['headFile'] );
	else
		require( dirname(__FILE__) . '/head.php' );
	$i_need_footer = TRUE;
}
?>

<?php global $NTS_VIEW, $NTS_CURRENT_USER; ?>
<!-- HEADER -->
<?php if( isset($NTS_VIEW['headerFile']) && file_exists($NTS_VIEW['headerFile']) ) : ?>
	<?php require( $NTS_VIEW['headerFile'] ); ?>
<?php endif; ?>

<?php
$ri = ntsLib::remoteIntegration();
$container = $ri ? 'container-fluid' : 'container';
?>

<div id="nts">
<div class="<?php echo $container; ?>">

<!-- DISPLAY PAGE -->
<?php
if( isset($NTS_VIEW['output']) ){
	echo $NTS_VIEW['output'];
}
else {
	if( file_exists($NTS_VIEW['displayFile']) )
		require( $NTS_VIEW['displayFile'] );
}
?>

</div>
</div><!-- end of #nts -->

<script language="Javascript1.2">
<!--
window.print();
//-->
</script>

<?php
if( $i_need_footer )
	require( dirname(__FILE__) . '/footer.php' );
?>