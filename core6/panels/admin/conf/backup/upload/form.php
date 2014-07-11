<p class="alert">
<?php echo M('Warning: your current data will be deleted by restoring from backup file'); ?>
</p>

<?php
echo ntsForm::wrapInput(
	M('Backup File'),
	$this->buildInput (
	/* type */
		'upload',
	/* attributes */
		array(
			'id'	=> 'file',
			)
		)
	);
?>

<?php echo $this->makePostParams('-current-', 'upload'); ?>

<?php
echo ntsForm::wrapInput(
	'',
	'<INPUT class="btn btn-info" TYPE="submit" VALUE="' . M('Restore') . '">'
	);
?>