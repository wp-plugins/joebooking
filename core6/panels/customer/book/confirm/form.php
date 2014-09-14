<?php
$session = new ntsSession;
$apps = $session->userdata( 'apps' );

$om =& objectMapper::getInstance();
$service_id = $this->getValue('service_id');

$class = 'appointment';
$otherDetails = array(
	'service_id' => $service_id,
	);
$all_fields = $om->getFields( $class, 'external', $otherDetails );
$fields = array();
reset( $all_fields );
foreach( $all_fields as $f )
{
	if( isset($f[4]) && ($f[4] == 'read') )
	{
		// check if there's a default value
		if( strlen($f[3]) == 0 )
		{
			continue;
		}
	}
	$fields[] = $f;
}
?>
<?php if( $fields ) : ?>
	<div class="page-header">
		<h3><?php echo M('Additional Information'); ?></h3>
	</div>
<?php endif; ?>

<p>
<?php foreach( $fields as $f ) : ?>
	<?php $c = $om->getControl( $class, $f[0], false ); ?>
	<?php
	if( isset($f[4]) )
	{
		if( $f[4] == 'read' )
		{
			$c[1] = 'label';
//			$c[2]['readonly'] = 1;
//			_print_r( $c );
//			continue;
		}
	}
	?>
	<?php
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
<?php endforeach; ?>

<?php echo $this->makePostParams('-current-', 'submit' ); ?>
<?php
$btn_label = (count($apps) > 1) ? M('Confirm Appointments') : M('Confirm Appointment');
$btn = '<INPUT class="btn btn-default btn-lg" TYPE="submit" VALUE="' . $btn_label . '">'
?>

<hr>
<p>
<?php if( $fields ) : ?>
	<?php
	echo ntsForm::wrapInput(
		'',
		$btn
		);
	?>
<?php else : ?>
	<?php echo $btn; ?>
<?php endif; ?>