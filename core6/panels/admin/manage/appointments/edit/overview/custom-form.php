<?php
$object = ntsLib::getVar( 'admin/manage/appointments/edit::OBJECT' );
$serviceId = $object->getProp( 'service_id' );

$class = 'appointment';
$otherDetails = array(
	'service_id'	=> $serviceId,
	);
$om =& objectMapper::getInstance();
$fields = $om->getFields( $class, 'internal', $otherDetails );
reset( $fields );
?>
<?php if( $fields ) : ?>
	<?php foreach( $fields as $f ) : ?>
		<?php $c = $om->getControl( $class, $f[0], false ); ?>
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

	<?php if( ! $this->readonly ) : ?>
		<?php echo $this->makePostParams('-current-', 'update'); ?>
		<?php
		echo ntsForm::wrapInput(
			'',
			'<INPUT class="btn btn-default" TYPE="submit" VALUE="' . M('Update') . '">'
			);
		?>
	<?php endif; ?>
<?php endif; ?>
