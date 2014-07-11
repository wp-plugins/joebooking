<?php
$conf['attr']['size'] = 8;
?>
<?php
switch( $inputAction ){
	case 'display':
		$htmlId = $conf['htmlId'];
		$hiddenHtmlId = $htmlId . '_' . 2;
		$currentValue = $conf['value'];

		$t = new ntsTime;
		$t->setDateDb( $currentValue );
		$currentDisplay = $t->formatDate();

		$input .= '<input type="hidden" NAME="' . $conf['id'] . '" ID="' . $hiddenHtmlId . '" VALUE="' . $currentValue . '">';
		$input .= '<a class="btn btn-default" href="#" ID="' . $htmlId . '">';
		$input .= $currentDisplay;
		$input .= '</a>';

		list( $year, $month, $day ) = ntsTime::splitDate( $currentValue );
		$month = ltrim($month, 0);
		$month = $month - 1;
		$day = ltrim($day, 0);
		$dateFormat = NTS_DATE_FORMAT;
		
		$input .=<<<EOT

<script language="javascript">
jQuery("#$htmlId").glDatePicker({
	onChange: function(target, newDate){
		jQuery("#$hiddenHtmlId").val( newDate.format('Ymd') );
		target.html( newDate.format('$dateFormat') );
		},
	startDate: new Date( $year, $month, $day ),
	selectedDate: new Date( $year, $month, $day ),
	});
</script>

EOT;

		break;

	default:
		require( NTS_LIB_DIR . '/lib/form/inputs/hidden.php' );
		break;
	}
?>