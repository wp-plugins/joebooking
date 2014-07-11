<?php
$t = new ntsTime;
$t->setNow();
$today = $t->formatDate_Db();

$objects = ntsObjectFactory::getAll( 'service', '', TRUE );

list( $year, $month, $day ) = ntsTime::splitDate( $today );
$month = ltrim($month, 0);
$month = $month - 1;
$day = ltrim($day, 0);
$dateFormat = NTS_DATE_FORMAT;

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
		$deleteHtml = '&nbsp;<a class="btn btn-default btn-xs  ' . $deleteClass . '" href="#" title="' . M('Delete') . '"><i class="fa fa-times text-danger"></i></a>';

		$input .= '<ol style="margin: 0 0 0 2em; padding: 0 0;" id="' . $containerId . '">';
		reset( $conf['value'] );
		for( $ii = 1; $ii <= count($conf['value']); $ii++ )
		{
			$date = $conf['value'][$ii - 1];
			$t->setDateDb( $date );
			$input .= '<li data-id="' . $date . '">' . $t->formatDate();
			$input .= $deleteHtml;
			$input .= '</li>';
		}
		$input .= '</ol>';

		$input .= '<a class="btn btn-success btn-xs" href="#" id="' . $addId . '" gldp-id="' . $addId . '">';
		$input .= '<i class="fa fa-plus"></i> ' . M('Add');
		$input .= '</a>';

		$input .= <<<EOT

<script language="JavaScript">
jQuery("#$addId").live( 'click', function(){
	return false;
	});

jQuery("#$addId").glDatePicker({
	onChange: function(target, newDate){
		var newItem = newDate.format('Ymd');
		var newItemView = newDate.format('$dateFormat');
		jQuery("#$containerId").append( '<li data-id="' + newItem + '">' + newItemView + '$deleteHtml' + '</li>' );
//		jQuery("#$addId >option:eq(0)").attr('selected', true);
		
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
		},
	startDate: new Date( $year, $month, $day ),
	selectedDate: new Date( $year, $month, $day )
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

	case 'submit':
		$input = $_NTS['REQ']->getParam( $handle );
		$input = $input ? explode( '-', $input ) : array();
	}
?>