<?php
$timezoneOptions = ntsTime::getTimezones();
echo $this->makeInput (
/* type */
	'select',
/* attributes */
	array(
		'id'		=> 'tz',
		'options'	=> $timezoneOptions,
		)
	);
?>
<?php echo $this->makePostParams('-current-', 'timezone'); ?>
<INPUT class="btn btn-default" TYPE="submit" VALUE="<?php echo M('Update'); ?>">