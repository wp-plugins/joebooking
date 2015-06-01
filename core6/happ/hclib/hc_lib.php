<?php
include_once( dirname(__FILE__) . '/hc_object_cache.php' );

// --------------------------------------------------------------------

if ( ! function_exists('hc_serialize'))
{
	function hc_serialize( $array )
	{
		$return = array();

		foreach( $array as $subarray ){
			foreach( $subarray as $k => $v ){
				if( is_object($v) ){
					$v = array( $v->id );
				}
				elseif( is_array($v) ){
				}
				else {
					$v = array( $v );
				}

				if( ! isset($return[$k]) ){
					$return[$k] = array();
				}
				$return[$k] = array_merge( $return[$k], $v );
				$return[$k] = array_unique( $return[$k] );
			}
		}
		$return = serialize( $return );
		return $return;
	}
}

/**
 * Plural
 *
 * Takes a singular word and makes it plural
 *
 * @access	public
 * @param	string
 * @param	bool
 * @return	str
 */
if ( ! function_exists('hc_plural'))
{
	function hc_plural($str, $force = FALSE)
	{
		$result = strval($str);

		$plural_rules = array(
			'/^(ox)$/'                 => '\1\2en',     // ox
			'/([m|l])ouse$/'           => '\1ice',      // mouse, louse
			'/(matr|vert|ind)ix|ex$/'  => '\1ices',     // matrix, vertex, index
			'/(x|ch|ss|sh)$/'          => '\1es',       // search, switch, fix, box, process, address
			'/([^aeiouy]|qu)y$/'       => '\1ies',      // query, ability, agency
			'/(hive)$/'                => '\1s',        // archive, hive
			'/(?:([^f])fe|([lr])f)$/'  => '\1\2ves',    // half, safe, wife
			'/sis$/'                   => 'ses',        // basis, diagnosis
			'/([ti])um$/'              => '\1a',        // datum, medium
			'/(p)erson$/'              => '\1eople',    // person, salesperson
			'/(m)an$/'                 => '\1en',       // man, woman, spokesman
			'/(c)hild$/'               => '\1hildren',  // child
			'/(buffal|tomat)o$/'       => '\1\2oes',    // buffalo, tomato
			'/(bu|campu)s$/'           => '\1\2ses',    // bus, campus
			'/(alias|status|virus)/'   => '\1es',       // alias
			'/(octop)us$/'             => '\1i',        // octopus
			'/(ax|cris|test)is$/'      => '\1es',       // axis, crisis
			'/s$/'                     => 's',          // no change (compatibility)
			'/$/'                      => 's',
		);

		foreach ($plural_rules as $rule => $replacement){
			if (preg_match($rule, $result)){
				$result = preg_replace($rule, $replacement, $result);
				break;
			}
		}
		return $result;
	}
}

// --------------------------------------------------------------------

/**
 * Singular
 *
 * Takes a plural word and makes it singular
 *
 * @access	public
 * @param	string
 * @return	str
 */
if ( ! function_exists('hc_singular'))
{
	function hc_singular($str)
	{
		$result = strval($str);

		$singular_rules = array(
			'/(matr)ices$/'         => '\1ix',
			'/(vert|ind)ices$/'     => '\1ex',
			'/^(ox)en/'             => '\1',
			'/(alias)es$/'          => '\1',
			'/([octop|vir])i$/'     => '\1us',
			'/(cris|ax|test)es$/'   => '\1is',
			'/(shoe)s$/'            => '\1',
			'/(o)es$/'              => '\1',
			'/(bus|campus)es$/'     => '\1',
			'/([m|l])ice$/'         => '\1ouse',
			'/(x|ch|ss|sh)es$/'     => '\1',
			'/(m)ovies$/'           => '\1\2ovie',
			'/(s)eries$/'           => '\1\2eries',
			'/([^aeiouy]|qu)ies$/'  => '\1y',
			'/([lr])ves$/'          => '\1f',
			'/(tive)s$/'            => '\1',
			'/(hive)s$/'            => '\1',
			'/([^f])ves$/'          => '\1fe',
			'/(^analy)ses$/'        => '\1sis',
			'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/' => '\1\2sis',
			'/([ti])a$/'            => '\1um',
			'/(p)eople$/'           => '\1\2erson',
			'/(m)en$/'              => '\1an',
			'/(s)tatuses$/'         => '\1\2tatus',
			'/(c)hildren$/'         => '\1\2hild',
			'/(n)ews$/'             => '\1\2ews',
			'/([^u])s$/'            => '\1',
		);

		foreach ($singular_rules as $rule => $replacement){
			if (preg_match($rule, $result)){
				$result = preg_replace($rule, $replacement, $result);
				break;
			}
		}
		return $result;
	}
}

if ( ! function_exists('hc_ci_before_exit'))
{
	function hc_ci_before_exit()
	{
	/* this is a hack to ensure that post controller and post system hooks are triggered */
		$GLOBALS['EXT']->_call_hook('post_controller');
		$GLOBALS['EXT']->_call_hook('post_system');
	}
}

if ( ! function_exists('hc_run_notifier'))
{
	function hc_run_notifier()
	{
		static $already_run = 0;
		if( $already_run ){
			return;
		}

		$already_run = 1;
		$notifier = HC_App::model('messages');
		if( isset($notifier) ){
			$notifier->run();
		}

		// $notifier = HC_App::notifier();
		// if( isset($notifier) ){
			// $notifier->run();
		// }
	}
}

if ( ! function_exists('hc_parse_args'))
{
	function hc_parse_args( $args, $multiple_values = FALSE )
	{
		$return = array();
		for( $ii = 0; $ii < count($args); $ii = $ii + 2 ){
			if( isset($args[$ii + 1]) ){
				$k = $args[$ii];
				$v = $args[$ii + 1];
				if( $multiple_values && (strpos($v, '.') !== FALSE) ){
					$v = explode('.', $v);
				}
				$return[ $k ] = $v;
			}
		}
		return $return;
	}
}

if ( ! function_exists('_print_r'))
{
	function _print_r( $thing )
	{
		echo '<pre>';
		print_r( $thing );
		echo '</pre>';
	}
}

if ( ! function_exists('hc_random'))
{
	function hc_random( $len = 8 )
	{
		$salt1 = '0123456789';
		$salt2 = 'abcdef';

//		$salt .= 'abcdefghijklmnopqrstuvxyz';
//		$salt .= 'ABCDEFGHIJKLMNOPQRSTUVXYZ';

		srand( (double) microtime() * 1000000 );
		$return = '';
		$i = 1;
		$array = array();

		while ( $i <= ($len - 1) ){
			$num = rand() % strlen($salt1 . $salt2);
			$tmp = substr($salt1 . $salt2, $num, 1);
			$array[] = $tmp;
			$i++;
			}
		shuffle( $array );

	// first is letter
		$num = rand() % strlen($salt2);
		$tmp = substr($salt2, $num, 1);
		array_unshift($array, $tmp);

		$return = join( '', $array );
		return $return;
	}
}

class HC_Presenter
{
	const VIEW_HTML = 2;
	const VIEW_HTML_ICON = 3;
	const VIEW_TEXT = 1;
	const VIEW_RAW = 0;

	public function errors( $model, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		$errors = $model->errors();

		switch( $vlevel ){
			case HC_PRESENTER::VIEW_HTML:
				$out = HC_Html_Factory::widget('list')
					->add_attr('class', 'list-unstyled')
					;
				break;
			default:
				$out = array();
				break;
		}

		foreach( $errors as $pname => $text ){
			switch( $vlevel ){
				case HC_PRESENTER::VIEW_HTML:
					$this_out = HC_Html_Factory::widget('list')
						->add_attr('class', 'list-inline')
						->add_attr('class', 'list-separated-hori')
						;
					$this_out->add_item( $model->present_property_name($pname) . ':' );
					$this_out->add_item( $text );
					$out->add_item( $this_out );
					break;
				default:
					$this_out = array();
					$this_out[] = $model->present_property_name($pname) . ': ';
					$this_out[] = $text;
					$out[] = join('', $this_out);
					break;
			}
		}

		switch( $vlevel ){
			case HC_PRESENTER::VIEW_HTML:
				$out = $out->render();
				break;
			default:
				$out[] = join("\n", $this_out);
				break;
		}

		return $out;
	}

	public function label( $model, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		$return = $model->my_class();
		return $return;
	}

	public function property_name( $model, $pname, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		switch( $pname ){
			case 'status':
				$return = lang('common_status');
				break;
			default:
				$return = $pname;
			}
		return $return;
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
	private $options = array();

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

	public function set_options( $key, $options ){
		$this->options[$key] = $options;
	}
	public function get_options( $key ){
		$return = NULL;
		if( isset($this->options[$key]) ){
			$return = $this->options[$key];
		}
		return $return;
	}

	public function reset( $key )
	{
		unset( $this->params[$key] );
	}

	public function get( $key )
	{
		$return = NULL;
		if( isset($this->params[$key]) ){
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
	static function app()
	{
		$return = '';
		if( isset($GLOBALS['NTS_APP'])){
			$return = $GLOBALS['NTS_APP'];
		}
		return $return;
	}

	static function acl()
	{
		$return = HC_Acl::get_instance();
		return $return;
	}

	static function extensions()
	{
		$return = HC_Extensions::get_instance();
		return $return;
	}

	static function notifier()
	{
		$return = HC_Notifier::get_instance();
		return $return;
	}

	static function app_conf()
	{
		return HC_App::model('app_conf');
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
		$return = NULL;
		$model = HC_App::full_model( $model );
		if( method_exists($model, 'get_instance')){
			$return = call_user_func(array($model, 'get_instance'));
			// $return = $model::get_instance();
		}
		elseif( class_exists($model) ){
			$return = new $model;
		}
		return $return;
	}

	static function icon_for( $class )
	{
		$return = '';
		$conf = array(
			'date'		=> 'calendar',
			'time'		=> 'clock-o',
			'shift'		=> 'clock-o',
			'shift'		=> 'gavel',
			'timeoff'	=> 'coffee',
			'user'		=> 'user',
			'location'	=> 'home',
			'trade'		=> 'exchange',
			'trade'		=> 'refresh',
			'conflict'	=> 'exclamation-circle',
			);
		if( isset($conf[$class]) )
			$return = $conf[$class];
		return $return;
	}

	static function widget_locations()
	{
		$return = array();
		$return['HC'] = dirname(__FILE__) . '/widgets';
		if( defined('APPPATH') ){
			$return['SFT'] = APPPATH . 'widgets';
		}
		return $return;
	}
}

class HC_Link
{
	private $controller = '';
	private $params = array();

	function __construct( $controller = '', $params = array() )
	{
		$this->controller = $controller;
		$this->params = $params;
	}

	function url()
	{
		$return = NULL;

		$append_controller = '';
		$change_params = array();

		$args = func_get_args();
		$ri = HC_Lib::ri();
		if( $ri && (count($args) == 0) && (count($this->params) == 0) ){
			switch( $this->controller ){
				case 'auth/login':
					$return = Modules::run( $ri . '/auth/login_url' );
					break;
				case 'auth/logout':
					$return = Modules::run( $ri . '/auth/logout_url' );
					break;
			}
		}
		if( $return ){
			return $return;
		}

		if( count($args) == 1 ){
			list( $change_params ) = $args;
		}
		elseif( count($args) == 2 ){
			list( $append_controller, $change_params ) = $args;
		}

		$slug = array();
		if( $this->controller ){
			$slug[] = $this->controller;
		}
		if( $append_controller ){
			$slug[] = $append_controller;
		}

		$params = array_merge( $this->params, $change_params );
		$params = $this->params;
		foreach( $change_params as $k => $v ){
			if( (substr($k, -1) == '+') OR (substr($k, -1) == '-') ){
				$operation = substr($k, -1);
				$k = substr($k, 0, -1);
				if( isset($params[$k]) ){
					if( ! is_array($params[$k]) ){
						$params[$k] = array( $params[$k] );
					}
				}
				else {
					$params[$k] = array();
				}
				if( $operation == '+' ){
					$params[$k][] = $v;
				}
				else {
					$params[$k] = HC_Lib::remove_from_array( $params[$k], $v );
				}
			}
			else {
				$params[$k] = $v;
			}
		}

		foreach( $params as $k => $v ){
			if( is_array($v) ){
				if( ! $v ){
					continue;
				}
				$v = join('.', $v);
			}
			if( $v !== NULL ){
				$slug[] = $k;
				$slug[] = $v;
			}
		}

		$CI =& ci_get_instance();
		$return = $CI->config->site_url( $slug );

		return $return;
	}

	public function __toString()
	{
		return $this->url();
    }
}

class HC_lib {
	static function redirect( $uri = '', $method = 'location', $http_response_code = 302 )
	{
		if( ! ( (! is_array($uri)) && preg_match('#^https?://#i', $uri) ) ){
			$uri = HC_Lib::link($uri)->url();
		}

	/* this is a hack to ensure that post controller and post system hooks are triggered */
		hc_ci_before_exit();

		switch($method){
			case 'refresh'	: header("Refresh:0;url=".$uri);
				break;
			default			: header("Location: ".$uri, TRUE, $http_response_code);
				break;
		}
		return;
	}

	static function build_csv( $array, $separator = ',' )
	{
		$processed = array();
		reset( $array );
		foreach( $array as $a ){
			if( strpos($a, '"') !== false ){
				$a = str_replace( '"', '""', $a );
				}
			if( strpos($a, $separator) !== false ){
				$a = '"' . $a . '"';
				}
			$processed[] = $a;
			}

		$return = join( $separator, $processed );
		return $return;
	}

	static function array_skip_after( $src, $after, $include = TRUE )
	{
		$return = array();
		foreach( $src as $k ){
			if( $k == $after ){
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
		foreach( $src as $k ){
			if( $k == $after ){
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
		foreach( $keys as $k ){
			if( array_key_exists($k, $src) ){
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
		if( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') ){
			$return = TRUE;
		}
		return $return;
	}

	static function app()
	{
		$return = '';
		if( isset($GLOBALS['NTS_APP'])){
			$return = $GLOBALS['NTS_APP'];
		}
		return $return;
	}

	static function nts_config()
	{
		$return = array();
		$app = HC_Lib::app();
		if( isset($GLOBALS['NTS_CONFIG'][$app]) ){
			$return = $GLOBALS['NTS_CONFIG'][$app];
		}
		return $return;
	}

	static function ri()
	{
		$return = '';
		$nts_config = HC_Lib::nts_config();
		if( isset($nts_config['REMOTE_INTEGRATION']) ){
			$return = $nts_config['REMOTE_INTEGRATION'];
		}
		return $return;
	}

	static function is_full_url( $url )
	{
		$full = FALSE;
		if( is_array($url)){
			return $full;
		}

		$prfx = array('http://', 'https://', '//', '/');
		reset( $prfx );
		foreach( $prfx as $prf ){
			if( substr($url, 0, strlen($prf)) == $prf ){
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

	static function link( $start = '', $params = array() )
	{
		$return = new HC_Link( $start, $params );
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
		foreach( $orderArray as $o ){
			if( in_array($o, $array) ){
				$return[] = $o;
			}
		}
		reset( $array );
		foreach( $array as $a ){
			if( ! in_array($a, $return) )
				$return[] = $a;
		}
		return $return;
	}

	static function ksort_array_by_array( $array, $orderArray )
	{
		$return = array();
		reset( $orderArray );
		foreach( $orderArray as $o ){
			if( array_key_exists($o, $array) ){
				$return[$o] = $array[$o];
			}
		}
		reset( $array );
		foreach( $array as $k => $k ){
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
   
		if( $i > count($out) ){
			$i = $i % count($out);
		}

		if( $i > 0 ){
			$return = $out[$i - 1];
		}
		else {
			$return = '#000';
		}
		return $return;
	}

	static function pick_random( $array, $many = 1 )
	{
		if( $many > 1 ){
			$return = array();
			$ids = array_rand($array, $many );
			foreach( $ids as $id )
				$return[] = $array[$id];
		}
		else {
			$id = array_rand($array);
			$return = $array[$id];
		}
		return $return;
	}

	static function list_files( $dirName, $extension = '' )
	{
		if( ! is_array($dirName) )
			$dirName = array( $dirName );

		$files = array();
		foreach( $dirName as $thisDirName ){
			if ( file_exists($thisDirName) && ($handle = opendir($thisDirName)) ){
				while ( false !== ($f = readdir($handle)) ){
					if( substr($f, 0, 1) == '.' )
						continue;

					if( is_file( $thisDirName . '/' . $f ) ){
						if( (! $extension ) || ( substr($f, - strlen($extension)) == $extension ) )
							$files[] = $f;
					}
				}
				closedir($handle);
			}
		}
		sort( $files );
		return $files;
	}

	static function list_subfolders( $dirName )
	{
		if( ! is_array($dirName) )
			$dirName = array( $dirName );

		$return = array();
		reset( $dirName );
		foreach( $dirName as $thisDirName ){
			if ( file_exists($thisDirName) && ($handle = opendir($thisDirName)) ){
				while ( false !== ($f = readdir($handle)) ){
					if( substr($f, 0, 1) == '.' )
						continue;
					if( is_dir( $thisDirName . '/' . $f ) ){
						if( ! in_array($f, $return) )
							$return[] = $f;
					}
				}
				closedir($handle);
			}
		}

		sort( $return );
		return $return;
	}

	static function format_price( $amount, $calculated_price = '' )
	{
		$app_conf = HC_App::app_conf();

		$before_sign = $app_conf->get( 'currency_sign_before' );
		$currency_format = $app_conf->get( 'currency_format' );
		list( $dec_point, $thousand_sep ) = explode( '||', $currency_format );
		$after_sign = $app_conf->get( 'currency_sign_after' );

		$amount = number_format( $amount, 2, $dec_point, $thousand_sep );
		$return = $before_sign . $amount . $after_sign;

		if( strlen($calculated_price) && ($amount != $calculated_price) ){
			$calc_format = $before_sign . number_format( $calculated_price, 2, $dec_point, $thousand_sep ) . $after_sign;
			$return = $return . ' <span style="text-decoration: line-through;">' . $calc_format . '</span>';
		}
		return $return;
	}

	static function insert_after( $what, $array, $after )
	{
		$inserted = FALSE;
		$return = array();
		foreach( $array as $e ){
			$return[] = $e;
			if( $e == $after ){
				$return[] = $what;
				$inserted = TRUE;
			}
		}
		if( ! $inserted ){
			$return[] = $what;
		}
		return $return;
	}

	static function remove_from_array( $array, $what, $all = TRUE )
	{
		$return = $array;
		for( $ii = count($return) - 1; $ii >= 0; $ii-- ){
			if( $return[$ii] == $what ){
				array_splice( $return, $ii, 1 );
				if( ! $all ){
					break;
				}
			}
		}
		return $return;
	}

	static function debug( $text )
	{
		$fname = FCPATH . '/debug.txt';
		$text = $text . "\n";
		HC_Lib::file_set_contents( $fname, $text, TRUE );
	}

	static function file_set_contents( $fileName, $content, $append = FALSE )
	{
		$length = strlen( $content );
		$return = 1;

		if( $append ){
			if(! $fh = fopen($fileName, 'a') ){
				echo "can't open file <B>$fileName</B> for appending.";
				exit;
			}
		}
		else {
			if(! $fh = fopen($fileName, 'w') ){
				echo "can't open file <B>$fileName</B> for wrinting.";
				exit;
			}
			rewind( $fh );
		}
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

	static function replace_in_array( $array, $from, $to ){
		$return = array();
		foreach( $array as $item ){
			if( $item == $from )
				$return[] = $to;
			else
				$return[] = $item;
		}
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
