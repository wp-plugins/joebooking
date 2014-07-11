<?php
$id = $this->getValue('id');
/* form params - used later for validation */
$this->setParams(
	array(
		'myId'	=> $id,
		)
	);

$object = $this->getValue('object');

if( $object->hasRole('customer') )
	$className = 'customer';
elseif( $object->hasRole('provider') )
	$className = 'provider';
else
	$className = 'user';

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
$restrictions = $object->getProp( '_restriction' );

if( $restrictions )
{
	if( in_array('email_not_confirmed', $restrictions) )
		$status_view = M('Email Not Confirmed');
	elseif( in_array('not_approved', $restrictions) )
		$status_view = M('Not Approved');
	elseif( in_array('suspended', $restrictions) )
		$status_view = M('Suspended');
}
else
{
	$status_view = M('Active');
}

if( $restrictions )
{
	$status_view = '<span class="btn btn-sm btn-danger">' . $status_view . '</span>';
}
else
{
	$status_view = '<span class="btn btn-sm btn-success">' . $status_view . '</span>';
}
?>
<?php
echo ntsForm::wrapInput(
	M('Status'),
	$status_view
	);
?>

<?php foreach( $fields as $f ) : ?>
	<?php
	$c = $om->getControl( $className, $f[0], false );
	$fieldType = $c[1];
	if( isset($f[4]) ){
		if( $f[4] == 'read' ){
			$c[1] = 'label';
			$c[2]['readonly'] = 1;
			}
		}
	$ri = ntsLib::remoteIntegration();
	if( ($ri == 'wordpress') && ($c[2]['id'] == 'username') )
	{
		$c[1] = 'labelData';
	}
	if( $c[2]['description'] )
		$c[2]['help'] = $c[2]['description'];

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