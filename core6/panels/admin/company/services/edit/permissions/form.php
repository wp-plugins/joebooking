<?php
$permissionOptions = array(
	array( 'allowed',		M('Approval') . ': ' . M('Required') ),
	array( 'auto_confirm',	M('Auto Approved') ),
	);
$groups = array(
	array( -1, M('New Customers') ),
	array( 0, M('Registered Customers') ),
	);
$object = ntsLib::getVar( 'admin/company/services/edit::OBJECT' );
$prepay = $object->getPrepayAmount();

$pgm =& ntsPaymentGatewaysManager::getInstance();
$has_online = $pgm->hasOnline();
?>

<?php foreach( $groups as $g ) : ?>
	<?php
	$ctlId = 'group' . $g[0];
	if( ($prepay > 0) && $has_online )
	{
		echo $this->wrapInput (
			$g[1],
			M('Payment Required')
			);
	}
	else
	{
		echo $this->wrapInput (
			$g[1],
			$this->buildInput (
			/* type */
				'select',
			/* attributes */
				array(
					'id'		=> $ctlId,
					'options'	=> $permissionOptions,
					)
				)
			);
	}
	?>
<?php endforeach; ?>

<?php
echo $this->wrapInput (
	M('Cancellation/Reschedule Deadline'),
	$this->buildInput (
	/* type */
		'period/MinHourDayWeek',
	/* attributes */
		array(
			'id'		=> 'min_cancel',
			'default'	=> 1 * 24 * 60 * 60,
			'attr'		=> array(
				),
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
	'<INPUT class="btn btn-default" TYPE="submit" VALUE="' . M('Update') . '">'
	);
?>
