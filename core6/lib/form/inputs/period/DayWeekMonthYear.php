<?php
/* should be sorted desc */
$multiplier = array(
	array( 'w', 7 * 24 * 60 * 60 ),
	array( 'd', 24 * 60 * 60 ),
	array( 'h', 60 * 60 ),
	array( 'm', 60 ),	
	);

switch( $inputAction ){
	case 'display':
		$id_Unit = $conf['id'] . '_unit';
		$id_Qty = $conf['id'] . '_qty';

		if( $conf['value'] == 0 ){
			$qty = 1;
			$unit = 'months';
			}
		else {
			list( $qty, $unit ) = explode( ' ', $conf['value'] );
			}

	// QTY CONTROL
		$qtyConf = array(
			'id'		=> $id_Qty,
			'value'		=> $qty,
			'attr'	=> array(
				'size'	=> 2,
				),
			);
		$input .= $this->makeInput(
			'text',
			$qtyConf
			);

	// UNIT CONTROL
		$unitOptions = array(
			array( 'hours', M('Hours') ),
			array( 'days', M('Days') ),
			array( 'weeks', M('Weeks') ),
			array( 'months', M('Months') ),
			array( 'years', M('Years') ),
			);

		$unitConf = array(
			'id'	 	=> $id_Unit,
			'value'		=> $unit,
			'options'	=> $unitOptions,
			);
		$input .= $this->makeInput(
			'select',
			$unitConf
			);
		break;

	case 'submit':
		$id_Unit = $handle . '_unit';
		$id_Qty = $handle . '_qty';

		$submittedValue_Unit = $_NTS['REQ']->getParam( $id_Unit );
		$submittedValue_Qty = $_NTS['REQ']->getParam( $id_Qty );

		$input = $submittedValue_Qty . ' ' . $submittedValue_Unit;
		break;

	case 'validate':
		$id_Unit = $handle . '_unit';
		$id_Qty = $handle . '_qty';

		$submittedValue_Unit = $_NTS['REQ']->getParam( $id_Unit );
		$submittedValue_Qty = $_NTS['REQ']->getParam( $id_Qty );

		$val->checkValue = $submittedValue_Qty; 
		if( ! $val->run('notEmpty') ){
			$validationFailed = TRUE;
			$validationError = M('Required');
			return;
			}
		if( ! $val->run('integer') ){
			$validationFailed = TRUE;
			$validationError = M('Numbers only');
			return;
			}
		break;

	case 'check_submit':
		$id_Unit = $handle . '_unit';
		$id_Qty = $handle . '_qty';

		$input = isset( $_POST[$id_Unit] ) ? true : false;
		break;
	}
?>