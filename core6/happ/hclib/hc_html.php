<?php
class Hc_html
{
	static function dropdown_menu( $menu, $class = 'dropdown-menu', $more_li_class = '' )
	{
		$renderer = new Hc_renderer;
		$view_file = dirname(__FILE__) . '/view/dropdown_menu.php';
		return $renderer->render( 
			$view_file, 
			array(
				'menu'			=> $menu,
				'class'			=> $class,
				'more_li_class'	=> $more_li_class,
				)
			);
	}

	/*
	$input could be either string or
	$input = new stdClass;
	$input->type = $type;
	$input->view = $input;
	$input->error = $conf['error'];
	$input->help = $conf['help'];
	*/
	static function wrap_input( $label, $input, $aligned = TRUE )
	{
		$return = '';
		$inputs = is_array($input) ? $input : array( $input );

		$label = Hc_lib::parse_lang( $label );

		$surroundClass = 'form-group';
		if( is_object($input) && $input->error )
		{
			$surroundClass .= ' has-error';
		}
		elseif( count($inputs) > 1 )
		{
			reset( $inputs );
			foreach( $inputs as $in )
			{
				if( is_object($in) && $in->error )
				{
					$surroundClass .= ' has-error';
					break;
				}
			}
		}

		$return .= '<div class="' . $surroundClass . '">';
		if( is_object($input) && ($input->type == 'checkbox') )
		{
			if( $aligned )
			{
//				$return .= '<div class="col-sm-10 col-sm-offset-2">';
				$return .= '<div class="control-holder">';
			}
			else
			{
				$return .= '<div class="col-sm-12">';
			}
		}
		else
		{
//			$return .= '<label class="col-sm-2 control-label">';
			$return .= '<label class="control-label">';
			$return .= $label;
			$return .= '</label>';

//			$return .= '<div class="col-sm-10">';
			$return .= '<div class="control-holder">';
		}

		foreach( $inputs as $input )
		{
			if( is_object($input) && $input->type == 'checkbox' )
			{
				$return		.= '<div class="checkbox">';
				$return			.= '<label>';
				$return				.= $input->view;
				if( count($inputs) <= 1 )
				{
					$return				.= ' ' . $label;
				}
				$return			.= '</label>';
				if( $input->error )
				{
					$return		.= '<span class="help-inline">' . $input->error . '</span>';
				}
				if( $input->help )
				{
					$return		.= '<span class="help-block">' . $input->help . '</span>';
				}
				$return		.= '</div>';
			}
			else
			{
				$wrap_start = '';
				$wrap_end = '';
				if( 
					(! is_object($input)) OR
					in_array( $input->type, array('labelData') )
					)
				{
					if( count($inputs) < 2 )
					{
						$wrap_start = '<p class="form-control-static">';
						$wrap_end = '</p>';
					}
				}

				if( is_object($input) )
				{
					$return .= $wrap_start . $input->view . $wrap_end;
				}
				else
				{
					$return .= $wrap_start . $input . $wrap_end;
				}

				if( is_object($input) )
				{
					if( $input->error )
					{
						$return .= '<span class="help-inline">' . $input->error . '</span>';
					}
					if( $input->help )
					{
						$return .= '<span class="help-block">' . $input->help . '</span>';
					}
				}
			}
		}
		$return .= '</div>';
		$return .= '</div>';
		return $return;
	}
}
