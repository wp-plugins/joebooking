<?php
$jsDurationOptions = array();
reset( $durations );
foreach( $durations as $du )
{
	$jsDurationOptions[] = '[' . join(',', $du) . ']';
}
?>

<script language="JavaScript">
<?php for( $di = 0; $di <= 6; $di++ ) : ?>
	jQuery('#<?php echo $this->formId; ?>slot_type_<?php echo $di; ?>').live("change", function()
	{
		var what_now = jQuery('#<?php echo $this->formId; ?>slot_type_<?php echo $di; ?>:checked').val();
		switch( what_now )
		{
			case 'none':
				jQuery('#<?php echo $this->formId; ?>_details_range_<?php echo $di; ?>').hide();
				jQuery('#<?php echo $this->formId; ?>_details_fixed_<?php echo $di; ?>').hide();
				jQuery('#<?php echo $this->formId; ?>_details_week_<?php echo $di; ?>').hide();
				break;
			case 'range':
				jQuery('#<?php echo $this->formId; ?>_details_range_<?php echo $di; ?>').show();
				jQuery('#<?php echo $this->formId; ?>_details_fixed_<?php echo $di; ?>').hide();
				jQuery('#<?php echo $this->formId; ?>_details_week_<?php echo $di; ?>').show();
				break;
			case 'fixed':
				jQuery('#<?php echo $this->formId; ?>_details_range_<?php echo $di; ?>').hide();
				jQuery('#<?php echo $this->formId; ?>_details_fixed_<?php echo $di; ?>').show();
				jQuery('#<?php echo $this->formId; ?>_details_week_<?php echo $di; ?>').show();
				break;
		}
	});

	<?php if( $slotType[$di] == 'none' ) : ?>
		jQuery('#<?php echo $this->formId; ?>_details_range_<?php echo $di; ?>').hide();
		jQuery('#<?php echo $this->formId; ?>_details_fixed_<?php echo $di; ?>').hide();
		jQuery('#<?php echo $this->formId; ?>_details_week_<?php echo $di; ?>').hide();
	<?php elseif( $slotType[$di] == 'range' ) : ?>
		jQuery('#<?php echo $this->formId; ?>_details_range_<?php echo $di; ?>').show();
		jQuery('#<?php echo $this->formId; ?>_details_fixed_<?php echo $di; ?>').hide();
		jQuery('#<?php echo $this->formId; ?>_details_week_<?php echo $di; ?>').show();
	<?php else : ?>
		jQuery('#<?php echo $this->formId; ?>_details_range_<?php echo $di; ?>').hide();
		jQuery('#<?php echo $this->formId; ?>_details_fixed_<?php echo $di; ?>').show();
		jQuery('#<?php echo $this->formId; ?>_details_week_<?php echo $di; ?>').show();
	<?php endif; ?>
<?php endfor; ?>
</script>