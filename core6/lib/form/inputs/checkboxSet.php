<?php
if( ! isset($conf['includeAll']) )
	$conf['includeAll'] = false;
$allValue = isset($conf['allValue']) ? $conf['allValue'] : 0;

switch( $inputAction ){
	case 'display':
		if( ! isset($conf['value']) )
			$conf['value'] = array();

		if( ! is_array($conf['value']) ){
			if( strlen($conf['value']) )
				$conf['value'] = array( $conf['value'] );
			else
				$conf['value'] = array();
			}

		$contId = $conf['htmlId'] . '_container';
		$allId = $conf['htmlId'] . '_' . $allValue;
		if( $conf['includeAll'] ){
			$input .= '<div class="checkbox"><label>';
			$checked = in_array($allValue, $conf['value']) ? true : false;
			$input .= $this->makeInput(
				'checkbox',
				array(
					'htmlId'	=> $allId,
					'id'		=> ntsView::getRealName($conf['id']) . '[]',
					'box_value'	=> $allValue,
					'value'		=> $checked,
					)
				);
			$labelId = $conf['htmlId'] . '_label_' . $allValue;
			$input .= '<span id="' . $labelId . '">' . ' - ' . M('All') . ' - ' . '</span> ';
			$input .= '</label></div>';
			}

		$input .= '<div id="' . $contId . '" style="overflow: auto;">';
		reset( $conf['options'] );
		foreach( $conf['options'] as $o ){
			if( isset($conf['attr']['separator_before']) )
				$input .= $conf['attr']['separator_before'];

			$attr = array();
			if( isset($conf['attr']) ){
				$attr = $conf['attr'];
				unset($attr['separator_before']);
				unset($attr['separator_after']);
				}

			$checked = in_array($o[0], $conf['value']) ? true : false;

			$input .= '<span style="white-space: nowrap; float: left; display: block; margin: 0 0.5em 0 0;">';

				$input .= '<div class="checkbox">';
				$input .= '<label>';
				$input .= $this->makeInput(
					'checkbox',
					array(
						'htmlId'	=> $conf['htmlId'] . '_' . $o[0],
						'id'		=> ntsView::getRealName($conf['id']) . '[]',
						'box_value'	=> $o[0],
						'value'		=> $checked,
						'readonly'	=> isset($o[2]) ? $o[2] : 0,
						'attr'		=> $attr,
						)
					);

				$labelId = $conf['htmlId'] . '_label_' . $o[0];
				$input .= '<span id="' . $labelId . '">' . $o[1] . '</span> ';
				$input .= '</label>';
				$input .= '</div>';

			$input .= '</span>';

			if( isset($conf['attr']['separator_after']) )
				$input .= $conf['attr']['separator_after'];
			}
			$input .= '<div style="float: none; clear: both;"></div>';
		$input .= '</div>';

		if( $conf['includeAll'] ){
			$input .= <<<EOT

<script language="JavaScript">
if( jQuery("#$allId").is(":checked") ){
	jQuery("#$contId").hide();
	}
else {
	jQuery("#$contId").show();
	}
jQuery("#$allId").live( 'click', function(){
	jQuery("#$contId").toggle();
	});
</script>

EOT;
			}

		break;

	case 'submit':
		$input = $_NTS['REQ']->getParam( $handle );
		if( ! $input )
			$input = array();
		if( $conf['includeAll'] )
		{
			if( in_array($allValue, $input) )
			{
				$input = array( $allValue );
			}
			if( ! $input )
			{
				$input = array( $allValue );
			}
		}
		break;

	case 'check_submit':
		$input = true;
		break;
	}
?>