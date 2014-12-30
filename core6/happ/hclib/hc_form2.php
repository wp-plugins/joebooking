<?php
class HC_Form2
{
	private $inputs = array();
	private $convert = array();

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

		return $form;
	}

	function close()
	{
		$return = '';

		$orphan_errors = $this->orphan_errors();
		if( $orphan_errors )
		{
			$danger = HC_Html_Factory::widget('alert', 'danger');
			$danger->set_items( $orphan_errors );
			$return .= $danger->render();
		}
		$return .= HC_Lib::ob_end();

		$return .= '</form>';
		return $return;
	}

	function set_input( $name, $type )
	{
		if( is_array($type) ) // with convert
		{
			list( $type, $convert ) = $type;
			$this->set_convert( $name, $convert );
		}

		$input = HC_Html_Factory::input( $type, $name );
		$this->inputs[ $name ] = $input;
		return $this;
	}
	function input( $name )
	{
		return isset($this->inputs[$name]) ? $this->inputs[$name] : NULL;
 	}

	function set_inputs( $inputs )
	{
		reset( $inputs );
		foreach( $inputs as $name => $type )
		{
			$this->set_input( $name, $type );
		}
		return $this;
	}

	function set_convert( $name, $convert )
	{
		$this->convert[$name] = $convert;
	}
	function set_converts( $converts )
	{
		reset( $converts );
		foreach( $converts as $name => $convert )
		{
			$this->set_convert( $name, $convert );
		}
	}
	function convert()
	{
		return $this->convert;
	}

	function grab( $post )
	{
		foreach( array_keys($this->inputs) as $k )
		{
			$this->inputs[$k]->grab( $post );
		}
	}

	function set_values( $values )
	{
		$convert = $this->convert();
		if( $convert )
		{
			$values = $this->_values_model_to_form( $values, $convert );
		}

		foreach( array_keys($this->inputs) as $k )
		{
			if( isset($values[$k]) )
			{
				$this->inputs[$k]->set_value( $values[$k] );
			}
		}
	}
	function values()
	{
		$return = array();
		foreach( array_keys($this->inputs) as $k )
		{
			$return[$k] = $this->inputs[$k]->value();
		}

		$convert = $this->convert();
		if( $convert )
		{
			$return = $this->_values_form_to_model( $return, $convert );
		}
		return $return;
	}

	function set_errors( $errors )
	{
		$convert = $this->convert();
		if( $convert )
		{
			$errors = $this->_errors_model_to_form( $errors, $convert );
		}

		$this->errors = $errors;
		foreach( array_keys($this->inputs) as $k )
		{
			if( isset($errors[$k]) )
			{
				$this->inputs[$k]->set_error( $errors[$k] );
			}
		}
	}
	function errors()
	{
		return $this->errors;
	}

	/* this one converts errors of model to the errors of the form with the translate conf array */
	protected function _errors_model_to_form( $errors, $convert_conf = array() )
	{
		$return = array();

		$unset_original_keys = array();
		reset( $convert_conf );
		foreach( $convert_conf as $my_key => $their_keys )
		{
			if( ! is_array($their_keys) )
				$their_keys = array( $their_keys );
			$unset_original_keys = array_merge( $unset_original_keys, $their_keys );

			reset( $their_keys );
			foreach( $their_keys as $tk )
			{
				if( isset($errors[$tk]) )
				{
					if( ! isset($return[$my_key]) )
					{
						$return[$my_key] = array();
					}
					$return[$my_key] = $errors[$tk];
				}
			}
		}

		$unset_original_keys = array_unique( $unset_original_keys );
		reset( $unset_original_keys );
		foreach( $unset_original_keys as $tk )
		{
			unset( $errors[$tk] );
		}

		$return = array_merge( $errors, $return );
		return $return;
	}

	/* this one converts values of model to the values of the form with the translate conf array */
	protected function _values_model_to_form( $values, $convert_conf = array() )
	{
		$return = array();

		$unset_original_keys = array();
		reset( $convert_conf );
		foreach( $convert_conf as $my_key => $their_keys )
		{
			if( ! is_array($their_keys) )
				$their_keys = array( $their_keys );
			$unset_original_keys = array_merge( $unset_original_keys, $their_keys );

			reset( $their_keys );
			foreach( $their_keys as $tk )
			{
				if( isset($values[$tk]) )
				{
					if( ! isset($return[$my_key]) )
					{
						$return[$my_key] = array();
					}
					$return[$my_key][] = $values[$tk];
				}
			}
		}

		$unset_original_keys = array_unique( $unset_original_keys );
		reset( $unset_original_keys );
		foreach( $unset_original_keys as $tk )
		{
			unset( $values[$tk] );
		}

		$return = array_merge( $values, $return );
		return $return;
	}

	/* this one converts values of the form to the values of the model with the translate conf array */
	protected function _values_form_to_model( $values, $convert_conf = array() )
	{
		$return = array();

		$unset_original_keys = array();
		reset( $convert_conf );
		foreach( $convert_conf as $my_key => $their_keys )
		{
			if( ! isset($values[$my_key]) )
				continue;

			$unset_original_keys[] = $my_key;
			if( is_array($their_keys) )
			{
				for( $ii = 0; $ii < count($their_keys); $ii++ )
				{
					$tk = $their_keys[$ii];
					if( isset($values[$my_key][$ii]) )
					{
						$return[$tk] = $values[$my_key][$ii];
					}
				}
			}
			else
			{
				$return[$tk] = $values[$my_key];
			}
		}

		$unset_original_keys = array_unique( $unset_original_keys );
		reset( $unset_original_keys );
		foreach( $unset_original_keys as $tk )
		{
			unset( $values[$tk] );
		}

		$return = array_merge( $values, $return );
		return $return;
	}

	function orphan_errors()
	{
		$return = array();
		$orphan_keys = array_diff( array_keys($this->errors), array_keys($this->inputs) );
		if( $orphan_keys )
		{
			foreach( $orphan_keys as $k )
			{
				$return[ $k ] = $this->errors[ $k ];
			}
		}
		return $return;
	}
	
}

include_once( dirname(__FILE__) . '/widgets/form/basic.php' );
?>