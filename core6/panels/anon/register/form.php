<div class="page-header">
	<H2><?php echo M('Register'); ?></H2>
</div>

<?php
$conf =& ntsConf::getInstance();
$useCaptcha = $conf->get( 'useCaptcha' );
$strongPassword = $conf->get( 'strongPassword' );

$om =& objectMapper::getInstance();
$fields = $om->getFields( 'customer', 'external' );
reset( $fields );
?>

<table>
<?php foreach( $fields as $f ) : ?>
	<?php
	if( $f[0] == 'username' )
		continue;
	?>
	<?php $c = $om->getControl( 'customer', $f[0], false ); ?>
	<?php
	if( 
		($f[4] == 'read') &&
		( ! strlen($f[3]) )
		)
	{
		continue;
	}
	?>
	<?php
	if( isset($f[4]) ){
		if( $f[4] == 'read' ){
			$c[2]['readonly'] = 1;
			}
		}
	?>
	<?php
	if( $c[2]['description'] )
	{
		$c[2]['help'] = $c[2]['description'];
	}

	if( NTS_ALLOW_NO_EMAIL && ($c[2]['id'] == 'email') )
	{
		$c[2]['after']	= '';
		$c[2]['after']	.= '<div class="checkbox">';
		$c[2]['after']		.= '<label>';
		$c[2]['after']		.= $this->makeInput (
								/* type */
									'checkbox',
								/* attributes */
									array(
										'id'	=> 'noEmail',
										)
									);
		$c[2]['after']		.= ' ' . M('No Email');
		$c[2]['after']		.= '</label>';
		$c[2]['after']	.= '</div>';
	}

	echo ntsForm::wrapInput(
		$c[0],
		$this->buildInput (
			$c[1],
			$c[2],
			$c[3]
			)
		);
	?>
<?php endforeach; ?>

<?php if( NTS_ENABLE_TIMEZONES > 0 ) : ?>
	<tr>
		<th><?php echo M('My Timezone'); ?></th>
		<td>
		<?php
		$timezoneOptions = ntsTime::getTimezones();
		echo $this->makeInput (
		/* type */
			'select',
		/* attributes */
			array(
				'id'		=> '_timezone',
				'options'	=> $timezoneOptions,
				'default'	=> NTS_COMPANY_TIMEZONE
				)
			);
		?>
		</td>
	</tr>
<?php endif; ?>
</table>

<p>
<h3><?php echo M('Login details'); ?></h3>

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
				'error'		=> M('Required field'),
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
	'error'		=> M('Required field'),
	);
if( $strongPassword )
{
	$passwordValidate[] = array(
		'code'		=> 'strongPassword.php', 
		);
}
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
	M('Password'),
	$this->buildInput (
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
echo ntsForm::wrapInput(
	'',
	'<INPUT class="btn btn-default" TYPE="submit" VALUE="' . M('Register') . '">'
	);
?>

<?php if( NTS_ALLOW_NO_EMAIL ) : ?>
<script language="JavaScript">
jQuery(document).ready( function()
{
	if( jQuery("#<?php echo $this->getName(); ?>noEmail").is(":checked") )
	{
		jQuery("#<?php echo $this->getName(); ?>email").hide();
	}
	else
	{
		jQuery("#<?php echo $this->getName(); ?>email").show();
	}
});
jQuery("#<?php echo $this->getName(); ?>noEmail").live( 'click', function()
{
	jQuery("#<?php echo $this->getName(); ?>email").toggle();
});
</script>
<?php endif; ?>