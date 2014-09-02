<?php
error_reporting( E_ALL );
ini_set( 'display_errors', 'On' );

$checker_file = dirname(__FILE__) . '/wp-plugin-update-checker/plugin-update-checker.php';
if( (! class_exists('hcWpPremiumPlugin')) && file_exists($checker_file) )
{
include_once( $checker_file );

class hcWpPremiumPlugin
{
	var $system_type = 'nts'; // or 'ci'
	var $slug = '';
	var $app = '';

	function __construct( 
		$app,			// app
		$product,		// hitcode product name
		$slug,			// slug in wp admin
		$full_path,		// full path of the original plugin file
		$system_type	// 'nts' or 'ci'
		)
	{
		$this->system_type = $system_type;
		$this->app = $app;
		$this->slug = $slug;

		if( defined('NTS_DEVELOPMENT') )
		{
			$check_url = 'http://localhost/hitcode/customers/update.php?';
			$check_period = (1 / 3600); // every second
//			$check_period = 12;
		}
		else
		{
			$check_url = 'http://localhost/hitcode/customers/update.php?';
			$check_period = 12;
		}

		$check_url .= '&slug=' . $product;
		$MyUpdateChecker = new PluginUpdateChecker_1_5 (
			$check_url,
			$full_path,
			$product,
			$check_period
			);
	// add more links in plugin list
//		$filter_name = 'network_admin_plugin_action_links_' . plugin_basename($full_path);
		$filter_name = 'plugin_action_links_' . plugin_basename($full_path);
//		add_filter( $filter_name, array($this, 'license_link') );

		$current_code = $this->current_code();
//		echo "cc = $current_code<br>";
		if( ! $current_code )
		{
			add_action( 'after_plugin_row_' . plugin_basename($full_path), array($this, 'license_details'), 10, 3 );
		}
	}

	function license_details( $plugin_file, $plugin_data, $status )
	{
		$url = $this->get_license_link();
		$license_link = '<a href="' . $url . '">' . __( 'Enter License Code' ) . '</a>';

		$return = array();
		$return[] = '<tr>';
		$return[] = '<td>&nbsp;</td>';
		$return[] = '<td style="padding: 0 0;" colspan="2">';

		$return[] = '<div class="update-nag" style="position: relative; display: block; margin: 0 0; width: auto;">';
		$return[] = $license_link;
		$return[] = '<br>';
		$return[] = '<small>';
		$return[] = 'License is not set yet. Please enter your license code to enable automatic updates.';
		$return[] = '</small>';
		$return[] = '</div>';

		$return[] = '</td>';
		$return[] = '</tr>';
		$return = join( '', $return );
		echo $return;
	}

	public function current_code()
	{
		global $wpdb;
		$db_prefix = $GLOBALS['NTS_CONFIG'][$this->app]['DB_TABLES_PREFIX'];
		$return = NULL;

		switch( $this->system_type )
		{
			case 'ci':
				$mytable = $db_prefix . 'conf';
				$sql = "SELECT value FROM $mytable WHERE name='license_code'";
				$return = $wpdb->get_var( $sql );
				break;

			case 'nts':
				$mytable = $db_prefix . 'conf';
				$sql = "SELECT value FROM $mytable WHERE name='licenseCode'";
				$return = $wpdb->get_var( $sql );
				break;
		}
		return $return;
	}

	public function get_license_link()
	{
		switch( $this->system_type )
		{
			case 'ci':
				$license_url = $this->slug . '&/license/admin';
				break;

			case 'nts':
				$license_url = $this->slug . '&nts-panel=admin/conf/upgrade';
				break;
		}

		$return = add_query_arg( 
			array(
				'page' => $license_url,
				),
			admin_url('admin.php')
			);
		return $return;
	}

	public function license_link( $links )
	{
		$url = $this->get_license_link();
		$license_link = '<a href="' . $url . '">' . __( 'Enter License Code' ) . '</a>';
		array_unshift( $links, $license_link );
		return $links;
	}
}

}