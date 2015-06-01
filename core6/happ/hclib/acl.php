<?php
class HC_Acl
{
	private $dirs = array();
	private $rules = array();
	private $object = NULL;
	private $user = NULL;

	protected function __construct()
	{
	}

	public static function get_instance()
	{
		static $instance = null;
		if( null === $instance ){
			$instance = new HC_Acl();
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
		$acl = array();
		reset( $this->dirs );
		foreach( $this->dirs as $dir ){
			$file = $dir . '/config/acl.php';
			/* should define the $acl array */
			if( file_exists($file) ){
				require( $file );
			}
		}
		$this->rules = $acl;
	}

	public function set_object( $object ){
		$this->object = $object;
		return $this;
	}
	public function object(){
		return $this->object;
	}

	public function set_user( $user ){
		$this->user = $user;
		return $this;
	}
	public function user(){
		return $this->user;
	}

	public function can( $what, $params = array() )
	{
		$return = FALSE;

		$object = $this->object();
		$user = $this->user();
		if( ! $user ){
			$user = HC_App::model('user');
		}

		$check = array();
		if( $object ){
			$check[] = $object->my_class() . '::' . $object->id . '::' . $what;
			$check[] = $object->my_class() . '::' . '*' . '::' . $what;
			$check[] = $object->my_class() . '::' . $what;
			$check[] = $object->my_class() . '::' . '*';
		}
		$check[] = $what;
		$check[] = '*';

		reset( $check );
		foreach( $check as $ch ){
			if( isset($this->rules[$ch]) ){
				$rule = $this->rules[$ch];
				if( is_callable($rule) ){
					$return = $rule($user, $object, $params);
				}
				else {
					$return = $rule;
				}
				if( $return !== NULL ){
					break;
				}
			}
		}
		return $return;
	}

	public function filter( $objects, $what ){
		$return = array();
		foreach( $objects as $obj ){
			if( $this->set_object($obj)->can($what) ){
				$return[] = $obj;
			}
		}
		return $return;
	}
}
