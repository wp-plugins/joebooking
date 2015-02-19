<?php
class HC_Html_Widget_Module extends HC_Html_Element
{
	private $self_target = TRUE;

	private $url = '';
	private $params = array();
	private $pass_params = array();
	private $args = array();

	function set_param( $param, $value )
	{
		$this->params[$param] = $value;
		return $this;
	}
	function params()
	{
		return $this->params;
	}
	function param( $key )
	{
		$return = isset($this->params[$key]) ? $this->params[$key] : '';
		return $return;
	}

	function pass_arg( $arg )
	{
		$this->args[] = $arg;
		return $this;
	}
	function args()
	{
		return $this->args;
	}

	function pass_param( $param, $value )
	{
		$this->pass_params[$param] = $value;
		return $this;
	}
	function more_params()
	{
		return $this->pass_params;
	}

	function set_url( $url )
	{
		$this->url = $url;
		return $this;
	}
	function url()
	{
		return $this->url;
	}

	function set_self_target( $self_target = TRUE )
	{
		$this->self_target = $self_target;
		return $this;
	}
	function self_target()
	{
		return $this->self_target;
	}

	function render()
	{
		$module_params = array();
		$module_params[] = $this->url();

		$link = HC_Lib::link( $this->url() );

		foreach( $this->args() as $k ){
			$module_params[] = $k;
			$link->pass_arg($k);
		}

		foreach( $this->params() as $k => $v ){
			$module_params[] = $k;
			$module_params[] = $v;
			$link->set_param($k, $v);
		}

		foreach( $this->more_params() as $k => $v ){
			$module_params[] = $k;
			$module_params[] = $v;
		}

		$return = call_user_func_array( 'Modules::run', $module_params );
		if( strlen($return) && $this->self_target() ){
			$out = HC_Html_Factory::element('div')
				->add_attr('class', 'hc-target')
				->add_attr('data-src', $link->url())
				->add_child(
					$return
					)
				;
			$return = $out->render();
		}
		return $return;
	}
}
?>