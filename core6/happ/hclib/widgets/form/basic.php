<?php
class HC_Form_Input extends HC_Html_Element
{
	protected $type = 'text';
	protected $name = 'name';
	protected $error = '';
	protected $value = NULL;
	protected $readonly = FALSE;

	function __construct( $name = '' )
	{
		if( strlen($name) )
			$this->set_name( $name );
	}

	function set_readonly( $readonly = TRUE )
	{
		$this->readonly = $readonly;
		return $this;
	}
	function readonly()
	{
		return $this->readonly;
	}

	/* if fails should return the error message otherwise NULL */
	function _validate()
	{
		$return = NULL;
		return $return;
	}

	/* this will add error messages and help text if needed*/
	function decorate( $return )
	{
		if( $wrap = $this->wrap() )
		{
			foreach( $wrap as $wr )
			{
				$return = $wr->add_child($return)->render();
			}
		}

		$error = $this->error();
		if( $error )
		{
			$return = HC_Html_Factory::widget('container')
				->add_item( $return )
				;
			if( is_array($error) )
			{
				$error = join( ' ', $error );
			}
			$return->add_item(
				HC_Html_Factory::element('span')
					->add_attr('class', 'help-inline')
					->add_child( $error )
				);
			$return = $return->render();
		}

/*
		if( $help )
		{
			$return .= '<span class="help-block">' . $help . '</span>';
		}
*/
		return $return;
	}

	function set_type( $type )
	{
		$this->type = $type;
		return $this;
	}
	function type()
	{
		return $this->type;
	}

	function set_error( $error )
	{
		if( ! $this->error )
			$this->error = $error;
		return $this;
	}
	function error()
	{
		return $this->error;
	}

	function set_value( $value )
	{
		$this->value = $value;
		if( $error = $this->_validate() )
		{
			$this->set_error( $error );
		}
		return $this;
	}
	function value()
	{
		return $this->value;
	}

	function set_name( $name )
	{
		$this->name = $name;
		return $this;
	}
	function name()
	{
		return $this->name;
	}

/* will be overwritten in child classes */
	function grab( $post )
	{
		$name = $this->name();
		$value = NULL;
		if( isset($post[$name]) )
		{
			$value = $post[$name];
		}
		$this->set_value( $value );
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

class HC_Form_Input_Textarea extends HC_Form_Input
{
	function render()
	{
		$el = HC_Html_Factory::element( 'textarea' )
			->add_attr( 'name', $this->name() )
			->add_child( $this->value() )
			->add_attr( 'class', 'form-control' )
			;

		$attr = $this->attr();
		foreach( $attr as $k => $v )
		{
			$el->add_attr($k, $v);
		}

		$return = $this->decorate( $el->render() );
		return $return;
	}
}

class HC_Form_Input_Select extends HC_Form_Input
{
	protected $options = array();

	function set_options( $options )
	{
		$this->options = $options;
	}
	function options()
	{
		return $this->options;
	}

	function render()
	{
		$readonly = $this->readonly();
//$readonly = FALSE;
		$options = $this->options();
		$value = $this->value();

		if( is_array($options) )
		{
			if( $readonly )
			{
				$return = isset($options[$value]) ? $options[$value] : lang('common_na');
			}
			else
			{
				$el = HC_Html_Factory::element( 'select' );
				$el->add_attr( 'class', 'form-control' );
				$el->add_attr( 'name', $this->name() );

				reset( $options );
				foreach( $options as $key => $label )
				{
					$option = HC_Html_Factory::element('option');
					$option->add_attr( 'value', $key );
					$option->add_child( $label );
					if( $this->value() == $key )
					{
						$option->add_attr( 'selected', 'selected' );
					}
					$el->add_child( $option );
				}

				$attr = $this->attr();
				foreach( $attr as $k => $v )
				{
					$el->add_attr($k, $v);
				}

				$return = $el->render();
			}
		}

		$return = $this->decorate( $return );
		return $return;
	}
}

class HC_Form_Input_Text extends HC_Form_Input
{
	function render()
	{
		$el = HC_Html_Factory::element( 'input' )
			->add_attr( 'type', 'text' )
			->add_attr( 'name', $this->name() )
			->add_attr( 'value', $this->value() )
			->add_attr( 'class', 'form-control' )
			;

		$attr = $this->attr();
		foreach( $attr as $k => $v )
		{
			$el->add_attr($k, $v);
		}

		$return = $this->decorate( $el->render() );
		return $return;
	}
}

class HC_Form_Input_Label extends HC_Form_Input
{
	function render()
	{
		$return = $this->value();
		$return = $this->decorate( $return );
		return $return;
	}
}

class HC_Form_Input_Password extends HC_Form_Input
{
	function render()
	{
		$el = HC_Html_Factory::element( 'input' )
			->add_attr( 'type', 'password' )
			->add_attr( 'name', $this->name() )
			->add_attr( 'value', $this->value() )
			->add_attr( 'class', 'form-control' )
			;

		$attr = $this->attr();
		foreach( $attr as $k => $v )
		{
			$el->add_attr($k, $v);
		}

		$return = $this->decorate( $el->render() );
		return $return;
	}
}

class HC_Form_Input_Radio extends HC_Form_Input
{
	function render()
	{
		$el = HC_Html_Factory::element( 'input' )
			->add_attr( 'type', 'radio' )
			->add_attr( 'name', $this->name() )
			->add_attr( 'value', $this->value() )
			->add_attr( 'class', 'form-control' )
			;

		$attr = $this->attr();
		foreach( $attr as $k => $v )
		{
			$el->add_attr($k, $v);
		}

		$return = $this->decorate( $el->render() );
		return $return;
	}
}

class HC_Form_Input_Checkbox extends HC_Form_Input
{
	protected $label = '';

	function set_label( $label = '' )
	{
		$this->label = $label;
	}
	function label()
	{
		return $this->label;
	}

	function render()
	{
		$label = $this->label();

		$el = HC_Html_Factory::element( 'input' )
			->add_attr( 'type', 'checkbox' )
			->add_attr( 'name', $this->name() )
			->add_attr( 'value', $this->value() )
			->add_attr( 'class', 'form-control' )
			;

		$attr = $this->attr();
		foreach( $attr as $k => $v )
		{
			$el->add_attr($k, $v);
		}

		$el = HC_Html_Factory::widget( 'container' )
			->add_item( $el );

		if( strlen($label) )
		{
			$el->add_item($label);
		}

		$out = HC_Html_Factory::element('div')
			->add_attr('class', 'checkbox')
			->add_child(
				HC_Html_Factory::element('label')
					->add_child( $el )
				)
			;

		$return = $this->decorate( $out->render() );
		return $return;
	}
}

class HC_Form_Input_Hidden extends HC_Form_Input
{
	function render()
	{
		$el = HC_Html_Factory::element( 'input' )
			->add_attr( 'type', 'hidden' )
			->add_attr( 'name', $this->name() )
			->add_attr( 'value', $this->value() )
			;

		$return = $this->decorate( $el->render() );
		return $return;
	}
}
