<?php
switch( $inputAction ){
	case 'display':
		if( ! isset($conf['value']) )
			$conf['value'] = array();
		else {
			$conf['value'] = explode( '-', $conf['value'] );
			}

		reset( $conf['options'] );
		foreach( $conf['options'] as $o ){
			if( isset($conf['attr']['separator_before']) )
				$input .= $conf['attr']['separator_before'];

			$checked = in_array($o[0], $conf['value']) ? true : false;
			$input .= $this->makeInput(
				'checkbox',
				array(
					'id'		=> $conf['id'] . '[]',
					'box_value'	=> $o[0],
					'value'		=> $checked,
					'readonly'	=> isset($o[2]) ? $o[2] : 0,
					)
				);
			$input .= '' . $o[1] . ' ';

			if( isset($conf['attr']['separator_after']) )
				$input .= $conf['attr']['separator_after'];
			}
		break;

	case 'submit':
		$input = $_NTS['REQ']->getParam( $handle );
		if( ! $input )
			$input = array();
		$input = join( '-', $input );
		break;

	case 'check_submit':
		$input = true;
		break;
	}
?>