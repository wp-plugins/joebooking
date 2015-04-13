<?php
$app_info = ntsLib::getAppInfo();
$duration2 = $this->getValue('duration2');
?>
<?php
echo ntsForm::wrapInput(
	M('Title'),
	$this->buildInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'title',
			'attr'		=> array(
				'size'	=> 32,
				),
			'default'	=> '',
			'required'	=> 1,
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> M('Required'),
				),
			array(
				'code'		=> 'checkUniqueProperty.php', 
				'error'		=> M('Already in use'),
				'params'	=> array(
					'prop'	=> 'title',
					'class'	=> 'service',
					),
				),
			)
		)
	);
?>

<?php
echo ntsForm::wrapInput(
	M('Description'),
	$this->buildInput (
	/* type */
		'textarea',
	/* attributes */
		array(
			'id'		=> 'description',
			'attr'		=> array(
				'cols'	=> 32,
				'rows'	=> 6,
				),
			'default'	=> '',
			),
	/* validators */
		array(
			)
		)
	);
?>

<?php
echo ntsForm::wrapInput(
	M('Duration'),
	$this->buildInput (
	/* type */
		'period/LongHourMinute',
	/* attributes */
		array(
			'id'	=> 'duration',
			),
	/* validators */
		array(
			array(
				'code'		=> 'greaterThan.php', 
				'error'		=> M('Required'),
				'params'	=> array(
					'compareWith'	=> 0,
					)
				),
			)
		)
	);
?>

<div id="<?php echo $this->formId; ?>processing-time-hidden">
<?php
echo ntsForm::wrapInput(
	'',
	'<a class="btn btn-default btn-sm" href="#" id="' . $this->formId . 'processing-time-show">' . M('Processing Time') . ': ' . M('Add') . '</a>'
	);
?>
</div>

<div id="<?php echo $this->formId; ?>processing-time-shown">
<?php
echo ntsForm::wrapInput(
	M('Processing Time'),
	$this->buildInput (
	/* type */
		'period/HourMinute',
	/* attributes */
		array(
			'id'	=> 'duration_break',
			),
	/* validators */
		array(
			)
		)
	);
?>

<?php
echo ntsForm::wrapInput(
	M('Finish Duration'),
	$this->buildInput (
	/* type */
		'period/HourMinute',
	/* attributes */
		array(
			'id'	=> 'duration2',
			),
	/* validators */
		array(
			)
		)
	);
?>

<?php
echo ntsForm::wrapInput(
	'',
	'<a class="btn btn-default btn-sm" href="#" id="' . $this->formId . 'processing-time-hide">' . M('Processing Time') . ': ' . M('Remove') . '</a>'
	);
?>
</div>

<?php
echo ntsForm::wrapInput(
	M('Clean Up'),
	$this->buildInput (
	/* type */
		'period/HourMinute',
	/* attributes */
		array(
			'id'	=> 'lead_out',
			)
		)
	);
?>

<?php
global $NTS_CURRENT_USER;
?>
<?php if( ! $NTS_CURRENT_USER->isPanelDisabled('admin/payments') ) : ?>
	<?php
	echo ntsForm::wrapInput(
		M('Price'),
		$this->buildInput (
		/* type */
			'text',
		/* attributes */
			array(
				'id'	=> 'price',
				'attr'	=> array(
					'size'	=> 4,
					)
				)
			)
		);
	?>
<?php endif; ?>

<?php if( ! NTS_SINGLE_RESOURCE ) : ?>
	<?php
	echo ntsForm::wrapInput(
		M('Occupies Entire Location'),
		$this->buildInput (
		/* type */
			'checkbox',
		/* attributes */
			array(
				'id'	=> 'blocks_location',
				)
			)
		);
	?>
<?php endif; ?>


<?php echo $this->makePostParams('-current-', 'create'); ?>
<?php
echo ntsForm::wrapInput(
	'',
	'<INPUT class="btn btn-default" TYPE="submit" VALUE="' . M('Create') . '">'
	);
?>

<script language="JavaScript">
jQuery(document).on( 'click', '#<?php echo $this->formId; ?>processing-time-hide', function(event)
{
	jQuery("#<?php echo $this->formId; ?>processing-time-hidden").show();
	jQuery("#<?php echo $this->formId; ?>processing-time-shown").hide();
	jQuery("#<?php echo $this->formId; ?>nts-duration_break_qty_hour").val(0);
	jQuery("#<?php echo $this->formId; ?>nts-duration_break_qty_min").val(0);
	jQuery("#<?php echo $this->formId; ?>nts-duration2_qty_hour").val(0);
	jQuery("#<?php echo $this->formId; ?>nts-duration2_qty_min").val(0);
	return false;
});
jQuery(document).on( 'click', '#<?php echo $this->formId; ?>processing-time-show', function(event)
{
	jQuery("#<?php echo $this->formId; ?>processing-time-hidden").hide();
	jQuery("#<?php echo $this->formId; ?>processing-time-shown").show();
	return false;
});

jQuery(document).ready( function()
{
<?php if( $duration2 ) : ?>
	jQuery("#<?php echo $this->formId; ?>processing-time-hidden").hide();
	jQuery("#<?php echo $this->formId; ?>processing-time-shown").show();
<?php else : ?>
	jQuery("#<?php echo $this->formId; ?>processing-time-hidden").show();
	jQuery("#<?php echo $this->formId; ?>processing-time-shown").hide();
	jQuery("#<?php echo $this->formId; ?>nts-duration_break_qty_hour").val(0);
	jQuery("#<?php echo $this->formId; ?>nts-duration_break_qty_min").val(0);
	jQuery("#<?php echo $this->formId; ?>nts-duration2_qty_hour").val(0);
	jQuery("#<?php echo $this->formId; ?>nts-duration2_qty_min").val(0);
<?php endif; ?>
});
</script>