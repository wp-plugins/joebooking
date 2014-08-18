<script language="JavaScript">
jQuery('#<?php echo $this->formId; ?>when').live("change", function() {
<?php foreach( $when as $wh ) : ?>
	jQuery('#<?php echo $this->formId; ?>_when_<?php echo $wh; ?>').toggle();
<?php endforeach; ?>
	});
	
jQuery('#<?php echo $this->formId; ?>slot_type').live("change", function() {
	jQuery('#<?php echo $this->formId; ?>_details_range').toggle();
	jQuery('#<?php echo $this->formId; ?>_details_fixed').toggle();
	});

<?php if( $slotType == 'range' ) : ?>
jQuery('#<?php echo $this->formId; ?>_details_range').show();
jQuery('#<?php echo $this->formId; ?>_details_fixed').hide();
<?php else : ?>
jQuery('#<?php echo $this->formId; ?>_details_range').hide();
jQuery('#<?php echo $this->formId; ?>_details_fixed').show();
<?php endif; ?>
</script>