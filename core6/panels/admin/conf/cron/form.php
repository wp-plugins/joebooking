<?php
$conf =& ntsConf::getInstance();
$cronLastRun = $conf->get( 'cronLastRun' );
$cronEnabled = $conf->get( 'cronEnabled' );
?>
<table class="ntsForm">
<tr>
	<td class="ntsFormLabel"><?php echo M("Yes, I've configured the cron job"); ?></td>
	<td class="ntsFormValue">
<?php
	echo $this->makeInput (
	/* type */
		'checkbox',
	/* attributes */
		array(
			'id'	=> 'cronEnabled',
			)
		);
?>
	</td>
</tr>
<?php if( $cronLastRun ) : ?>
<tr>
	<td class="ntsFormLabel"><?php echo M("Cron Job Last Run"); ?></td>
	<td class="ntsFormValue">
<?php
	$t = new ntsTime();
	$t->setTimestamp( $cronLastRun );
	echo $t->formatFull();
?>
	</td>
</tr>
<?php endif; ?>

<?php if ($cronEnabled) : ?>
<tr>
	<td class="ntsFormLabel"><?php echo M('Send Reminder Before'); ?></td>
	<td class="ntsFormValue">
<?php
	echo $this->makeInput (
	/* type */
		'period/MinHourDayWeek',
	/* attributes */
		array(
			'id'	=> 'remindBefore',
			)
		);
?>
	</td>
</tr>

<tr>
	<td class="ntsFormLabel"><?php echo M('Auto Complete Appointments After'); ?></td>
	<td class="ntsFormValue">
<?php
	echo $this->makeInput (
	/* type */
		'period/MinHourDayWeek',
	/* attributes */
		array(
			'id'	=> 'autoComplete',
			'help'	=> M('Set to 0 to disable'),
			)
		);
?>
	</td>
</tr>

<tr>
	<td class="ntsFormLabel"><?php echo M('Auto Reject Appointments If Not Approved Within'); ?></td>
	<td class="ntsFormValue">
<?php
	echo $this->makeInput (
	/* type */
		'period/MinHourDayWeek',
	/* attributes */
		array(
			'id'	=> 'autoReject',
			'help'	=> M('Set to 0 to disable'),
			)
		);
?>
	</td>
</tr>

<?php endif; ?>

<tr>
<td>&nbsp;</td>
<td>
<?php echo $this->makePostParams('-current-', 'update'); ?>
<input class="btn btn-default" type="submit" value="<?php echo M('Save'); ?>">
</td>
</tr>
</table>