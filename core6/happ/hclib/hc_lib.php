<?php
include_once( dirname(__FILE__) . '/hc_object_cache.php' );
class HC_App
{
	static function app_conf()
	{
		$CI =& ci_get_instance();
		return $CI->app_conf; 
	}

	static function presenter( $model )
	{
		$class = ucfirst($model) . '_Presenter';
		$return = new $class;

		$args = func_get_args();
		array_shift( $args );
		if( $args )
		{
			call_user_func_array( array($return, "set_model"), $args );
		}
		return $return;
	}

	static function model( $model )
	{
		$class = ucfirst($model) . '_Model';
		$return = new $class;
		return $return;
	}

	static function icon_for( $class )
	{
		$return = '';
		$conf = array(
			'date'		=> 'calendar',
			'time'		=> 'clock-o',
			'user'		=> 'user',
			'location'	=> 'home',
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

		if( count($args) == 2 )
		{
			$method = $args[0];
			$change_params = $args[1];
		}
		elseif( count($args) == 1 )
		{
			if( is_array($args[0]) )
			{
				$method = '';
				$change_params = $args[0];
			}
			else
			{
				$method = $args[0];
				$change_params = array();
			}
		}

		$return = array();

		$params = $this->params();
		foreach( $change_params as $k => $v )
		{
			$params[ $k ] = $v;
		}

		$controller = array();
		$controller[] = $this->controller();
		if( $method )
		{
			$controller[] = $method;
		}
		$controller = join( '/', $controller );

		$return[] = $controller;
		if( $params )
		{
			foreach( $params as $k => $v )
			{
				$return[] = $k;
				$return[] = $v;
			}
		}
		return $return;
	}

	function url()
	{
		$args = func_get_args();
		$slug = call_user_func_array( array($this, 'slug'), $args );
		$return = ci_site_url( $slug );
		return $return;
	}

	public function __toString()
	{
		return $this->url();
    }
}

class Hc_lib {
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
