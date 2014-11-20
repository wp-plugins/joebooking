<table>
<tr>
	<th><?php echo M('Email'); ?> *</th>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'email',
			'attr'		=> array(
				'size'	=> 42,
				),
			'required'	=> 1,
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> M('Required'),
				),
			)
		);
	?>
	</td>
</tr>

<tr>
<td>&nbsp;</td>
<td>
	<DIV CLASS="buttonBar">
	<?php echo $this->makePostParams('-current-', 'resend' ); ?>
	<INPUT TYPE="submit" VALUE="<?php echo M('Resend Confirmation Code'); ?>">
	</DIV>
</td>
</tr>
</table>