<?php if( isset($NTS_VIEW['display']) ) : ?>
	<?php require($NTS_VIEW['display']); ?>
<?php else : ?>
<?php
$conf =& ntsConf::getInstance();

$appInfo = ntsLib::getAppInfo();
$currentVersion = $appInfo['current_version'];
$dgtCurrentVersion = ntsLib::parseVersion($currentVersion);

$fileVersion = ntsLib::getAppVersion();
$dgtFileVersion = ntsLib::parseVersion($fileVersion);
?>

<h3><?php echo $appInfo['app']; ?> <?php echo $appInfo['installed_version']; ?></h3>

<?php
if( ! in_array(NTS_APP_LEVEL, array('lite')) && (! defined('NTS_APP_DEVELOPER')) )
{
	$NTS_VIEW['form']->display();
}
?>

<p>
<a class="btn btn-danger btn-xs" href="<?php echo ntsLink::makeLink('-current-', 'uninstall' ); ?>" onClick="return confirm('<?php echo M('Are you sure?'); ?>');">
	<?php echo M('Complete Uninstall'); ?>?
</a>

<a class="btn btn-warning btn-xs" href="<?php echo ntsLink::makeLink('-current-', 'deleteapps' ); ?>" onClick="return confirm('<?php echo M('Are you sure?'); ?>');">
	<?php echo M('Delete All Appointments'); ?>?
</a>
</p>

<p>
<strong><?php echo M('Uploaded Version'); ?></strong>:
<?php echo $fileVersion; ?>
</p>

<?php if( $dgtFileVersion > $dgtCurrentVersion ) : ?>
	<p>
		<a class="btn btn-info" href="<?php echo ntsLink::makeLink('-current-/../backup', 'make' ); ?>"><?php echo M('Download Backup'); ?></a> - highly recommended!
	</p>
	<p>
		<a class="btn btn-success btn-lg" href="<?php echo ntsLink::makeLink('-current-', 'upgrade' ); ?>"><?php echo M('Run Upgrade Procedure'); ?>: <?php echo $fileVersion; ?></a>
	</p>
<?php else: ?>
	<p>
		<?php echo M('No Upgrade Required'); ?>
	</p>
<?php endif; ?>

<p>
<strong>PHP</strong>:
<?php echo PHP_VERSION; ?>
</p>



<p>
	<strong><?php echo M('Current Version'); ?></strong>:
<?php
$myUrl = ntsLink::makeLinkFull( ntsLib::getFrontendWebpage() );
// strip started http:// as apache seems to have troubles with it
$myUrl = preg_replace( '/https?\:\/\//', '', $myUrl );

$checkUrl2 = ntsLib::checkLicenseUrl();
//echo $checkUrl2 . '<br>';
$installedVersionNumber = ntsLib::parseVersion( $currentVersion );
?>
<script language="JavaScript" type="text/javascript" src="<?php echo $checkUrl2; ?>">
</script>
<script language="JavaScript" type="text/javascript">
var currentVersionNumber = 0;
if( ntsVersion.length )
{
	var myV = ntsVersion.split( '.' );
	var currentVersion = myV[0] + '' + myV[1] + '' + ntsZeroFill(myV[2], 2);

	var currentVersionNumber = parseInt(currentVersion);
//	currentVersionNumber = currentVersionNumber + (<?php echo $appInfo['modify_version']; ?>);

	currentVersion = String(currentVersionNumber);
	currentVersion = ntsZeroFill(currentVersion, 4);

	ntsVersion = currentVersion.substring(0,1) + '.' + currentVersion.substring(1,2);
	var v3 = currentVersion.substring(2,4);
	v3 = parseInt(v3);
	v3 = String(v3);
	ntsVersion = ntsVersion + '.' + v3;

	document.write(ntsVersion);
}
</script>

<script language="JavaScript" type="text/javascript">
if( (currentVersionNumber > 1000) && (currentVersionNumber > <?php echo $dgtFileVersion; ?>) )
{
	<?php if( $_NTS['DOWNLOAD_URL'] ) : ?>
		document.write( '<a target="_blank" href="<?php echo $_NTS['DOWNLOAD_URL']; ?>">' );
	<?php endif; ?>
	document.write( "<?php echo M('Please Upgrade'); ?>" );
	<?php if( $_NTS['DOWNLOAD_URL'] ) : ?>
		document.write( '</a>' );
	<?php endif; ?>
}
</script>
</p>

<?php endif; ?>
