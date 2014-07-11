<?php
$conf =& ntsConf::getInstance();

/* current plugins */
$plm =& ntsPluginManager::getInstance();
$activePlugins = $plm->getActivePlugins();

/* available */
$available = array();
$allPlugins = $plm->getPlugins();
reset( $allPlugins );
foreach( $allPlugins as $plg ){
	if( ! in_array($plg, $activePlugins) )
		$available[] = $plg;
	}
?>
<p>
<h3><?php echo M('Active'); ?></h3>

<?php if( count($activePlugins) > 0 ) : ?>
	<ul>
	<?php foreach($activePlugins as $plg ) : ?>
	<li style="margin-bottom: 1em;">
		<?php
		$infoFile = $plm->getPluginFolder($plg) . '/info.php';
		require( $infoFile );
		?>
		<b><?php echo $pluginName; ?></b>
		<br>
		<?php echo $pluginDescription; ?>
		<br>
	<?php
		$settings = true;
		$plgSettingsFile = $plm->getPluginFolder( $plg ) . '/settingsForm.php';
		if( ! file_exists($plgSettingsFile) )
			$settings = false;
	?>
		<?php if( $settings ) : ?>
			<a href="<?php echo ntsLink::makeLink('-current-/settings', '', array('plugin' => $plg) ); ?>"><?php echo M('Settings'); ?></a>
		<?php endif; ?>

		<a href="<?php echo ntsLink::makeLink('-current-', 'disable', array('plugin' => $plg) ); ?>"><?php echo M('Uninstall'); ?></a>

	<?php
		$missing = false;
		$plgFolder = $plm->getPluginFolder( $plg );
		if( ! file_exists($plgFolder) )
			$missing = true;
	?>
		<?php if( $missing ) : ?>
			<br>
			<b class="alert">Mod folder missing! It is not active now.</b>
		<?php endif; ?>
		
	</li>
	<?php endforeach; ?>
	</ul>
<?php else: ?>
	<?php echo M('None'); ?>
<?php endif; ?>

<p>
<h3><?php echo M('Available'); ?></h3>
<?php if( count($available) > 0 ) : ?>
	<ul>
	<?php foreach( $available as $av ) : ?>
		<li style="margin-bottom: 1em;">
		<?php
		$infoFile = $plm->getPluginFolder($av) . '/info.php';
		require( $infoFile );		
		?>
		<b><?php echo $pluginName; ?></b>
		<br>
		<?php echo $pluginDescription; ?>
		<br>
		<?php
		$require = ntsLib::parseVersionNumber( $requireVersion );
		$appInfo = ntsLib::getAppInfo();
		$systemVersion = ntsLib::parseVersionNumber( $appInfo['core_version'] );
		?>
		<?php if( $systemVersion < $require ) : ?>
			<span class="alert">Error: this plugin requires core version <?php echo $requireVersion; ?>
			<br>
		<?php else : ?>
			<a href="<?php echo ntsLink::makeLink('-current-/settings', '', array('plugin' => $av, 'new' => 1) ); ?>"><?php echo M('Install'); ?></a>
		<?php endif; ?>
		</li>
	<?php endforeach; ?>
	</ul>
<?php else: ?>
	<?php echo M('None'); ?>
<?php endif; ?>
