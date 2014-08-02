<?php
if( ! function_exists('_print_r') )
{
	function _print_r($thing)
	{
		echo '<pre>';
		print_r( $thing );
		echo '</pre>';
	}
}

if( ! class_exists('hcWpPost') )
{
class hcWpPost {
	var $post_title;
	var $post_content;
	var $post_status;
	var $post_author;    /* author user id (optional) */
	var $post_name;      /* slug (optional) */
	var $post_type;      /* 'page' or 'post' (optional, defaults to 'post') */
	var $comment_status; /* open or closed for commenting (optional) */
	}
}

if( ! class_exists('hcWpBase2') )
{
class hcWpBase2
{
	var $app = '';
	var $slug = '';
	var $db_prefix = '';
	var $types = array();
	var $dir = '';
	var $_admin_styles = array();
	var $_admin_scripts = array();
	var $pages = array();
	var $page_param = '';
	var $own_db = FALSE;
	var $require_shortcode = FALSE;
//	var $query_prefix = '?/'; // for CI based apps
	var $query_prefix = ''; // for NTS based apps

	public function __construct( 
		$app, 
		$dir, 
		$types = array(),
		$own_db = FALSE,
		$slug = '',
		$db_prefix = ''
		)
	{
		$GLOBALS['NTS_IS_PLUGIN'] = 'wordpress';

		$this->app = $app;
		$GLOBALS['NTS_APP'] = $app;

		$this->slug = $slug ? $slug : $this->app;
		$this->db_prefix = $db_prefix ? $db_prefix : $this->slug;
		$this->dir = $dir;
		$this->types = array();
		$this->page_param = 'page_id';
		$this->own_db = $own_db;

		reset( $types );
		foreach( $types as $t )
		{
			$full_type = $this->app . '-' . $t;
			$this->types[ $t ] = $full_type;
		}

		$this->_admin_styles = array();
		$this->_admin_scripts = array();

		$file = $this->dir . '/' . $app . '.php';
		if( file_exists($file) )
		{
			register_activation_hook( $file, array($this, '_install') );
		}

		add_action(	'init',						array($this, '_init') );
		if( $this->is_me_admin() )
		{
			add_action( 'admin_enqueue_scripts',	array($this, 'admin_scripts') );
			add_action( 'admin_head', 				array($this, 'admin_head') );
		}

		if( $this->is_me_front() )
		{
			add_action( 'wp_enqueue_scripts',	array($this, 'admin_scripts') );
			add_action( 'wp_head', 				array($this, 'admin_head') );
		}

		add_action( 'save_post',				array($this, 'save_meta'));
		add_action( 'wp_logout',				array($this, 'logout'));
	}

	private function _init_db()
	{
		global $table_prefix;
		$mypref = $table_prefix . $this->db_prefix . '_';

		$GLOBALS['NTS_CONFIG'][$this->app]['DB_HOST'] = DB_HOST;
		$GLOBALS['NTS_CONFIG'][$this->app]['DB_USER'] = DB_USER;
		$GLOBALS['NTS_CONFIG'][$this->app]['DB_PASS'] = DB_PASSWORD;
		$GLOBALS['NTS_CONFIG'][$this->app]['DB_NAME'] = DB_NAME;
		$GLOBALS['NTS_CONFIG'][$this->app]['DB_TABLES_PREFIX'] = $mypref;
	}

	function register_admin_style( $url )
	{
		$id = $this->app . '-style-admin-' . (count($this->_admin_styles) + 1);
		$this->_admin_styles[] = array( $id, $url );
	}

	function register_admin_script( $url, $id = '' )
	{
		if( ! $id )
		{
			$id = $this->app . '-script-admin-' . ( count($this->_admin_scripts) + 1);
		}

		$skip = FALSE;
		/* check jquery */
		$check_url = is_array($url) ? $url[0] : $url;
		if(
			preg_match('/\/jquery\-\d/', $check_url)
		)
		{
			$id = 'jquery';
			$url = '';
		}

		if( ! $skip )
			$this->_admin_scripts[] = array( $id, $url );
	}

	public function admin_init()
	{
		if( $this->is_me_admin() )
		{
			$GLOBALS['NTS_APP'] = $this->app;

			$current_user = wp_get_current_user();
			$GLOBALS['NTS_CONFIG'][$this->app]['FORCE_LOGIN_ID'] = $current_user->ID;
			$GLOBALS['NTS_CONFIG'][$this->app]['FORCE_LOGIN_NAME'] = $current_user->user_email;

			$GLOBALS['NTS_CONFIG'][$this->app]['BASE_URL'] = get_admin_url();
			$GLOBALS['NTS_CONFIG'][$this->app]['INDEX_PAGE'] = 'admin.php?page=' . $this->slug . '&';
			$GLOBALS['NTS_CONFIG'][$this->app]['ADMIN_PANEL'] = 1;
		}
	}

	public function front_init()
	{
		global $post;
		$return = FALSE;

		if( $this->is_me_front() )
		{
			$GLOBALS['NTS_APP'] = $this->app;

			add_action( 'wp_enqueue_scripts',	array($this, 'admin_scripts') );
			add_action( 'wp_head', 				array($this, 'admin_head') );

			$current_user = wp_get_current_user();
			$GLOBALS['NTS_CONFIG'][$this->app]['FORCE_LOGIN_ID'] = $current_user->ID;
			$GLOBALS['NTS_CONFIG'][$this->app]['FORCE_LOGIN_NAME'] = $current_user->user_email;

			$url = parse_url( get_permalink($post) );
			$base_url = $url['path'];
			$index_page = (isset($url['query']) && $url['query']) ? '?' . $url['query'] . '&' : $this->query_prefix;

			$GLOBALS['NTS_CONFIG'][$this->app]['BASE_URL'] = $base_url;
			$GLOBALS['NTS_CONFIG'][$this->app]['INDEX_PAGE'] = $index_page;
			$return = TRUE;
		}
		return $return;
	}

	public function is_me_front()
	{
		global $post;
		$return = FALSE;

		if( is_admin() )
			return $return;

		if( ! (isset($post) && $post) )
			return $return;

		$pattern = '\[' . $this->slug . '\]';
		if(
			preg_match('/'. $pattern .'/s', $post->post_content, $matches)
			)
		{
			$return = TRUE;
		}
		else
		{
			// might be shortcode with params
			$pattern = '\[' . $this->slug . '\s*(.+)\]';
			if(
				preg_match('/'. $pattern .'/s', $post->post_content, $matches)
				)
			{
//				$atts = shortcode_parse_atts( $matches[1] );
				$return = TRUE;
			}
		}
		return $return;
	}

	public function is_me_front_()
	{
		global $post;
		$return = FALSE;

		if( is_admin() )
			return $return;
		$pattern = get_shortcode_regex();
		if(
			$post && 
			preg_match_all('/'. $pattern .'/s', $post->post_content, $matches) &&
			array_key_exists(2, $matches) &&
			in_array($this->slug, $matches[2])
			)
		{
			$return = TRUE;
		}
		return $return;
	}

	function is_me_admin()
	{
		global $post;
		if(
			( isset($post) && in_array($post->post_type, $this->types) )
			OR
			( isset($_REQUEST['post_type']) && in_array($_REQUEST['post_type'], $this->types) )
			)
		{
			$return = TRUE;
		}
		else
		{
			$page = isset($_GET['page']) ? $_GET['page'] : '';
			if( isset($_REQUEST['page']) )
			{
				$page = $_REQUEST['page'];
			}
			if( $page && ($page == $this->slug) )
			{
				$return = TRUE;
			}
			else
			{
				$return = FALSE;
			}
		}
		return $return;
	}

	function admin_scripts()
	{
		reset( $this->_admin_styles );
		foreach( $this->_admin_styles as $sa )
		{
			if( is_array($sa[1]) )
			{
				// processed later in head
			}
			else
			{
				wp_enqueue_style( $sa[0], $sa[1] );
			}
		}

		reset( $this->_admin_scripts );
		foreach( $this->_admin_scripts as $sa )
		{
			if( $sa[1] )
			{
				if( is_array($sa[1]) )
				{
					// processed later in head
				}
				else
				{
					wp_enqueue_script( $sa[0], $sa[1] );
				}
			}
			else
			{
				wp_enqueue_script( $sa[0] );
			}
		}
	}

	function admin_head()
	{
	/* this is here because damn WP can't handle confitional scripts for damn IE */
		$return = array();

		reset( $this->_admin_styles );
		foreach( $this->_admin_styles as $sa )
		{
			if( $sa[1] && is_array($sa[1]) )
			{
				$return[] = 
					'<!--[if ' . $sa[1][1] . ']>' .
					"\n" . 
					'<link rel="stylesheet" id="' . $sa[0] . '" href="' . $sa[1][0] . ' type="text/css" media="all" />' .
					"\n" . 
					'<![endif]-->' .
					"\n";
			}
		}

		reset( $this->_admin_scripts );
		foreach( $this->_admin_scripts as $sa )
		{
			if( $sa[1] && is_array($sa[1]) )
			{
				$return[] = 
					'<!--[if ' . $sa[1][1] . ']>' .
					"\n" . 
					'<script src="' . $sa[1][0] . '"></script>' . 
					"\n" . 
					'<![endif]-->' .
					"\n";
			}
		}

		$return = join( "\n", $return );
		echo $return;
	}

// normally overwritten by child classes
	function _install()
	{
	// own database
		if( $this->own_db )
		{
			$this->_init_db();
		}
	}

	function _init()
	{
	// custom types and taxonimies
		$file = $this->dir . '/conf/cpt.php';
		if( file_exists($file) )
		{
			require( $file );
		}

	// own database
		if( $this->own_db )
		{
			$this->_init_db();
		}

	// load shortcode
		global $wpdb;

		$shortcode = '[' . $this->slug . ']';
		$this->pages = array();
		$pages = $wpdb->get_results( 
			"
			SELECT 
				ID 
			FROM $wpdb->posts 
			WHERE 
				(post_type = 'post' OR post_type = 'page') AND 
				(
				post_content LIKE '%" . $shortcode . "%'
				)
			"
			);
		foreach( $pages as $p )
		{
			$this->pages[] = $p->ID;
		}

		if( $this->pages )
		{
			$web_page = get_permalink($this->pages[0]);
			if( strlen($this->query_prefix) )
			{
				$url = parse_url( $web_page );
				if( isset($url['query']) && $url['query'] )
				{
					$web_page .= '&';
				}
				else
				{
					$web_page .= $this->query_prefix;
				}
			}
		}
		else
		{
			$web_page = get_bloginfo('wpurl');
		}

		$GLOBALS['NTS_CONFIG'][$this->app]['FRONTEND_WEBPAGE'] = $web_page;

	// other config
		$GLOBALS['NTS_CONFIG'][$this->app]['REMOTE_INTEGRATION'] = 'wordpress';
		$session_name = 'ntssess_' . $this->app;
		$GLOBALS['NTS_CONFIG'][$this->app]['SESSION_NAME'] = $session_name;

		session_name( $session_name );
		@session_start();
		ob_start();
	}

	function get_options( $defaults = array() )
	{
		$options = get_option($this->app);
		$return = array_merge( $defaults, $options );
		return $return;
	}

	function get_option( $key )
	{
		$options = $this->get_options();
		$return = isset($options[$key]) ? $options[$key] : NULL;
		return $return;
	}

	function render( $view, $vars )
	{
		$file = $this->dir . '/views/' . $view . '.php';
		if( ! file_exists($file) )
		{
			$content = 'File "' . $view . '" does not exist<br>';
		}
		else
		{
			extract( $vars );
			ob_start();
			require( $file );
			$content = ob_get_contents();
			ob_end_clean();
		}
		return $content;
	}

	function save_option( $key, $value )
	{
		$options = $this->get_options();
		$options[$key] = $value;
		update_option($this->app, $options);
	}

	function check_post( $post_id )
	{
		global $post;
	/* Check if the current user has permission to edit the post. */
		if( $post )
		{
			$post_type = get_post_type_object( $post->post_type );
			if ( ! current_user_can($post_type->cap->edit_post, $post_id) )
				return FALSE;
		}
		return TRUE;
	}

	function save_meta( $post_id )
	{
		// normally overwritten by child classes
		return $post_id;
	}

	function make_input( $start, $props )
	{
		$display = array();
		$display[] = $start;

		if( ! isset($props['id']) )
		{
			$id = $props['name'];
			$id = str_replace( '[', '_', $id );
			$id = str_replace( ']', '', $id );
			$props['id'] = $id;
		}

		reset( $props );
		foreach( $props as $k => $v )
		{
			$display[] = $k . '="' . $v . '"';			
		}
		$return = '<' . join( ' ', $display ) . '>';
		return $return;
	}

	public function logout()
	{
		if( isset($_SESSION['NTS_SESSION_REF']) )
		{
			unset( $_SESSION['NTS_SESSION_REF'] );
		}
	}
}
}
?>