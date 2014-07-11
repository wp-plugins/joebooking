<?php
/**
 * @package JoeBooking
 * @author JoeBooking
 * @version 6.0.0
 */
/*
Plugin Name: JoeBooking
Plugin URI: http://www.joebooking.com/
Description: Appointment scheduling plugin designed specifically for service professionals like massage therapists, consultants, tutors, instructors, photographers, stylists, dog groomers and others who need to book their time with clients online. 
Author: JoeBooking
Version: 6.0.0
Author URI: http://www.joebooking.com/
*/

if( file_exists(dirname(__FILE__) . '/db.php') )
{
	$nts_no_db = TRUE;
	include_once( dirname(__FILE__) . '/db.php' );
}
include_once( dirname(__FILE__) . '/core6/lib/ntsWpBase.php' );

class joeBooking extends ntsWpBase
{
	public function __construct()
	{
		parent::__construct( 
			strtolower(get_class()),
			__FILE__
			);
	}

	public function admin_menu()
	{
		$page = add_menu_page(
			'JoeBooking',
			'JoeBooking',
			'read',
			$this->slug,
			array( $this, 'admin_view' ),
			'dashicons-calendar'
			);
	}
}

$jbk = new joeBooking();
?>