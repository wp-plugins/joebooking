<?php
class ntsObserverManager
{
	var $observers = array();

	function __construct()
	{
		$this->dir = NTS_APP_DIR . '/observers';
		$this->conf_name = 'observers';
		$this->conf_params_name = 'observers_params_';

		$files = ntsLib::listFiles( $this->dir );
		reset( $files );
		foreach( $files as $f )
		{
			include_once( $this->dir . '/' . $f );
			$key = $this->make_key_from_file( $f );
			$className = $this->make_class_from_file( $f );

			$params = $this->get_params( $key );
			$obs = new $className( $params );
			$info = $obs->info();
			if( $info )
			{
				$this->observers[ $key ] = $obs;
			}
		}
	}

	function observe( $action_name, $object, $main_action_name, $params )
	{
		$active_observers = $this->get_active_observers();
		foreach( $active_observers as $ao )
		{
			if( isset($this->observers[$ao]) )
			{
				$obs = $this->observers[$ao];
				$obs->run(
					$action_name,
					$object,
					$main_action_name,
					$params
					);
			}
		}
	}

	function set_enabled( $enabled )
	{
		$conf =& ntsConf::getInstance();
		$conf->set( $this->conf_name, $enabled );
	}

	function set_params( $params )
	{
		$conf =& ntsConf::getInstance();

		$set = array();
		reset( $params );
		foreach( $params as $pn => $pv )
		{
			$setting_pn = $this->conf_params_name . $pn;
			$set[ $setting_pn ] = $pv;
		}

		foreach( $set as $k => $v )
		{
			$conf->set( $k, $v );
		}
	}

	function get_params( $obs_name )
	{
		$return = array();
		$conf =& ntsConf::getInstance();
		$names = $conf->getLoadedNames();
		reset( $names );
		foreach( $names as $n )
		{
			if( substr($n, 0, strlen($this->conf_params_name)) == $this->conf_params_name )
			{
				$pn = substr($n, strlen($this->conf_params_name));
				if( substr($pn, 0, strlen($obs_name . '_')) == ($obs_name . '_') )
				{
					$ppn = substr($pn, strlen($obs_name . '_'));
					$return[ $ppn ] = $conf->get( $n );
				}
			}
		}
		return $return;
	}

	function get_active_observers()
	{
		$conf =& ntsConf::getInstance();
		$return = $conf->get( $this->conf_name );
		if( ! is_array($return) )
		{
			$return = $return ? array( $return ) : array();
		}
		return $return;
	}

	private function make_key_from_file( $file_name )
	{
		$return = $file_name;
		$return = str_replace( '.php', '', $return );
		$return = str_replace( '-', '', $return );
		$return = str_replace( '_', '', $return );
		return $return;
	}

	private function make_class_from_file( $file_name )
	{
		$return = $this->make_key_from_file( $file_name );
		$return = 'ntsObserver' . $return;
		return $return;
	}

	function get_all_observers()
	{
		return $this->observers;
	}

	// Singleton stuff
	static function &getInstance()
	{
		return ntsLib::singletonFunction( 'ntsObserverManager' );
	}
}

class ntsObserver
{
	var $params;

	function __construct( $params )
	{
		$this->params = $params;
	}

	public function is_not_valid()
	{
		return FALSE;
	}

	public function info()
	{
		$return = array(
			'title'			=> '',
			'description'	=> '',
			);
		return $return;
	}
	
	function run( $action_name, $object, $main_action_name, $params )
	{
		return;
	}

	function form()
	{
		$return = array();
		return $return;
	}
}
?>