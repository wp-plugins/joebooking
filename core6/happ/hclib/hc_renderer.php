<?php
class Hc_renderer
{
	function render( $view_file, $view_params = array() )
	{
		if( $view_params ){
			extract($view_params);
		}

		ob_start();
		require( $view_file );
		$output = ob_get_contents();
		ob_end_clean();
		$output = trim( $output );
		return $output;
	}
}
