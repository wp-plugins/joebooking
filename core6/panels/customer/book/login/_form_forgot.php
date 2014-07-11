<?php
echo ntsForm::wrapInput(
	M('Email'),
	$this->buildInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'email',
			'attr'		=> array(
				'size'	=> 20,
				),
			),
	/* validators */
		array(
			)
		)
	);
?>

<?php echo $this->makePostParams('-current-', 'reset' ); ?>

<?php
echo ntsForm::wrapInput(
	'',
	'<INPUT class="btn btn-default" TYPE="submit" VALUE="' . M('Send New Password') . '">'
	);
?>