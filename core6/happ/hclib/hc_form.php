<?php
class HC_Form_Input
{
	protected $type = 'text';
	protected $name = 'name';
	protected $error = '';
	protected $value = NULL;

	function __construct()
	{
	}

	function set_type( $type )
	{
		$this->type = $type;
	}
	function type()
	{
		return $this->type;
	}

	function set_error( $error )
	{
		$this->error = $error;
	}
	function error()
	{
		return $this->error;
	}

	function set_value( $value )
	{
		$this->value = $value;
	}
	function value()
	{
		return $this->value;
	}

	function set_name( $name )
	{
		$this->name = $name;
	}
	function name()
	{
		return $this->name;
	}

	function to_array()
	{
		$return = array(
			'name'	=> $this->name(),
			'type'	=> $this->type(),
			'value'	=> $this->value(),
			'error'	=> $this->error(),
			);
		return $return;
	}
}

class HC_Form
{
	protected $defaults;
	protected $errors;
	protected $displayed_inputs = array();

	function open( $action = '', $attributes = '' )
	{
		HC_Lib::ob_start();
		$this->displayed_inputs = array();

		$CI =& ci_get_instance();

		if ($attributes == '')
		{
			$attributes = 'method="post"';
		}

		// If an action is not a full URL then turn it into one
		if ($action && strpos($action, '://') === FALSE)
		{
			$action = $CI->config->site_url($action);
		}

		// If no action is provided then set to the current url
		$action OR $action = $CI->config->site_url($CI->uri->uri_string());

		$form = '<form action="'.$action.'"';

		$form .= _attributes_to_string($attributes, TRUE);

		$form .= '>';

		// Add CSRF field if enabled, but leave it out for GET requests and requests to external websites	
		$hidden = array();
		if ($CI->config->item('csrf_protection') === TRUE AND ! (strpos($action, $CI->config->base_url()) === FALSE OR strpos($form, 'method="get"')))	
		{
			$hidden[$CI->security->get_csrf_token_name()] = $CI->security->get_csrf_hash();
		}

		if (is_array($hidden) AND count($hidden) > 0)
		{
			$form .= sprintf("<div style=\"display:none\">%s</div>", form_hidden($hidden));
		}

/*
		$errors = $this->errors();
		if( $errors )
		{
			$form .= HC_Html::alert_danger( $errors );
		}
*/

		return $form;
	}

	function close()
	{
		$return = '';

		$remained_errors = $this->remained_errors();
		if( $remained_errors )
		{
			$return .= HC_Html::alert_danger( $remained_errors );
		}
		$return .= HC_Lib::ob_end();

		$return .= '</form>';
		return $return;
	}

	function make_input( $type, $name )
	{
		$input = new HC_Form_Input;
		$input->set_type( $type );
		$input->set_name( $name );

		$default = $this->get_default( $name );
		if( $default !== NULL )
		{
			$input->set_value( $default );
		}

		$error = $this->error( $name );
		if( $error )
		{
			$input->set_error( $error );
		}

//		return $input;

		if( is_array($name) )
		{
			foreach( $name as $n )
			{
				$this->displayed_inputs[ $n ] = 1;
			}
		}
		else
		{
			$this->displayed_inputs[ $name ] = 1;
		}

		return $input->to_array();
	}

	function set_defaults( $defaults )
	{
		if( ! is_array($defaults) )
			return;

		reset( $defaults );
		foreach( $defaults as $k => $v )
		{
			$this->set_default( $k, $v );
		}
	}

	function set_default( $name, $value )
	{
		$this->defaults[$name] = $value;
	}

	function is_set_default( $name )
	{
		return isset($this->defaults[$name]);
	}

	function get_default( $name )
	{
		if( is_array($name) )
		{
			$return = array();
			foreach( $name as $n )
			{
				$return[ $n ] = $this->get_default( $n );
			}
		}
		else
		{
			$return = isset($this->defaults[$name]) ? $this->defaults[$name] : NULL;
		}
		return $return;
	}

	function get_defaults()
	{
		return $this->defaults;
	}

	function set_errors( $errors )
	{
		$this->errors = $errors;
	}

	function input( $field, $add_class = TRUE )
	{		
		if( $add_class )
		{
			if( ! isset($field['extra']['class']) )
				$field['extra']['class'] = '';
			if( $field['extra']['class'] )
				$field['extra']['class'] .= ' ';
			if( ! (isset($field['type']) && ( in_array($field['type'], array('checkbox', 'radio', 'hidden')) ) ) )
			{
				$field['extra']['class'] .= 'form-control';
			}
		}

		return hc_form_input(
			$field,
			$this->defaults,
			$this->errors,
			FALSE
			);
	}

	function build_input( $field, $show_error = FALSE )
	{
		if( ! isset($field['extra']['class']) )
			$field['extra']['class'] = '';
		if( $field['extra']['class'] )
			$field['extra']['class'] .= ' ';
		if( ! (isset($field['type']) && ( in_array($field['type'], array('checkbox', 'radio', 'hidden')) ) ) )
		{
			$field['extra']['class'] .= 'form-control';
		}

		if( $show_error )
		{
			$view = hc_form_input(
				$field,
				$this->defaults,
				$this->errors,
				FALSE
				);
		}
		else
		{
			$view = hc_form_input(
				$field,
				$this->defaults,
				array(),
				FALSE
				);
		}

		$type = isset($field['type']) ? $field['type'] : '';
		$error = isset( $this->errors[$field['name']] ) ? $this->errors[$field['name']] : '';
		$help = isset($field['help']) ? $field['help'] : '';

		$return = new stdClass;
		$return->type = $type;
		$return->view = $view;
		$return->error = $error;
		$return->help = isset($field['help']) ? $field['help'] : '';
		return $return;
	}

	function error( $name )
	{
		if( is_array($name) )
		{
			$return = array();
			foreach( $name as $n )
			{
				if( $e = $this->error($n) )
				{
					$return[ $n ] = $e;
				}
			}
		}
		else
		{
			if( isset($this->errors[$name]) )
				$return = $this->errors[$name];
			else
				$return = FALSE;
		}
		return $return;
	}

	function errors()
	{
		return $this->errors;
	}

	function remained_errors()
	{
		$return = $this->errors();
		reset( $this->displayed_inputs );
		foreach( $this->displayed_inputs as $i )
		{
			unset( $return[$i] );
		}
		return $return;
	}
}