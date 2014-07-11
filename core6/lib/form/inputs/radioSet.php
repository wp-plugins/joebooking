<?php
$allValue = isset($conf['allValue']) ? $conf['allValue'] : 0;

switch( $inputAction ){
	case 'display':
		if( ! isset($conf['value']) )
			$conf['value'] = array();

//		$input .= '<ul class="list-inline">';

		reset( $conf['options'] );
		foreach( $conf['options'] as $o ){
			$attr = array();
			if( isset($conf['attr']) ){
				$attr = $conf['attr'];
				}

			$sub_conf = array(
				'id'		=> ntsView::getRealName($conf['id']),
				'value'		=> $o[0],
				'default'	=> $conf['value'],
				'attr'		=> $attr,
				);

			$input .= '<label class="radio-inline">';
				$input .= $this->makeInput(
					'radio',
					$sub_conf
					);
				$input .= $o[1];
			$input .= '</label>';
			}
//		$input .= '</ul>';

		break;

	case 'submit':
		$input = $_NTS['REQ']->getParam( $handle );
		break;

	case 'check_submit':
		$input = isset( $_POST[$handle] ) ? true : false;
		break;
	}
?>