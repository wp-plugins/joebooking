<?php
$objects = ntsObjectFactory::getAll( 'service', '', TRUE );

$conf['options'] = array();
reset( $objects );
foreach( $objects as $obj  ){
	$conf['options'][] = array( $obj->getId(), ntsView::objectTitle($obj) );
	}

switch( $inputAction )
{
	case 'display':
		if( (! isset($conf['value'])) || (! $conf['value']) )
		{
			$conf['value'] = array();
		}
		else
		{
			if( ! is_array($conf['value']) )
				$conf['value'] = explode( '-', $conf['value'] );
		}
		$saveValue = $conf['value'];
	// for hidden input
		$conf['value'] = join( '-', $conf['value'] );
		break;
}

require( NTS_LIB_DIR . '/lib/form/inputs/hidden.php' );

switch( $inputAction ){
	case 'display':
		$conf['value'] = $saveValue;

		$hiddenId = $conf['htmlId'];
		$containerId = $conf['htmlId'] . '_container';
		$addId = $conf['htmlId'] . '_add';

		$deleteClass = $conf['htmlId'] . '_delete';
		$deleteHtml = '&nbsp;<a class="btn btn-default btn-xs ' . $deleteClass . '" href="#" title="' . M('Delete') . '"><i class="fa fa-times text-danger"></i></a>';

		$input .= '<ol id="' . $containerId . '">';
		reset( $conf['value'] );
		for( $ii = 1; $ii <= count($conf['value']); $ii++ )
		{
			$sid = $conf['value'][$ii - 1];
			$input .= '<li data-id="' . $sid . '">' . ntsView::objectTitle($objects[$sid]);
			$input .= $deleteHtml;
			$input .= '</li>';
		}
		$input .= '</ol>';

		$input .= '<select id="' . $addId . '" class="form-control">';
		$input .= '<option value="0">' . ' - ' . M('Add') . ' - ' . '</option>';
		reset( $objects );
		foreach( $objects as $sid => $obj )
			$input .= '<option value="' . $sid . '">' . ntsView::objectTitle($objects[$sid]) . '</option>';
		$input .= '</select>';

		$input .= <<<EOT

<script language="JavaScript">
jQuery("#$addId").live( 'change', function(){
	var newSid = parseInt( jQuery("#$addId").val() );
	if( newSid > 0 ){
		var newService = jQuery("#$addId >option:selected").text();
		jQuery("#$containerId").append( '<li data-id="' + newSid + '">' + newService + '$deleteHtml' + '</li>' );
		jQuery("#$addId >option:eq(0)").attr('selected', true);

		// rebuild value
		var currentValue = '';
		jQuery('#$containerId').children('li').each( function(){
			var newSid = jQuery(this).data('id');
			if( currentValue )
				currentValue = currentValue + '-' + newSid;
			else
				currentValue = newSid;
			});
		jQuery("#$hiddenId").attr('value', currentValue );
		}
	});

jQuery("a.$deleteClass").live( 'click', function(){
	var targetDiv = jQuery(this).closest('li');
	targetDiv.remove();

	// rebuild value
	var currentValue = '';
	jQuery('#$containerId').children('li').each( function(){
		var newSid = jQuery(this).data('id');
		if( currentValue )
			currentValue = currentValue + '-' + newSid;
		else
			currentValue = newSid;
		});
	jQuery("#$hiddenId").attr('value', currentValue );
	return false;
	});

</script>

EOT;
		break;
	}
?>