<?php
$class = 'customer';
$om =& objectMapper::getInstance();
$fields = $om->getFields( $class, 'internal' );
reset( $fields );

$conf =& ntsConf::getInstance();
$enableRegistration = $conf->get('enableRegistration');
?>

<?php foreach( $fields as $f ) : ?>
	<?php 
	if( ($f[0] == 'username') )
		continue;
	?>
	<?php
	$c = $om->getControl( $class, $f[0], false );
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
<?php endforeach; ?>

<?php if( NTS_ENABLE_TIMEZONES > 0 ) : ?>
	<?php
	$timezoneOptions = ntsTime::getTimezones();
	?>
	<?php
	echo ntsForm::wrapInput(
		M('Timezone'),
		$this->buildInput (
		/* type */
			'select',
		/* attributes */
			array(
				'id'		=> '_timezone',
				'options'	=> $timezoneOptions,
				'default'	=> NTS_COMPANY_TIMEZONE,
				)
			)
		);
	?>
<?php endif; ?>

<?php
$lm =& ntsLanguageManager::getInstance();
$languages = $lm->getActiveLanguages();
?>
<?php if( count($languages) > 1 ) : ?>
	<?php
	$languageOptions = array();
	reset( $languages );
	foreach( $languages as $lng )
	{
		$languageOptions[] = array( $lng, $lng );
	}
	?>
	<?php
	echo ntsForm::wrapInput(
		M('Language'),
		$this->buildInput(
		/* type */
			'select',
		/* attributes */
			array(
				'id'		=> '_lang',
				'options'	=> $languageOptions,
				)
			)
		);
	?>
<?php endif; ?>

<?php if( ! $enableRegistration ) : ?>
	<?php
	echo ntsForm::wrapInput(
		M('Login Details'),
		$this->buildInput(
		/* type */
			'checkbox',
		/* attributes */
			array(
				'id'		=> 'login-details',
				'default'	=> 0,
				)
			)
		);
	?>
<?php endif; ?>

<div id="<?php echo $this->getName(); ?>login-wrapper" style="margin-top: 2em;">
	<?php if( ! NTS_EMAIL_AS_USERNAME ) : ?>
		<?php
		$c = $om->getControl( $class, 'username', false );
		?>
		<?php
		echo ntsForm::wrapInput(
			$c[0],
			$this->buildInput(
				$c[1],
				$c[2],
				$c[3]
				)
			);
		?>
	<?php endif; ?>

	<p class="text-muted">
	<?php echo M('Leave these blank to autogenerate a random password'); ?>
	</p>

	<?php
	echo ntsForm::wrapInput(
		M('Password') . ' *',
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
				),
		/* validators */
			array(
				)
			)
		);
	?>


	<?php
	echo ntsForm::wrapInput(
		M('Confirm Password') . ' *',
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
				),
		/* validators */
			array(
				array(
					'code'		=> 'confirmPassword.php', 
					'error'		=> "Passwords don't match!",
					'params'	=> array(
						'mainPasswordField' => 'password',
						),
					),
				)
			)
		);
	?>
</div>

<div id="<?php echo $this->getName(); ?>notify-wrapper">
	<?php
	echo ntsForm::wrapInput(
		M('Send Notification'),
		$this->buildInput(
		/* type */
			'checkbox',
		/* attributes */
			array(
				'id'		=> 'notify',
				'default'	=> $enableRegistration ? 1 : 0,
				)
			)
		);
	?>
</div>

<?php
$params = array();
$params[NTS_PARAM_VIEW_MODE] = $NTS_VIEW[NTS_PARAM_VIEW_MODE];
echo $this->makePostParams('-current-', 'create-customer', $params);
?>

<?php
echo ntsForm::wrapInput(
	'',
	'<INPUT TYPE="submit" class="btn btn-default" VALUE="' . M('Customer') . ': ' . M('Create') . '">'
	);
?>

<?php if( NTS_ALLOW_NO_EMAIL && ($class == 'customer') ) : ?>
<script language="JavaScript">
jQuery(document).ready( function()
{
	if( jQuery("#<?php echo $this->getName(); ?>noEmail").is(":checked") )
	{
		jQuery("#<?php echo $this->getName(); ?>email").hide();
		jQuery("#<?php echo $this->getName(); ?>notify-wrapper").hide();
	}
	else
	{
		jQuery("#<?php echo $this->getName(); ?>email").show();
		jQuery("#<?php echo $this->getName(); ?>notify-wrapper").show();
	}
});
jQuery("#<?php echo $this->getName(); ?>noEmail").live( 'click', function()
{
	jQuery("#<?php echo $this->getName(); ?>email").toggle();
	jQuery("#<?php echo $this->getName(); ?>notify-wrapper").toggle();
});
</script>
<?php endif; ?>

<?php if( ! $enableRegistration ) : ?>
<script language="JavaScript">
jQuery(document).ready( function(){
	if( jQuery("#<?php echo $this->getName(); ?>login-details").is(":checked") )
	{
		jQuery("#<?php echo $this->getName(); ?>login-wrapper").show();
	}
	else
	{
		jQuery("#<?php echo $this->getName(); ?>login-wrapper").hide();
	}
});
jQuery("#<?php echo $this->getName(); ?>login-details").live( 'click', function()
{
	jQuery("#<?php echo $this->getName(); ?>login-wrapper").toggle();
});
</script>
<?php endif; ?>