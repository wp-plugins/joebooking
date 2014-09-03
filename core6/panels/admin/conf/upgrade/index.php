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
if( $NTS_VIEW['form'] )
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

<?php
if( $NTS_VIEW['form'] && file_exists(dirname(__FILE__) . '/index_version.php') )
{
	require( dirname(__FILE__) . '/index_version.php' ); 
}
?>

<?php endif; ?>
