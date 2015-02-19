<?php
include_once( dirname(__FILE__) . '/hc_object_cache.php' );

class HC_Presenter
{
	const VIEW_HTML = 2;
	const VIEW_HTML_ICON = 3;
	const VIEW_TEXT = 1;
	const VIEW_RAW = 0;

	public function label( $model, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		$return = $model->my_class();
		return $return;
	}

	public function property_name( $model, $pname, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		return $pname;
	}

	public function color( $model )
	{
		$return = Hc_lib::random_html_color( $model->id );
		return $return;
	}
}

class HC_Page_Params
{
	private $params = array();
	private $skip = array();

	public function slug()
	{
		$array = $this->to_array();
		$return = array();
		foreach( $array as $k => $v )
		{
			$return[] = $k;
			$return[] = $v;
		}
		return $return;
	}

	public function skip( $skip = array() )
	{
		$this->skip = $skip;
		return $this;
	}

	public function set( $key, $value )
	{
		$this->params[$key] = $value;
	}

	public function reset( $key )
	{
		unset( $this->params[$key] );
	}

	public function get( $key )
	{
		$return = NULL;
		if( isset($this->params[$key]) )
		{
			$return = $this->params[$key];
		}
		return $return;
	}

	public function to_array()
	{
		$return = array();
		foreach( $this->params as $k => $v )
		{
			if( in_array($k, $this->skip) )
				continue;
			$return[ $k ] = $v;
		}
		$this->skip( array() );
		return $return;
	}

	public function get_keys()
	{
		return array_keys($this->params);
	}
}

class HC_App
{
	static function app_conf()
	{
		$CI =& ci_get_instance();
		if( isset($CI->app_conf) )
			return $CI->app_conf;
		else
			return NULL;
	}

	static function csrf()
	{
		$csrf_name = '';
		$csrf_value = '';
		$CI =& ci_get_instance();
		if ($CI->config->item('csrf_protection') )
		{
			$csrf_name = $CI->security->get_csrf_token_name();
			$csrf_value = $CI->security->get_csrf_hash();
		}
		return array( $csrf_name, $csrf_value );
	}

	static function presenter( $model )
	{
		$return = NULL;

		$class = ucfirst($model) . '_HC_Presenter';
		if( class_exists($class) ){
			$return = new $class;
		}

		return $return;
	}

	static function short_model( $model ){
		$model = strtolower($model);
		if( substr($model, -strlen('_hc_model')) == '_hc_model' ){
			$model = substr($model, 0, -strlen('_hc_model'));
		}
		return $model;
	}

	static function full_model( $model ){
		$model = strtolower($model);
		if( substr($model, -strlen('_hc_model')) != '_hc_model' ){
			$model = $model . '_hc_model';
		}
		return $model;
	}

	static function model( $model )
	{
		$model = HC_App::full_model( $model );
		$return = new $model;
		return $return;
	}

	static function icon_for( $class )
	{
		$return = '';
		$conf = array(
			'date'		=> 'calendar',
			'time'		=> 'clock-o',
			'shift'		=> 'clock-o',
			'timeoff'	=> 'coffee',
			'user'		=> 'user',
			'location'	=> 'home',
			'trade'		=> 'exchange',
			'trade'		=> 'refresh',
			);
		if( isset($conf[$class]) )
			$return = $conf[$class];
		return $return;
	}

	static function widget_locations()
	{
		$return = array();
		$return['HC'] = dirname(__FILE__) . '/widgets';
		if( defined('APPPATH') )
		{
			$return['SFT'] = APPPATH . 'widgets';
		}
		return $return;
	}
}

class HC_Link
{
	private $args = array();
	private $params = array();
	private $controller = '';

	function __construct( $init = '' )
	{
		if( is_array($init) )
		{
			$controller = array_shift( $init );
			$this->set_controller( $controller );
			$params = hc_parse_args( $init );
			foreach( $params as $k => $v )
			{
				$this->set_param($k, $v);
			}
		}
		else
		{
			$this->set_controller( $init );
		}
	}

	function set_controller( $controller )
	{
		$this->controller = $controller;
		return $this;
	}
	function controller()
	{
		return $this->controller;
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

	function append_param( $key, $value )
	{
		if( array_key_exists($key, $this->params) )
		{
			$current_value = $this->params[$key];
			if( ! is_array($current_value) )
				$current_value = array( $current_value );
			$current_value[] = $value;
			$value = $current_value;
		}

		if( ! is_array($value) )
			$value = array( $value );

		$this->params[$key] = $value;
		return $this;
	}

	function reset_params()
	{
		$this->params = array();
	}

	function set_params( $params )
	{
		foreach( $params as $k => $v )
		{
			$this->set_param( $k, $v );
		}
		return $this;
	}
	function set_param( $key, $value )
	{
		$this->params[ $key ] = $value;
		return $this;
	}
	function params()
	{
		return $this->params;
	}
	function param( $key )
	{
		return isset($this->params[$key]) ? $this->params[$key] : NULL;
	}

	function slug()
	{
		$args = func_get_args();

		$method = '';
		$change_params = array();

		if( count($args) == 2 ){
			$method = $args[0];
			$change_params = $args[1];
		}
		elseif( count($args) == 1 ){
			if( is_array($args[0]) ){
				$method = '';
				$change_params = $args[0];
			}
			else {
				$method = $args[0];
				$change_params = array();
			}
		}

		$return = array();

		$params = $this->params();
		foreach( $change_params as $k => $v ){
			$params[$k] = $v;
		}

		$controller = array();
		$controller[] = $this->controller();
		if( $method ){
			$controller[] = $method;
		}
		$controller = join( '/', $controller );

		$return[] = $controller;

		foreach( $this->args() as $a ){
			$return[] = $a;
		}

		if( $params ){
			foreach( $params as $k => $v ){
				$return[] = $k;
				if( is_array($v) ){
					$v = join('.', $v);
				}
				$return[] = $v;
			}
		}

		$this->reset_params();
		return $return;
	}

	function url()
	{
		$args = func_get_args();
		$slug = call_user_func_array( array($this, 'slug'), $args );
		$return = ci_site_url( $slug );
		return $return;
	}

	function url_short()
	{
		$slug = $this->slug();
		$return = join('/', $slug );
		return $return;
	}

	public function __toString()
	{
		return $this->url();
    }
}

class Hc_lib {
	static function array_skip_after( $src, $after, $include = TRUE )
	{
		$return = array();
		foreach( $src as $k )
		{
			if( $k == $after )
			{
				if( $include )
					$return[] = $k;
				break;
			}
			$return[] = $k;
		}
		return $return;
	}

	static function array_remain_after( $src, $after, $include = TRUE )
	{
		$return = array();
		$ok = FALSE;
		foreach( $src as $k )
		{
			if( $k == $after )
			{
				$ok = TRUE;
				if( ! $include )
					continue;
			}
			if( $ok )
				$return[] = $k;
		}
		return $return;
	}


	static function array_intersect_by_key( $src, $keys )
	{
		$out = array();
		foreach( $keys as $k )
		{
			if( array_key_exists($k, $src) )
			{
				$out[ $k ] = $src[ $k ];
			}
		}
		return $out;
	}

	static function generate_rand( $len = 12, $conf = array() )
	{
		$useLetters = isset($conf['letters']) ? $conf['letters'] : TRUE;
		$useHex = isset($conf['hex']) ? $conf['hex'] : FALSE;
		$useDigits = isset($conf['digits']) ? $conf['digits'] : TRUE;
		$useCaps = isset($conf['caps']) ? $conf['caps'] : FALSE;

		$salt = '';
		if( $useHex )
			$salt .= '0123456789abcdef';
		if( $useLetters )
			$salt .= 'abcdefghijklmnopqrstuvxyz';
		if( $useDigits )
			$salt .= '0123456789';
		if( $useCaps )
			$salt .= 'ABCDEFGHIJKLMNOPQRSTUVXYZ';

		srand( (double) microtime() * 1000000 );
		$return = '';
		$i = 1;
		$array = array();
		while ( $i <= $len ){
			$num = rand() % strlen($salt);
			$tmp = substr($salt, $num, 1);
			$array[] = $tmp;
			$i++;
			}
		shuffle( $array );
		$return = join( '', $array );
		return $return;
	}

	static function is_ajax()
	{
		$return = FALSE;
		if( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') )
		{
			$return = TRUE;
		}
		return $return;
	}

	static function app()
	{
		$return = '';
		if( isset($GLOBALS['NTS_APP']))
		{
			$return = $GLOBALS['NTS_APP'];
		}
		return $return;
	}

	static function nts_config()
	{
		$return = array();
		$app = HC_Lib::app();
		if( isset($GLOBALS['NTS_CONFIG'][$app]) )
		{
			$return = $GLOBALS['NTS_CONFIG'][$app];
		}
		return $return;
	}

	static function ri()
	{
		$return = '';
		$nts_config = HC_Lib::nts_config();
		if( isset($nts_config['REMOTE_INTEGRATION']) )
		{
			$return = $nts_config['REMOTE_INTEGRATION'];
		}
		return $return;
	}

	static function is_full_url( $url )
	{
		$full = FALSE;
		if( is_array($url))
		{
			return $full;
		}

		$prfx = array('http://', 'https://', '//');
		reset( $prfx );
		foreach( $prfx as $prf )
		{
			if( substr($url, 0, strlen($prf)) == $prf )
			{
				$full = TRUE;
				break;
			}
		}
		return $full;
	}

	static function cache()
	{
		$return = new HC_Object_Cache();
		return $return;
	}

	static function link( $start = '' )
	{
		$return = new HC_Link( $start );
		return $return;
	}

	static function form()
	{
		$return = new HC_Form2;
		return $return;
	}

	static function time()
	{
		$return = new HC_Time;
		return $return;
	}

	static function ob_start()
	{
		ob_start();
	}
	static function ob_end()
	{
		$return = ob_get_contents();
		ob_end_clean();
		return $return;
	}

	static function sort_array_by_array( $array, $orderArray )
	{
		$return = array();
		reset( $orderArray );
		foreach( $orderArray as $o )
		{
			if( in_array($o, $array) )
			{
				$return[] = $o;
			}
		}
		reset( $array );
		foreach( $array as $a )
		{
			if( ! in_array($a, $return) )
				$return[] = $a;
		}
		return $return;
	}

	static function ksort_array_by_array( $array, $orderArray )
	{
		$return = array();
		reset( $orderArray );
		foreach( $orderArray as $o )
		{
			if( array_key_exists($o, $array) )
			{
				$return[$o] = $array[$o];
			}
		}
		reset( $array );
		foreach( $array as $k => $k )
		{
			if( ! array_key_exists($k, $return) )
				$return[$k] = $v;
		}
		return $return;
	}

	static function random_html_color( $i )
	{
		$out = array(
			'#0000dd',
			'#dd0000',
			'#7F5417',
			'#21B6A8',
			'#87907D',
			'#ec6d66',
			'#177F75',
			'#B6212D',
			'#B67721',
			'#da2d8b',
			'#FF8000',
			'#61e94c',
			'#FFAABF',
			'#91C3DC',
			'#FFCC00',
			'#E5E0C1',
			'#68BD66',
			'#179CE8',
			'#BBFF20',
			'#30769E',
			'#FFE500',
			'#C8E9FC',
			'#758a09',
			'#00CCFF',
			'#FFC080',
			'#4086AA',
			'#FFAABF',
			'#0000AA',
			'#AA6363',
			'#AA9900',
			'#1A8BC0',
			'#ECF8FF',
			'#758a09',
			'#dd3100',
			'#dea04a',
			'#af2a30',
			'#EECC99',
			'#179999',
			'#BBFF20',
			'#a92e03',
			'#dd9cc9',
			'#f30320',
			'#579108',
			'#ce9135',
			'#acd622',
			'#e46e46',
			'#53747d',
			'#36a62a',
			'#83877e',
			'#e82385',
			'#73f2f2',
			'#cb9fa4',
			'#12c639',
			'#f51b2b',
			'#985d27',
			'#3595d5',
			'#cb9987',
			'#d52192',
			'#695faf',
			'#de2426',
			'#295d5a',
			'#824b2d',
			'#08ccf6',
			'#e82a3c',
			'#fcd11a',
			'#2b4c04',
			'#3011fd',
			'#1df37b',
			'#af2a30',
			'#c456d1',
			'#dcf174',
			'#025df6',
			'#0ab24f',
			'#c0d962',
			'#62369f',
			'#73faa9',
			'#fb453c',
			'#0487a4',
			'#ce9e07',
			'#2b407e',
			'#c28551',
			);
   
		if( $i > count($out) )
		{
			$i = $i % count($out);
		}

		$return = $out[$i - 1];
		return $return;
	}

	static function pick_random( $array, $many = 1 )
	{
		if( $many > 1 )
		{
			$return = array();
			$ids = array_rand($array, $many );
			foreach( $ids as $id )
				$return[] = $array[$id];
		}
		else
		{
			$id = array_rand($array);
			$return = $array[$id];
		}
		return $return;
	}

	static function remove_from_array( $array, $what, $all = TRUE )
	{
		$return = $array;
		for( $ii = count($return) - 1; $ii >= 0; $ii-- )
		{
			if( $return[$ii] == $what )
			{
				array_splice( $return, $ii, 1 );
				if( ! $all )
				{
					break;
				}
			}
		}
		return $return;
	}

	static function file_set_contents( $fileName, $content )
	{
		$length = strlen( $content );
		$return = 1;

		if(! $fh = fopen($fileName, 'w') ){
			echo "can't open file <B>$fileName</B> for wrinting.";
			exit;
			}
		rewind( $fh );
		$writeResult = fwrite($fh, $content, $length);
		if( $writeResult === FALSE )
			$return = 0;

		return $return;
	}

	static function parse_lang( $label )
	{
		$lang_pref = 'lang:';
		if( substr($label, 0, strlen($lang_pref)) == $lang_pref )
		{
			$label = substr($label, strlen($lang_pref));
			$label = lang( $label );
		}
		return $label;
	}

	static function parse_icon( $title, $add_fw = TRUE )
	{
		$icon_start = strpos( $title, '<i' );
		if( $icon_start !== FALSE )
		{
			$icon_end = strpos( $title, '</i>' ) + 4; 
			$link_icon = substr( $title, 0, $icon_end );
			$link_title = substr( $title, $icon_end );
		}
		else
		{
			$link_title = strip_tags( $title );
			$link_icon = '';
		}

		if( $link_icon && $add_fw )
		{
			$icon_class_start = strpos( $link_icon, 'class=' ) + 6;
			if( $icon_class_start !== FALSE )
			{
				$icon_start = substr( $link_icon, 0, $icon_class_start + 1 );
				$icon_end = substr( $link_icon, $icon_class_start + 1 );
				if( strpos($link_icon, 'fa-fw') === FALSE )
				{
					$link_icon = $icon_start . 'fa-fw ' . $icon_end;
				}
			}
		}

		$link_icon = trim( $link_icon );
		$return = array( $link_title, $link_icon );
		return $return;
	}

	static function parse_icon_old( $title, $add_fw = TRUE )
	{
		if( preg_match('/(\<i.+\>.*\<\/i\>\s*)(.+)/', $title, $ma) )
		{
			$link_title = $ma[2];
			$link_icon = $ma[1];
		}
		else
		{
			$link_title = strip_tags( $title );
			$link_icon = '';
		}

		if( $link_icon && $add_fw )
		{
			if( preg_match('/\<i.+class\=[\'\"](.+)[\'\"]\>\<\/i\>/', $title, $ma2) )
			{
				$class = $ma2[1];
				if( strpos($class, 'fa-fw') === FALSE )
				{
					$new_class = 'fa-fw ' . $class;
					$link_icon = str_replace( $class, $new_class, $link_icon );
				}
			}
		}

		$link_icon = trim( $link_icon );
		$return = array( $link_title, $link_icon );
		return $return;
	}
}
