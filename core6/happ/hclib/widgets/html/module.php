<?php
class HC_Html_Widget_Module
{
	private $slug = array();
	private $self_target = TRUE;
	private $params = array();

	function set_param( $param, $value )
	{
		$this->params[ $param ] = $value;
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

	function set_slug( $slug )
	{
		$this->slug = $slug;
		return $this;
	}
	function slug()
	{
		return $this->slug;
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
		$link = HC_Lib::link( $this->slug() );

		$module_params = $this->slug();
		foreach( $this->params() as $k => $v )
		{
			$module_params[] = $k;
			$module_params[] = $v;
		}

		$return = call_user_func_array( 'Modules::run', $module_params );

		if( strlen($return) && $this->self_target() )
		{
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