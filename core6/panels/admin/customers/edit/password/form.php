<?php
$om =& objectMapper::getInstance();
$ri = ntsLib::remoteIntegration();
$current_user =& ntsLib::getCurrentUser();
$admin_level = $current_user->getProp('_admin_level');
$is_admin = ( $admin_level && ($admin_level != 'admin')) ? FALSE : TRUE;

$id = $this->getValue('id');

/* form params - used later for validation */
$this->setParams(
	array(
		'myId'	=> $id,
		)
	);

$ntsConf = ntsConf::getInstance();
$canEditLogin = TRUE;
$current_user =& ntsLib::getCurrentUser();
$admin_level = $current_user->getProp('_admin_level');
$staffCanEditCustomerLogin = $ntsConf->get('staffCanEditCustomerLogin');
if( ($admin_level != 'admin') && (! $staffCanEditCustomerLogin) ){
	$canEditLogin = FALSE;
}
?>
<?php if( NTS_EMAIL_AS_USERNAME ) : ?>
	<?php
	$c = $om->getControl( 'customer', 'email', false );
	$c[1] = 'labelData';
	?>
	<?php
	echo ntsForm::wrapInput(
		$c[0],
		$this->buildInput (
			$c[1],
			$c[2],
			$c[3]
			)
		);
	?>
<?php else : ?>
	<?php
	$c = $om->getControl( 'customer', 'username', false );
	if( $ri == 'wordpress' ){
		$c[1] = 'labelData';
	}
	if( ! $canEditLogin ){
		$c[1] = 'labelData';
	}
	?>
	<?php
	echo ntsForm::wrapInput(
		$c[0],
		$this->buildInput (
			$c[1],
			$c[2],
			$c[3]
			)
		);
	?>
<?php endif; ?>

<?php if( $canEditLogin ) : ?>
	<?php
	echo ntsForm::wrapInput(
		M('Password'),
		$this->buildInput (
		/* type */
			'password',
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
			'password',
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
<?php endif; ?>

<?php
if( $canEditLogin ){
	echo $this->makePostParams('-current-', 'update_password' );
	echo ntsForm::wrapInput(
		'',
		'<INPUT class="btn btn-default" TYPE="submit" VALUE="' . M('Change Password') . '">'
		);
}
?>
