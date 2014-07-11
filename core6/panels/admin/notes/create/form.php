<p>
<?php
echo $this->makeInput (
/* type */
	'textarea',
/* attributes */
	array(
		'id'		=> 'note',
		'attr'		=> array(
//			'cols'	=> 36,
			'rows'	=> 4,
			'style'	=> 'width: 90%;',
			),
		'default'	=> '',
		),
/* validators */
	array(
		)
	);
?>

<p>
<?php echo $this->makePostParams('-current-', 'create' ); ?>
<INPUT class="btn btn-default" TYPE="submit" VALUE="<?php echo M('Add'); ?>">
