<?php echo M('Please give a reason'); ?>
<p>
<?php
echo $this->makeInput (
	'textarea',
	array(
		'id'		=> 'reason',
		'attr'		=> array(
			'cols'	=> 32,
			'rows'	=> 3,
			),
		'default'	=> '',
		),
	array(
		)
	);
?>
<p>
<?php
$action = $_NTS['REQ']->getRequestedAction();
echo $this->makePostParams('-current-', $action . '-confirm' );
?>
<INPUT class="btn btn-default" TYPE="submit" VALUE="<?php echo M('Confirm'); ?>">
