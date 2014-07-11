<?php
$myTimeUnit = defined('NTS_TIME_UNIT') ? NTS_TIME_UNIT : 1;

/* should be sorted desc */
$multiplier = array(
	'h'	=> 60 * 60,
	);

switch( $inputAction ){
	case 'display':
		$id_Qty_Hours = $conf['id'] . '_qty_hour';

	/* find multiplier first */
		$remainValue = $conf['value'];
		$qty_Hours = 0;

		if( $remainValue >= $multiplier['h'] ){
			$qty_Hours = floor( $remainValue / $multiplier['h'] );
			$remainValue = $remainValue - $qty_Hours * $multiplier['h'];
			}

	// QTY CONTROL
		$hoursOptions = array();
		for( $i = 1; $i <= 24; $i++ )
			$hoursOptions[] = array( $i, sprintf('%02d', $i) );

		$input .= $this->makeInput(
			'select',
			array(
				'id'		=> $id_Qty_Hours,
				'options'	=> $hoursOptions,
				'default'	=> $qty_Hours,
				)
			);
		break;

	case 'submit':
		$id_Qty_Hours = $handle . '_qty_hour';

		$submittedValue_Hours = $_NTS['REQ']->getParam( $id_Qty_Hours );

		$input = $multiplier['h'] * $submittedValue_Hours;
		break;

	case 'check_submit':
		$id_Qty_Hours = $handle . '_qty_hour';

		$input = isset($_POST[$id_Qty_Hours]) ? true : false;
		break;
	}
?>