<?php
switch( $inputAction )
{
	case 'display':
		// is URL?
		if( preg_match('/https?\:/', $conf['value']) )
		{
			$input .= '<a target="_blank" href="' . $conf['value'] . '">' . $conf['value'] . '</a>';
		}
		else
		{
			$input .= '<p class="form-control-static">';
			$input .= htmlspecialchars( $conf['value'] );
			$input .= '</p>';
		}
		break;
}
?>