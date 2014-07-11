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
		),
/* validators */
	array(
		)
	);
?>

<p>
<?php echo $this->makePostParams('-current-', 'update', array('noteid' => $this->getValue('noteid'))); ?>
<INPUT class="btn btn-default btn-sm" TYPE="submit" VALUE="<?php echo M('Update'); ?>">