<?php
$conf =& ntsConf::getInstance();
$commonHeader = $conf->get('emailCommonHeader');
$commonFooter = $conf->get('emailCommonFooter');
?>
<?php
echo ntsForm::wrapInput(
	M('Subject'),
	$this->buildInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'subject',
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
	)
?>

<p>
<a class="btn btn-sm btn-default" href="<?php echo ntsLink::makeLink('admin/conf/email_settings'); ?>"><?php echo M('Header'); ?>: <?php echo M('Edit'); ?></a>
</p>
<?php echo nl2br( htmlentities($commonHeader) ); ?>

<br />
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

<br />
<?php echo nl2br( htmlentities($commonFooter) ); ?>

<p>
<a class="btn btn-sm btn-default" href="<?php echo ntsLink::makeLink('admin/conf/email_settings'); ?>"><?php echo M('Footer'); ?>: <?php echo M('Edit'); ?></a>
</p>

<hr>
<?php echo $this->makePostParams('-current-', 'send' ); ?>
<?php
echo ntsForm::wrapInput(
	'',
	'<input class="btn btn-default" type="submit" value="' . M('Send') . '">'
	);
?>
