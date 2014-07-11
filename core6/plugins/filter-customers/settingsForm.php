<?php
$plugin = 'filter-customers';
$new = $_NTS['REQ']->getParam( 'new' );
?>
<table class="ntsForm">
<tr>
	<td class="ntsFormLabel">Allow to view customers with no appointments</td>
	<td class="ntsFormValue">
<?php
	echo $this->makeInput (
	/* type */
		'checkbox',
	/* attributes */
		array(
			'id'		=> 'no_apps',
			'default'	=> 1,
			)
		);
?>
</td>
</tr>

</table>