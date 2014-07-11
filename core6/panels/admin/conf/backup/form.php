<?php
$selectOptions = array(
	array( 0, M('No') ),
	array( 7 * 24 * 60 * 60, '7 ' . M('Days') ),
	array( 14 * 24 * 60 * 60, '14 ' . M('Days') ),
	array( 30 * 24 * 60 * 60, '30 ' . M('Days') ),
	);
echo ntsForm::wrapInput(
	M('Backup Reminder'),
	$this->buildInput (
	/* type */
		'select',
	/* attributes */
		array(
			'id'		=> 'remindOfBackup',
			'options'	=> $selectOptions
			)
		)
	);
?>
<?php echo $this->makePostParams('-current-', 'update'); ?>
<?php
echo ntsForm::wrapInput(
	'',
	'<input class="btn btn-default" type="submit" value="' . M('Save') . '">'
	);
?>