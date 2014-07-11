<?php
$objects = ntsObjectFactory::getAll( 'location' );
if( count($objects) > 1 )
{
	$object_options = array();
	$object_options[] = array( 0, ' - ' . M('Any') . ' - ' );
	reset( $objects );
	foreach( $objects as $o )
	{
		$object_options[] = array( $o->getId(), ntsView::objectTitle($o) );
	}

	echo ntsForm::wrapInput(
		M('Location'),
		array( 
			$this->buildInput(
			/* type */
				'select',
			/* attributes */
				array(
					'id'	=> '_assign_location',
					'options'	=> $object_options
					)
				),
			'<br>',
			'<div>',
			$this->buildInput(
			/* type */
				'checkbox',
			/* attributes */
				array(
					'id'	=> '_assign_location_only',
					'label'	=> M('Only This One')
					)
				),
			'</div>'
			)
		);
}
?>

<?php
$objects = ntsObjectFactory::getAll( 'resource' );
if( count($objects) > 1 )
{
	$object_options = array();
	$object_options[] = array( 0, ' - ' . M('Any') . ' - ' );
	reset( $objects );
	foreach( $objects as $o )
	{
		$object_options[] = array( $o->getId(), ntsView::objectTitle($o) );
	}

	echo ntsForm::wrapInput(
		M('Bookable Resource'),
		array( 
			$this->buildInput(
			/* type */
				'select',
			/* attributes */
				array(
					'id'	=> '_assign_resource',
					'options'	=> $object_options
					)
				),
			'<br>',
			$this->buildInput(
			/* type */
				'checkbox',
			/* attributes */
				array(
					'id'	=> '_assign_resource_only',
					'label'	=> M('Only This One')
					)
				)
			)
		);
}
?>

<?php
$objects = ntsObjectFactory::getAll( 'service' );
if( count($objects) > 1 )
{
	$object_options = array();
	$object_options[] = array( 0, ' - ' . M('Any') . ' - ' );
	reset( $objects );
	foreach( $objects as $o )
	{
		$object_options[] = array( $o->getId(), ntsView::objectTitle($o) );
	}

	echo ntsForm::wrapInput(
		M('Service'),
		array(
			$this->buildInput(
			/* type */
				'select',
			/* attributes */
				array(
					'id'	=> '_assign_service',
					'options'	=> $object_options
					)
				),
			'<br>',
			$this->buildInput(
			/* type */
				'checkbox',
			/* attributes */
				array(
					'id'	=> '_assign_service_only',
					'label'	=> M('Only This One')
					)
				)
			)
		);
}
?>

<?php echo $this->makePostParams('-current-', 'assign' ); ?>
<?php
echo ntsForm::wrapInput(
	'',
	'<INPUT class="btn btn-default" TYPE="submit" VALUE="' . M('Assign') . '">'
	);
?>

<script language="JavaScript">
<?php if( ! $this->getValue('_assign_location') ) : ?>
jQuery("#<?php echo $this->formId; ?>_assign_location_only").closest('div').hide();
<?php endif; ?>
<?php if( ! $this->getValue('_assign_resource') ) : ?>
jQuery("#<?php echo $this->formId; ?>_assign_resource_only").closest('div').hide();
<?php endif; ?>
<?php if( ! $this->getValue('_assign_service') ) : ?>
jQuery("#<?php echo $this->formId; ?>_assign_service_only").closest('div').hide();
<?php endif; ?>

jQuery("#<?php echo $this->formId; ?>_assign_location").on(
	'change',
	function(){
		var my_parent = jQuery("#<?php echo $this->formId; ?>_assign_location_only").closest('div');
		( jQuery(this).val() == 0 ) ? my_parent.hide() : my_parent.show();
	});

jQuery("#<?php echo $this->formId; ?>_assign_resource").on(
	'change',
	function(){
		var my_parent = jQuery("#<?php echo $this->formId; ?>_assign_resource_only").closest('div');
		( jQuery(this).val() == 0 ) ? my_parent.hide() : my_parent.show();
	});

jQuery("#<?php echo $this->formId; ?>_assign_service").on(
	'change',
	function(){
		var my_parent = jQuery("#<?php echo $this->formId; ?>_assign_service_only").closest('div');
		( jQuery(this).val() == 0 ) ? my_parent.hide() : my_parent.show();
	});
</script>