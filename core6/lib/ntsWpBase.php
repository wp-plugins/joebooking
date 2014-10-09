<?php
if( file_exists(dirname(__FILE__) . '/../../db.php') )
{
	$nts_no_db = TRUE;
	include_once( dirname(__FILE__) . '/../../db.php' );
}

if( defined('NTS_DEVELOPMENT') )
	$happ_path = NTS_DEVELOPMENT;
else
	$happ_path = dirname(__FILE__) . '/../happ';
include_once( $happ_path . '/hclib/hcWpBase.php' );
if( file_exists($happ_path . '/hclib/hcWpPremiumPlugin.php') )
{
	include_once( $happ_path . '/hclib/hcWpPremiumPlugin.php' );
}

if( ! class_exists('ntsWpBase2') )
{
class ntsWpBase2 extends hcWpBase4
{
	public function __construct( 
		$real_class,
		$real_class_file,
		$hc_product = ''
		)
	{
		$this->happ_path = defined('NTS_DEVELOPMENT') ? NTS_DEVELOPMENT : dirname(__FILE__) . '/../happ';;
		$this->happ_web_dir = defined('NTS_DEVELOPMENT') ? 'http://localhost' : plugins_url('core6', $real_class_file);
		$app = strtolower( $real_class );
		$slug = $app;
		$db_prefix = 'ha';

		$dev_app_file = dirname(__FILE__) . '/../../_app.php';
		if( file_exists($dev_app_file) )
		{
			require( $dev_app_file ); /* $app defined there */
		}
		else
		{
			$dev_app_file = dirname(__FILE__) . '/../version_' . $slug . '_salon_pro.php';
			if( file_exists($dev_app_file) )
			{
				$app = $slug . '_salon_pro';
			}
			else
			{
				$dev_app_file = dirname(__FILE__) . '/../version_' . $slug . '_pro.php';
				if( file_exists($dev_app_file) )
				{
					$app = $slug . '_pro';
				}
			}
		}

		parent::__construct( 
			$app,
			$real_class_file,
			$hc_product,
			'nts',
			array(),
			$slug,
			$db_prefix
			);

		$this->dir = dirname(__FILE__);
		$this->require_shortcode = TRUE;
		$this->query_prefix = '';

		require( dirname(__FILE__) . '/../assets/files.php' );
		reset( $css_files );
		foreach( $css_files as $f )
		{
			$file = is_array($f) ? $f[0] : $f;

			$full = FALSE;
			$prfx = array('http://', 'https://');
			reset( $prfx );
			foreach( $prfx as $prf )
			{
				if( substr($file, 0, strlen($prf)) == $prf )
				{
					$full = TRUE;
					break;
				}
			}

			if( $full )
			{
				$full_file = $file;
			}
			else
			{
				$full_file = (substr($file, 0, strlen('happ/')) == 'happ/') ? $this->happ_web_dir . '/' . $file : plugins_url($file, $real_class_file);
			}

			if( is_array($f) )
				$f[0] = $full_file;
			else
				$f = $full_file;

			$this->register_admin_style($f);
		}

		reset( $js_files );
		foreach( $js_files as $f )
		{
			$file = is_array($f) ? $f[0] : $f;

			$full = FALSE;
			$prfx = array('http://', 'https://');
			reset( $prfx );
			foreach( $prfx as $prf )
			{
				if( substr($file, 0, strlen($prf)) == $prf )
				{
					$full = TRUE;
					break;
				}
			}

			if( $full )
			{
				$full_file = $file;
			}
			else
			{
				$full_file = (substr($file, 0, strlen('happ/')) == 'happ/') ? $this->happ_web_dir . '/' . $file : plugins_url($file, $real_class_file);
			}

			if( is_array($f) )
				$f[0] = $full_file;
			else
				$f = $full_file;
			$this->register_admin_script($f);
		}

//		$shortcode = $slug . '6';
		$shortcode = $slug;
		add_shortcode( $shortcode, array($this, 'front_view'));
		add_action('wp', array($this, 'front_init') );
		add_action( 'admin_init', array($this, 'admin_init') );
		add_action( 'admin_menu', array($this, 'admin_menu') );
	}

	public function admin_menu()
	{
		parent::admin_menu();

		$default_title = $this->app;
		$default_title = str_replace( '_', ' ', $default_title );
		$default_title = ucwords( $default_title );
		$menu_title = get_site_option( $this->app . '_menu_title', $default_title );

		$page = add_menu_page(
			$menu_title,
			$menu_title,
			'read',
			$this->slug,
			array( $this, 'admin_view' ),
			'dashicons-calendar'
			);
	}

	public function front_init()
	{
		if( ! is_admin() )
		{
			if( parent::front_init() )
			{
			// action
				$file = $this->dir . '/../controller.php';
				require( $file );
				$GLOBALS['NTS_CONFIG'][$this->app]['ACTION_STARTED'] = 1;
			}
		}
	}

	public function admin_init()
	{
		if( $this->is_me_admin() )
		{
			parent::admin_init();

			$file = $this->dir . '/../controller.php';
			require( $file );

			if( $this->require_shortcode )
			{
				if( ! $this->pages )
				{
					$announceText = "You have not yet added the <strong>&#91;" . $this->slug . "&#93;</strong> shortcode to any of your posts or pages, the customer booking interface will not work!";
					ntsView::setAdminAnnounce( $announceText, 'alert' );
				}
			}
		}
	}

	public function front_view()
	{  
		if( 
			isset($GLOBALS['NTS_CONFIG'][$this->app]['ACTION_STARTED']) && 
			$GLOBALS['NTS_CONFIG'][$this->app]['ACTION_STARTED']
			)
		{
			$file = $this->dir . '/../view.php';
			require( $file );
		}
	}

	public function admin_view()
	{
		$file = $this->dir . '/../view.php';
		require( $file );
	}

	public function _install()
	{
		parent::_install();

		/* make the current user the admin in our app */
		$currentUser = wp_get_current_user();
		$currentUserId = $currentUser->ID;

		global $NTS_SETUP_ADMINS;
		$NTS_SETUP_ADMINS = array();

		$role = 'Administrator';
		$wp_user_search = new WP_User_Search( '', '', $role);
		$NTS_SETUP_ADMINS = $wp_user_search->get_results();

/* database */
		$file = $this->dir . '/../model/init.php';
		include_once( $file );

		$app_info = ntsLib::getAppInfo();
		if( ! $app_info['installed_version'] )
		{
			require( $this->dir . '/../setup/create-database.php' );
			require( $this->dir . '/../setup/populate.php' );

		/* reset some settings */
			$conf =& ntsConf::getInstance();
			$email_from = get_bloginfo('admin_email');
			$email_from_name = get_bloginfo('name');
			$conf->set( 'emailSentFrom', $email_from );
			$conf->set( 'emailSentFromName', $email_from_name );
		}
	}
}
}
?>