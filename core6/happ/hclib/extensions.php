<?php
class HC_Extensions
{
	private $dirs = array();
	private $extensions = array();
	private $skip = array();

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
		if( is_array($which) ){
			if( isset($this->extensions[$which[0]][$which[1]]) ){
				$return = TRUE;
			}
		}
		else {
			if( isset($this->extensions[$which]) ){
				$return = TRUE;
			}
		}
		return $return;
	}

	public function set_skip( $skip = array() )
	{
		$this->skip = $skip;
		return $this;
	}
	public function skip()
	{
		return $this->skip;
	}

	public function run( $which )
	{
		$return = array();
		$skip = $this->skip();
		$this->set_skip( array() );

		$params = func_get_args();
		$which = array_shift( $params );

		if( ! $this->has($which) ){
			return $return;
		}

		$calling_parent = '';
		if( is_array($which) ){
			$this_extensions = $this->extensions[$which[0]][$which[1]];
			if( isset($which[2]) ){
				$calling_parent = $which[2];
			}
		}
		else {
			$this_extensions = $this->extensions[$which];
		}
		if( ! is_array($this_extensions) ){
			$this_extensions = array( $this_extensions );
		}

		foreach( $this_extensions as $hk => $hinfo ){
			if( in_array($hk, $skip) ){
				continue;
			}

			/* hinfo is a path to module */
			// Modules::run( $hinfo, $model)
			if( ! is_array($hinfo) ){
				$hinfo = array($hinfo);
			}
			if( $calling_parent ){
				$hinfo[0] = array( $hinfo[0], $calling_parent );
			}

			$this_params = array_merge( $hinfo, $params );
			$this_return = call_user_func_array( 'Modules::run', $this_params );
			if( strlen($this_return) ){
				$return[$hk] = $this_return;
			}
		}
		return $return;
	}
}
