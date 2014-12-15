<?php
class HC_Form_Input_Textarea extends HC_Form_Input
{
	function render( $more = array() )
	{
		$el = HC_Html_Factory::element( 'textarea' );
		$el->add_attr( 'name', $this->name() );
		$el->add_child( $this->value() );

		return $this->decorate( $this->more($el, $more)->render() );
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

	function render( $more = array() )
	{
		$el = HC_Html_Factory::element( 'select' );
		$el->add_attr( 'name', $this->name() );

		if( isset($more['options']) )
		{
			$this->set_options( $more['options'] );
			unset( $more['options'] );
		}

		$options = $this->options();
		if( is_array($options) )
		{
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
		}

		return $this->decorate( $this->more($el, $more)->render() );
	}
}

class HC_Form_Input_Text extends HC_Form_Input
{
	function render( $more = array() )
	{
		$el = HC_Html_Factory::element( 'input' );
		$el->add_attr( 'type', 'text' );
		$el->add_attr( 'name', $this->name() );
		$el->add_attr( 'value', $this->value() );

		return $this->decorate( $this->more($el, $more)->render() );
	}
}

class HC_Form_Input_Password extends HC_Form_Input
{
	function render( $more = array() )
	{
		$el = HC_Html_Factory::element( 'input' );
		$el->add_attr( 'type', 'password' );
		$el->add_attr( 'name', $this->name() );
		$el->add_attr( 'value', $this->value() );

		return $this->decorate( $this->more($el, $more)->render() );
	}
}

class HC_Form_Input_Radio extends HC_Form_Input
{
	function __construct( $name, $value, $more )
	{
		parent::__construct( $name, $value, $more );
		$this->add_attr( 'name', $name );
		$this->add_attr( 'value', $value );
		$this->add_attr( 'type', 'radio' );
	}
}

class HC_Form_Input_Checkbox extends HC_Form_Input
{
	function __construct( $name, $value, $more )
	{
		if( isset($more['label']) )
		{
			$this->add_addon( $more['label'] );
			unset( $more['label'] );
		}

		parent::__construct( $name, $value, $more );
		$this->add_attr( 'name', $name );
		$this->add_attr( 'value', $value );
		$this->add_attr( 'type', 'checkbox' );

		$this->add_wrap(
			HC_Html_Factory::element('label')
//				->add_attr( 'class', 'checkbox', 1 )
//				->add_attr( 'class', 'inline', 1 )
			);
		$this->add_wrap(
			HC_Html_Factory::element('div')
				->add_attr( 'class', 'checkbox', 1 )
			);

	}
}

class HC_Form_Input_Hidden extends HC_Form_Input
{
	function __construct( $name, $value, $more )
	{
		parent::__construct( $name, $value, $more );
		$this->add_attr( 'name', $name );
		$this->add_attr( 'value', $value );
		$this->add_attr( 'type', 'hidden' );
	}
}
