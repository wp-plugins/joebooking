<?php
$startTs = $this->getValue('starts_at');
$oldDuration = $this->getValue('duration');
$startTime = $startTs + 60 * NTS_TIME_UNIT;
$endTime = $startTs + $this->getValue('max_duration');
$leadOut = $this->getValue('lead_out');

$tsOptions = array();
$NTS_VIEW['t']->setTimestamp( $startTime );
$ts = $NTS_VIEW['t']->getTimestamp();
while( $ts <= $endTime ){
	$tsOptions[] = array( $ts, $NTS_VIEW['t']->formatTime() );
	$NTS_VIEW['t']->modify( '+' . 60 * NTS_TIME_UNIT . ' seconds' );
	$ts = $NTS_VIEW['t']->getTimestamp();
	}

$NTS_VIEW['t']->setTimestamp( $startTs );
$viewStartTime = $NTS_VIEW['t']->formatTime();
$NTS_VIEW['t']->modify( '+' . $oldDuration . ' seconds' );
$viewEndTime = $NTS_VIEW['t']->formatTime();

if( $leadOut )
	$viewEndTime .= ' [+ ' . ntsTime::formatPeriod($leadOut) . ' ' . M('Clean Up') . ']';
?>
<?php echo $viewStartTime; ?> - 
<span id="<?php echo $this->formId; ?>endTime">
	<?php echo $viewEndTime; ?>
	<?php if( ! $this->readonly ) : ?>
		<a href="#" id="<?php echo $this->formId; ?>toggleEndTime"><?php echo M('Other end time?'); ?></a>
	<?php endif; ?>
</span>

<?php if( ! $this->readonly ) : ?>
<span style="display: none;" id="<?php echo $this->formId; ?>formEndTime">
<?php
echo $this->makeInput (
/* type */
	'select',
/* attributes */
	array(
		'id'		=> 'end_time',
		'options'	=> $tsOptions,
		)
	);
?>
<?php echo $this->makePostParams('-current-', 'endtime' ); ?>
<INPUT class="btn btn-default" TYPE="submit" VALUE="<?php echo M('Update'); ?>">
<a href="#" id="<?php echo $this->formId; ?>cancelEndTime"><?php echo M('Cancel'); ?></a>
</span>

<script type="text/javascript">
jQuery("#<?php echo $this->formId; ?>toggleEndTime").live("click", function() {
	jQuery("#<?php echo $this->formId; ?>cancelEndTime").show();
	jQuery("#<?php echo $this->formId; ?>endTime").hide();
	jQuery("#<?php echo $this->formId; ?>toggleEndTime").hide();
	jQuery("#<?php echo $this->formId; ?>formEndTime").show();
	return false;
	});
jQuery("#<?php echo $this->formId; ?>cancelEndTime").live("click", function() {
	jQuery("#<?php echo $this->formId; ?>cancelEndTime").hide();
	jQuery("#<?php echo $this->formId; ?>endTime").show();
	jQuery("#<?php echo $this->formId; ?>toggleEndTime").show();
	jQuery("#<?php echo $this->formId; ?>formEndTime").hide();
	return false;
	});
</script>
<?php endif; ?>