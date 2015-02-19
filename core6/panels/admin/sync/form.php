<?php
$selectOptions = array(
	array( 'service', M('Service') ),
	array( 'customer', M('Customer') ),
	);
if( ! NTS_SINGLE_RESOURCE ){
	$selectOptions[] = array( 'resource', M('Bookable Resource') );
}
echo ntsForm::wrapInput(
	M('iCal Summary'),
	$this->buildInput (
	/* type */
		'select',
	/* attributes */
		array(
			'id'		=> 'icalSummary',
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