<?php if( ! NTS_EMAIL_AS_USERNAME ) : ?>
	<?php
	echo ntsForm::wrapInput(
		M('Username'),
		$this->buildInput (
		/* type */
			'text',
		/* attributes */
			array(
				'id'		=> 'login_username',
				'attr'		=> array(
					'size'	=> 20,
					),
				)
			)
		);
	?>

<?php else : ?>

	<?php
	echo ntsForm::wrapInput(
		M('Email'),
		$this->buildInput (
		/* type */
			'text',
		/* attributes */
			array(
				'id'		=> 'login_email',
				'attr'		=> array(
					'size'	=> 20,
					),
				)
			)
		);
	?>

<?php endif; ?>

<?php
echo ntsForm::wrapInput(
	M('Password'),
	$this->buildInput (
	/* type */
		'password',
	/* attributes */
		array(
			'id'		=> 'login_password',
			'attr'		=> array(
				'size'	=> 20,
				),
			)
		)
	);
?>

<?php
echo ntsForm::wrapInput(
	M('Remember Me'),
	$this->buildInput (
	/* type */
		'checkbox',
	/* attributes */
		array(
			'id'	=> 'remember',
			)
		)
	);
?>

<?php echo $this->makePostParams('-current-', 'login' ); ?>
<?php
echo ntsForm::wrapInput(
	'',
	'<INPUT NAME="nts-login" class="btn btn-default" TYPE="submit" VALUE="' . M('Login') . '">'
	);
?>

<input type="hidden" name="nts-skip-cookie" value="1">
