<?php
class HC_Extensions
{
	private $dirs = array();
	private $extensions = array();

	protected function __construct()
	{
	}

	public static function get_instance()
	{
		static $instance = null;
		if( null === $instance ){
			$instance = new HC_Extensions();
		}
		return $instance;
	}

	public function add_dir( $dir )
	{
		if( ! in_array($dir, $this->dirs) ){
			$this->dirs[] = $dir;
		}
	}

	public function init()
	{
		$extensions = array();
		reset( $this->dirs );
		foreach( $this->dirs as $dir ){
			$file = $dir . '/config/extensions.php';
			/* should define the $extensions array */
			if( file_exists($file) ){
				require( $file );
			}
		}
		$this->extensions = $extensions;
	}

	public function subextensions( $start )
	{
		$return = array();

		$params = func_get_args();
		$start = array_shift( $params );

		reset( $this->extensions );
		foreach( $this->extensions as $hk => $ha ){
			if( substr($hk, 0, strlen($start)) == $start ){
				$remain = substr($hk, strlen($start) + 1);
				if( strlen($remain) ){
					if( $ha === NULL ){
						$return[ $remain ] = $ha;
					}
					else {
						$this_params = array_merge( array($hk), $params );
						$return[ $remain ] = call_user_func_array( array($this, 'run'), $this_params );
					}
				}
			}
		}
		return $return;
	}

	public function has( $which )
	{
		$return = FALSE;
		if( isset($this->extensions[$which]) ){
			$return = TRUE;
		}
		return $return;
	}

	public function run( $which )
	{
		$return = array();

		$params = func_get_args();
		$which = array_shift( $params );

		if( ! $this->has($which) ){
			return $return;
		}

		$this_extensions = $this->extensions[$which];
		if( ! is_array($this_extensions) ){
			$this_extensions = array( $this_extensions );
		}
		foreach( $this_extensions as $hinfo ){
			/* hinfo is a path to module */
			// Modules::run( $hinfo, $model)
			if( ! is_array($hinfo) ){
				$hinfo = array($hinfo);
			}

			$this_params = array_merge( $hinfo, $params );
			$this_return = call_user_func_array( 'Modules::run', $this_params );
			if( strlen($this_return) ){
				$return[] = $this_return;
			}
		}
		return $return;
	}
}
