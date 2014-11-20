<?php
$conf =& ntsConf::getInstance();
$useCaptcha = $conf->get( 'useCaptcha' );
$strongPassword = $conf->get( 'strongPassword' );
$enableRegistration = $conf->get('enableRegistration');

$om =& objectMapper::getInstance();
$fields = $om->getFields( 'customer', 'external' );
reset( $fields );
?>

<?php foreach( $fields as $f ) : ?>
	<?php
	if( $f[0] == 'username' )
		continue;
	?>
	<?php $c = $om->getControl( 'customer', $f[0], false ); ?>
	<?php
	if( isset($f[4]) )
	{
		if( $f[4] == 'read' )
		{
			$c[2]['readonly'] = 1;
		}
	}

	if( ! $enableRegistration )
	{
		if( $f[0] == 'email' )
		{
			/* traverse validators */
			reset( $c[3] );
			$copyVali = $c[3];
			$c[3] = array();
			foreach( $copyVali as $vali )
			{
				if( preg_match('/checkUserEmail\.php$/', $vali['code']) )
				{
					continue;
				}
				$c[3][] = $vali;
			}
		}
	}

	if( $c[2]['description'] )
		$c[2]['help'] = $c[2]['description'];
	echo ntsForm::wrapInput(
		$c[0],
		$this->buildInput (
			$c[1],
			$c[2],
			$c[3]
			)
		);
	?>

	<?php if( NTS_ALLOW_NO_EMAIL && ($c[2]['id'] == 'email') ) : ?>
		<?php
		echo ntsForm::wrapInput(
			'',
			$this->buildInput (
			/* type */
				'checkbox',
			/* attributes */
				array(
					'id'	=> 'noEmail',
					'label'	=> M('No Email') . '?'
					)
				)
			);
		?>
	<?php endif; ?>
<?php endforeach; ?>

<?php if( $enableRegistration ) : ?>
	<hr>
	<p>
	<h4><?php echo M('Login Details'); ?></h4>

	<?php if( ! NTS_EMAIL_AS_USERNAME ) : ?>
		<?php
		$control = $om->getControl( 'customer', 'username', false );
		if( isset($control[3]) && is_array($control[3]) )
		{
			$validators = $control[3];
		}
		else
		{
			$validators = array(
				array(
					'code'		=> 'notEmpty.php', 
					'error'		=> M('Required'),
					),
				array(
					'code'		=> 'checkUsername.php', 
					'error'		=> M('Already in use'),
					'params'	=> array(
						'skipMe'	=> 1,
						)
					),
				);
		}

		echo ntsForm::wrapInput(
			M('Desired Username'),
			$this->buildInput (
			/* type */
				'text',
			/* attributes */
				array(
					'id'		=> 'username',
					'attr'		=> array(
						'size'	=> 16,
						),
					'default'	=> '',
					'required'	=> 1,
					),
			/* validators */
				$validators
				)
			);
		?>
	<?php endif; ?>

	<?php
	$passwordValidate = array();
	$passwordValidate[] = array(
		'code'		=> 'notEmpty.php', 
		'error'		=> M('Required'),
		);
	if( $strongPassword ){
		$passwordValidate[] = array(
			'code'		=> 'strongPassword.php', 
			);
		}
	echo ntsForm::wrapInput(
		M('Password'),
		$this->buildInput(
		/* type */
			'password',
		/* attributes */
			array(
				'id'		=> 'password',
				'attr'		=> array(
					'size'	=> 16,
					),
				'default'	=> '',
				'required'	=> 1,
				),
		/* validators */
			$passwordValidate
			)
		);
	?>

	<?php
	echo ntsForm::wrapInput(
		M('Confirm Password'),
		$this->buildInput(
		/* type */
			'password',
		/* attributes */
			array(
				'id'		=> 'password2',
				'attr'		=> array(
					'size'	=> 16,
					),
				'default'	=> '',
				'required'	=> 1,
				),
		/* validators */
			array(
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
	<hr>
<?php endif; ?>

<?php if( $useCaptcha ) : ?>
	<?php
	echo ntsForm::wrapInput(
		M('Enter Code Shown'),
		$this->buildInput (
		/* type */
			'captcha',
		/* attributes */
			array(
				'id'	=> 'captcha',
				'attr'	=> array(
					'size'	=> 6
					)
				)
			)
		);
	?>
<?php endif; ?>

<?php echo $this->makePostParams('-current-', 'register' ); ?>

<?php
$btnTitle = M('Confirm');
echo ntsForm::wrapInput(
	'',
	'<INPUT NAME="nts-register" class="btn btn-default" TYPE="submit" VALUE="' . $btnTitle . '">'
	);
?>
