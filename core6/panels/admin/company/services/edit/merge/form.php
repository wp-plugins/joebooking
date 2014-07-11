<?php
$object = ntsLib::getVar( 'admin/company/services/edit::OBJECT' );

$services = ntsObjectFactory::getAll('service');
$options = array();
foreach( $services as $service )
{
	if( $object->getId() != $service->getId() )
	{
		$options[] = array( $service->getId(), ntsView::objectTitle($service) );
	}
}
?>
<p>
<?php echo M('When you merge a service to another one, this service is deleted and all appointments and schedules are transferred to the other one'); ?>
</p>

<?php
echo $this->wrapInput (
	M('Merge To'),
	$this->buildInput (
	/* type */
		'select',
	/* attributes */
		array(
			'id'	=> 'merge_to',
			'options'	=> $options,
			)
		)
	);
?>


<?php echo $this->makePostParams('-current-', 'merge'); ?>
<?php
echo ntsForm::wrapInput(
	'',
	'<INPUT class="btn btn-default" TYPE="submit" VALUE="' . M('Merge') . '">'
	);
?>
