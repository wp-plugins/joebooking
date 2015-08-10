<?php
/**
 * @package JoeBooking
 * @author HitCode
 */
/*
Plugin Name: JoeBooking
Plugin URI: http://www.joebooking.com/
Description: Appointment scheduling plugin designed specifically for service professionals like massage therapists, consultants, tutors, instructors, photographers, stylists, dog groomers and others who need to book their time with clients online. 
Author: HitCode
Version: 6.3.9
Author URI: http://www.hitcode.com/
*/

include_once( dirname(__FILE__) . '/core6/lib/ntsWpBase.php' );

register_uninstall_hook( __FILE__, array('joeBooking', 'uninstall') );

class joeBooking extends ntsWpBase2
{
	public function __construct()
	{
		parent::__construct( 
			strtolower(get_class()),
			__FILE__
			);
	}

	static function uninstall( $prefix = 'ha_v6', $watch_other = array() )
	{
		$prefix = 'ha_v6';
		$watch_other = array('joebooking-pro.php', 'joebooking-salon-pro.php');
		hcWpBase4::uninstall( $prefix, $watch_other );
	}
}

$jbk = new joeBooking();
?>