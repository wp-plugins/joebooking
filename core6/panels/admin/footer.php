<?php
$ri = ntsLib::remoteIntegration();
?>
<!-- FOOTER -->
<div id="nts-footer">
<?php if( defined('NTS_APP_DEVELOPER') && NTS_APP_DEVELOPER ) : ?>
	<?php
	$wlFile = NTS_APP_DIR . '/../developer.php';
	if( file_exists($wlFile) )
	{
		require( $wlFile );
	}
	?>
<?php elseif( NTS_APP_WHITELABEL ) : ?>
	<?php
	$wlFile = NTS_APP_DIR . '/../whitelabel.php';
	if( file_exists($wlFile) )
	{
		require( $wlFile );
	}
	?>
<?php elseif( ! $ri ) : ?>
	<?php
	$app = ntsLib::getAppInfo();
	$currentYear = date('Y');
	?>
	&copy; 2010-<?php echo $currentYear; ?> <a href="http://www.hitappoint.com/"><b>hitAppoint <?php echo ucfirst(NTS_APP_LEVEL); ?></b></a> ver. <?php echo $app['current_version']; ?>
<?php endif; ?>

<?php
$conf =& ntsConf::getInstance();
$theme = $conf->get( 'theme' );
$themeFolder = NTS_EXTENSIONS_DIR . '/themes/' . $theme;
$adminFooterFile = $themeFolder . '/admin-footer.php';
if( file_exists($adminFooterFile) ){
	require( $adminFooterFile );
	}
?>
</div>