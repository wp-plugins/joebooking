<?php
$timezoneOptions = ntsTime::getTimezones();
?>
<?php
//echo $this->makePostParams('-current-', 'timezone');
echo $this->makePostParams('customer/appointments/view', 'timezone');
?>
<ul class="list-inline">
	<li>
		<?php echo M('Timezone'); ?> 
	</li>
	<li>
		<?php
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
	</li>
	<li>
		<INPUT class="btn btn-default" TYPE="submit" VALUE="<?php echo M('Update'); ?>">
	</li>
</ul>