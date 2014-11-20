<?php
$app_info = ntsLib::getAppInfo();
$id = $this->getValue( 'id' );
$this->setParams(
	array(
		'myId'	=> $id,
		)
	);
?>

<?php
echo ntsForm::wrapInput(
	M('Title'),
	$this->buildInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'title',
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
			array(
				'code'		=> 'checkUniqueProperty.php', 
				'error'		=> M('Already in use'),
				'params'	=> array(
					'prop'	=> 'title',
					'class'	=> 'service',
					'skipMe'	=> 1
					),
				),
			)
		)
	);
?>

<?php
echo ntsForm::wrapInput(
	M('Description'),
	$this->buildInput (
	/* type */
		'textarea',
	/* attributes */
		array(
			'id'		=> 'description',
			'attr'		=> array(
				'cols'	=> 20,
				'rows'	=> 6,
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
	M('Duration'),
	$this->buildInput (
	/* type */
		'period/LongHourMinute',
	/* attributes */
		array(
			'id'	=> 'duration',
			),
	/* validators */
		array(
			array(
				'code'		=> 'greaterThan.php', 
				'error'		=> M('Required'),
				'params'	=> array(
					'compareWith'	=> 0,
					)
				),
			)
		)
	);
?>

<?php
echo ntsForm::wrapInput(
	M('Clean Up'),
	$this->buildInput (
	/* type */
		'period/HourMinute',
	/* attributes */
		array(
			'id'	=> 'lead_out',
			)
		)
	);
?>

<?php
global $NTS_CURRENT_USER;
?>
<?php if( ! $NTS_CURRENT_USER->isPanelDisabled('admin/payments') ) : ?>
	<?php
	echo ntsForm::wrapInput(
		M('Price'),
		$this->buildInput (
		/* type */
			'text',
		/* attributes */
			array(
				'id'	=> 'price',
				'attr'	=> array(
					'size'	=> 4,
					)
				)
			)
		);
	?>
<?php endif; ?>

<?php if( ! NTS_SINGLE_RESOURCE ) : ?>
	<?php
	echo ntsForm::wrapInput(
		M('Occupies Entire Location'),
		$this->buildInput (
		/* type */
			'checkbox',
		/* attributes */
			array(
				'id'	=> 'blocks_location',
				)
			)
		);
	?>
<?php endif; ?>

<?php echo $this->makePostParams('-current-', 'update'); ?>
<?php
echo ntsForm::wrapInput(
	'',
	'<INPUT class="btn btn-default" TYPE="submit" VALUE="' . M('Update') . '">'
	);
?>