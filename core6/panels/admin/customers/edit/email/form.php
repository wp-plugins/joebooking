<?php
$conf =& ntsConf::getInstance();
$commonHeader = $conf->get('emailCommonHeader');
$commonFooter = $conf->get('emailCommonFooter');
?>
<table class="ntsForm">
<tr>
	<td class="ntsFormLabel"><?php echo M('Subject'); ?></td>
	<td>
<?php
	echo $this->makeInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'subject',
			'attr'		=> array(
				'size'	=> 48,
				),
			'required'	=> 1,
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> M('Required field'),
				),
			)
		);
?>
	</td>
</tr>

<tr>
	<td class="ntsFormLabel"><?php echo M('Message'); ?></td>
	<td>
	<a href="<?php echo ntsLink::makeLink('admin/conf/email_settings'); ?>"><?php echo M('Header'); ?>: <?php echo M('Edit'); ?></a>
	<br>
	<?php echo $commonHeader; ?>
	<br>
<?php
	echo $this->makeInput (
	/* type */
		'textarea',
	/* attributes */
		array(
			'id'		=> 'body',
			'attr'		=> array(
				'cols'	=> 56,
				'rows'	=> 16,
				),
			'required'	=> 1,
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> M('Required field'),
				),
			)
		);
?>

	<br>
	<?php echo nl2br($commonFooter); ?>
	<br>
	<a href="<?php echo ntsLink::makeLink('admin/conf/email_settings'); ?>"><?php echo M('Footer'); ?>: <?php echo M('Edit'); ?></a>
	</td>
</tr>

<tr>
<td></td>
<td>
<?php echo $this->makePostParams('-current-', 'send' ); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Send'); ?>">
</td>
</tr>
</table>