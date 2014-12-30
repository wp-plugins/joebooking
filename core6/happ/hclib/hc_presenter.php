<?php
class HC_Presenter
{
	protected $model;

	function __construct( $model = NULL )
	{
		if( $model )
		{
			$this->set_model( $model );
		}
	}

	function set_model( $model )
	{
		$this->model = $model;
	}

	public function __get($key)
	{
		$prfx = 'present_';
		if( substr($key, 0, strlen($prfx)) == $prfx )
		{
			$key = substr($key, strlen($prfx));
			if( method_exists($this, $key) )
			{
				return $this->{$key}();
			}
			else
			{
				return $this->model->$key;
			}
		}
		return $this->model->$key;
	}

	public function __call( $method, $args )
	{
		return call_user_func_array(array($this->model, $method), $args);
	}
}