<?php
$plm =& ntsPluginManager::getInstance();
$plgFolder = $plm->getPluginFolder( 'limits' );
$formFile = $plgFolder . '/settingsForm.php';
require( $formFile );
?>
<p>
<DIV CLASS="buttonBar">
<?php echo $this->makePostParams('-current-', 'update'); ?>
<INPUT class="btn btn-default" TYPE="submit" VALUE="<?php echo M('Update'); ?>">
</DIV>