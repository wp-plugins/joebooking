<?php
$plm =& ntsPluginManager::getInstance();
$plugin = $this->getValue('plugin');
$new = $this->getValue('new');

$plgFolder = $plm->getPluginFolder( $plugin );
$formFile = $plgFolder . '/settingsForm.php';
$skipSubmit = false;
require( $formFile );
?>

<?php if( ! $skipSubmit ) : ?>
	<?php if( $new ) : ?>
		<?php echo $this->makePostParams('-current-', 'activate', array('plugin' => $plugin, 'new' => $new) ); ?>
		<?php
		echo ntsForm::wrapInput(
			'',
			'<INPUT class="btn btn-default" TYPE="submit" VALUE="' . M('Install') . '">'
			);
		?>
	<?php else : ?>
		<?php echo $this->makePostParams('-current-', 'update', array('plugin' => $plugin, 'new' => $new) ); ?>
		<?php
		echo ntsForm::wrapInput(
			'',
			'<INPUT class="btn btn-default" TYPE="submit" VALUE="' . M('Update') . '">'
			);
		?>
	<?php endif; ?>
<?php endif; ?>