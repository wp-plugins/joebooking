<?php
$id = $this->getValue('id');
/* form params - used later for validation */
$this->setParams(
	array(
		'myId'	=> $id,
		)
	);

$object = $this->getValue('object');
$className = 'provider';

$om =& objectMapper::getInstance();
if( $className == 'customer' || $className == 'user' ){
	if( $object->hasRole('admin') )
		$side = 'internal';
	else
		$side = 'external';
	}
else {
	$side = 'internal';
	}

$fields = $om->getFields( $className, $side );
reset( $fields );

/* status */
$rolesNames = array(
	'admin'		=> M('Admin'),
	'staff'		=> M('Staff'),
	'customer'	=> M('Customer'),
	);
?>

<?php
$admin_level = $object->getProp( '_admin_level' );
$myRoleView = $rolesNames[ $admin_level ];
echo ntsForm::wrapInput(
	M('Role'),
	$myRoleView
	);
?>

<?php foreach( $fields as $f ) : ?>
	<?php $c = $om->getControl( $className, $f[0], false ); ?>
	<?php
	$fieldType = $c[1];
	if( isset($f[4]) )
	{
		if( $f[4] == 'read' )
		{
			$c[1] = 'label';
			$c[2]['readonly'] = 1;
		}
	}
	$ri = ntsLib::remoteIntegration();
	if( ($ri == 'wordpress') && ($c[2]['id'] == 'username') )
	{
		$c[1] = 'labelData';
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

	<?php if( NTS_ALLOW_NO_EMAIL && ($className == 'customer') && ($c[2]['id'] == 'email') ) : ?>
		<?php
		echo ntsForm::wrapInput(
			M('No Email'),
			$this->buildInput (
			/* type */
				'checkbox',
			/* attributes */
				array(
					'id'	=> 'noEmail',
					)
				)
			);
		?>
	<?php endif; ?>

<?php endforeach; ?>

<?php if( NTS_ENABLE_TIMEZONES > 0 ) : ?>
	<?php if( $className == 'customer' ) : ?>
		<?php
		$timezoneOptions = ntsTime::getTimezones();
		echo ntsForm::wrapInput(
			M('Timezone'),
			$this->buildInput (
			/* type */
				'select',
			/* attributes */
				array(
					'id'		=> '_timezone',
					'options'	=> $timezoneOptions,
					)
				)
			);
		?>
	<?php endif; ?>
<?php endif; ?>

<?php echo $this->makePostParams('-current-', 'update' ); ?>
<?php
echo ntsForm::wrapInput(
	'',
	'<INPUT class="btn btn-default" TYPE="submit" VALUE="' . M('Update') . '">'
	);
?>