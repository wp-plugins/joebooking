<?php
class HC_Form_Input_Checkbox_Set extends HC_Form_Input
{
	protected $options = array();
	protected $readonly = array();
	protected $value = array();

	function add_option( $value, $label = '' )
	{
		$this->options[$value] = $label;
		return $this;
	}
	function options()
	{
		return $this->options;
	}

	function set_readonly( $value, $ro = TRUE )
	{
		$this->readonly[$value] = $ro;
		return $this;
	}
	function readonly( $value = NULL )
	{
		if( $value === NULL ){
			$return = $this->readonly;
		}
		else {
			$return = ( array_key_exists($value, $this->readonly) && $this->readonly[$value] ) ? TRUE : FALSE;
		}
		return $return;
	}

	function grab( $post )
	{
		$name = $this->name();
		$value = array();
		if( isset($post[$name]) )
		{
			$value = $post[$name];
		}
		$this->set_value( $value );
	}

	function render_one( $value, $decorate = TRUE )
	{
		$options = $this->options();
		$full_value = $this->value();
		$label = $options[$value];

		$sub_el = HC_Html_Factory::input('checkbox', $this->name() . '[]' )
			->set_my_value($value)
			;
		if( $this->readonly($value) ){
			$sub_el->set_readonly();
			}
		if( strlen($label) ){
			$sub_el->set_label( $label );
		}
		if( in_array($value, $full_value) ){
			$sub_el->set_value(1);
		}

		if( $decorate ){
			$return = $this->decorate( $sub_el->render() );
		}
		else {
			$return = $sub_el->render($decorate);
		}
		return $return;
	}

	function render()
	{
		$options = $this->options();
		$full_value = $this->value();

		$el = HC_Html_Factory::widget('list')
			->add_attr('class', array('list-inline', 'list-separated'))
			;
		foreach( $options as $value => $label ){
			$el->add_item( $this->render_one($value) );
		}

		$return = $this->decorate( $el->render() );
		return $return;
	}
}