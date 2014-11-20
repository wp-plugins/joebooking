<?php
/* tags */
$tm =& ntsEmailTemplateManager::getInstance();
$tags = $tm->getTags( 'common-header-footer' );
?>

<?php
echo ntsForm::wrapInput(
	M('From') . ': ' . M('Email'),
	$this->buildInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'emailSentFrom',
			'attr'		=> array(
				'size'	=> 32,
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
	M('From') . ': ' . M('Name'),
	$this->buildInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'emailSentFromName',
			'attr'		=> array(
				'size'	=> 48,
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
	M('Disable Email'),
	$this->buildInput (
	/* type */
		'checkbox',
	/* attributes */
		array(
			'id'	=> 'emailDisabled',
			)
		)
	);
?>

<p>
<table>
<tr>
	<th><?php echo M('Header For All Emails'); ?></th>
	<th><?php echo M('Tags'); ?></th>
</tr>
<tr>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'textarea',
	/* attributes */
		array(
			'id'		=> 'emailCommonHeader',
			'attr'		=> array(
				'cols'	=> 42,
				'rows'	=> 4,
				),
			),
	/* validators */
		array(
			)
		);
	?>
	</td>
	<td rowspan="3" style="vertical-align: top;">
		<?php foreach( $tags as $t ) : ?>
			<?php echo $t; ?><br>
		<?php endforeach; ?>
	</td>
</tr>

<tr>
	<th><?php echo M('Footer For All Emails'); ?></th>
</tr>
<tr>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'textarea',
	/* attributes */
		array(
			'id'		=> 'emailCommonFooter',
			'attr'		=> array(
				'cols'	=> 42,
				'rows'	=> 4,
				),
			),
	/* validators */
		array(
			)
		);
	?>
	</td>
</tr>
</table>

<p>
<h3><?php echo M('SMTP Settings'); ?></h3>
<p>
<em>
<?php echo M('Fill in if required by your web hosting. You may need to consult your web hosting administrator or help documentation.'); ?>
</em>

<?php
echo ntsForm::wrapInput(
	M('Host'),
	$this->buildInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'smtpHost',
			'attr'		=> array(
				'size'	=> 32,
				),
			),
	/* validators */
		array(
			)
		)
	);
?>

<?php
echo ntsForm::wrapInput(
	M('Username'),
	$this->buildInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'smtpUser',
			'attr'		=> array(
				'size'	=> 32,
				),
			),
	/* validators */
		array(
			)
		)
	);
?>

<?php
echo ntsForm::wrapInput(
	M('Password'),
	$this->buildInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'smtpPass',
			'attr'		=> array(
				'size'	=> 42,
				),
			),
	/* validators */
		array(
			)
		)
	);
?>

<?php
$secureOptions = array(
	array( '', M('None') ),
	array( 'tls', 'TLS' ),
	array( 'ssl', 'SSL' ),
	);
echo ntsForm::wrapInput(
	M('Secure'),
	$this->buildInput (
	/* type */
		'select',
	/* attributes */
		array(
			'id'		=> 'smtpSecure',
			'options'	=> $secureOptions,
			),
	/* validators */
		array(
			)
		)
	);
?>

<?php echo $this->makePostParams('-current-', 'update'); ?>
<?php
echo ntsForm::wrapInput(
	'',
	'<input class="btn btn-default" type="submit" value="' . M('Save') . '">'
	);
?>