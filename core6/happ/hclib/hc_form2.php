<?php
class HC_Form2
{
	private $inputs = array();
	private $errors = array();
	private $orphan_errors = array();

	private $convert = array();

	function set_input( $name, $type, $convert = array() )
	{
		$input = HC_Html_Factory::input( $type, $name );
		$this->inputs[ $name ] = $input;

		if( $convert ){
			foreach( $convert as $kmodel => $kform ){
				$this->convert[ $kmodel ] = $name . '.' . $kform;
			}
		}

		return $this;
	}
	function input( $name )
	{
		return isset($this->inputs[$name]) ? $this->inputs[$name] : NULL;
 	}

	function set_inputs( $inputs )
	{
		reset( $inputs );
		foreach( $inputs as $name => $type ){
			$this->set_input( $name, $type );
		}
		return $this;
	}

	function convert()
	{
		return $this->convert;
	}

	function grab( $post )
	{
		foreach( array_keys($this->inputs) as $k ){
			$this->inputs[$k]->grab( $post );
		}
	}

	function set_values( $values )
	{
		$values = $this->_values_model_to_form( $values, $this->convert() );
		foreach( array_keys($this->inputs) as $k ){
			if( isset($values[$k]) ){
				$this->inputs[$k]->set_value( $values[$k] );
			}
		}
	}
	function values()
	{
		$return = array();
		foreach( array_keys($this->inputs) as $k ){
			$return[$k] = $this->inputs[$k]->value();
		}
		$return = $this->_values_form_to_model( $return, $this->convert() );
		return $return;
	}

	function set_errors( $errors )
	{
		$this->errors = array();
		$this->orphan_errors = array();

		$errors = $this->_errors_model_to_form( $errors, $this->convert() );

		$input_names = array_keys($this->inputs);
		foreach( $errors as $k => $e ){
			if( in_array($k, $input_names) ){
				$this->inputs[$k]->set_error( $e );
				$this->errors[$k] = $e;
			}
			else {
				$this->orphan_errors[$k] = $e;
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
		$return = $errors;
		foreach( $convert_conf as $mkey => $fkey ){
			list( $finput, $fkey ) = explode('.', $fkey);
			if( isset($errors[$mkey]) ){
				unset($return[$mkey]);
				$return[$finput] = $errors[$mkey];
			}
		}
		return $return;
	}

	/* this one converts values of model to the values of the form with the translate conf array */
	protected function _values_model_to_form( $values, $convert_conf = array() )
	{
		$return = $values;
		foreach( $convert_conf as $mkey => $fkey ){
			list( $finput, $fkey ) = explode('.', $fkey);
			if( isset($values[$mkey]) ){
				unset($return[$mkey]);
				$return[$finput][$fkey] = $values[$mkey];
			}
		}
		return $return;
	}

	/* this one converts values of the form to the values of the model with the translate conf array */
	protected function _values_form_to_model( $values, $convert_conf = array() )
	{
		$return = $values;
		foreach( $convert_conf as $mkey => $fkey ){
			list( $finput, $fkey ) = explode('.', $fkey);
			if( isset($values[$finput][$fkey]) ){
				unset($return[$finput]);
				$return[$mkey] = $values[$finput][$fkey];
			}
		}
		return $return;
	}

	function orphan_errors()
	{
		return $this->orphan_errors;
	}
}

include_once( dirname(__FILE__) . '/widgets/form/basic.php' );
?>