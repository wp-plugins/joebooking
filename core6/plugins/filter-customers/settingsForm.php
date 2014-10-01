<?php
$plugin = 'filter-customers';
$new = $_NTS['REQ']->getParam( 'new' );
?>

<?php
echo ntsForm::wrapInput(
	'Allow to view customers with no appointments',
	$this->buildInput(
	/* type */
		'checkbox',
	/* attributes */
		array(
			'id'		=> 'no_apps',
			'default'	=> 1,
			)
		)
	);
?>