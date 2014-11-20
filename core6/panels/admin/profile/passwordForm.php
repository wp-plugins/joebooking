<?php
echo ntsForm::wrapInput(
	M('Password'),
	$this->buildInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'password',
			'attr'		=> array(
				'size'	=> 16,
				),
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> M('Required'),
				),
			)
		)
	);
?>
<?php
echo ntsForm::wrapInput(
	M('Confirm Password'),
	$this->buildInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'password2',
			'attr'		=> array(
				'size'	=> 16,
				),
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> M('Please confirm the password'),
				),
			array(
				'code'		=> 'confirmPassword.php', 
				'error'		=> M("Passwords don't match!"),
				'params'	=> array(
					'mainPasswordField' => 'password',
					),
				),
			)
		)
	);
?>

<?php echo $this->makePostParams('-current-', 'update_password' ); ?>
<?php
echo ntsForm::wrapInput(
	'',
	'<INPUT class="btn btn-default" TYPE="submit" VALUE="' . M('Change Password') . '">'
	);
?>