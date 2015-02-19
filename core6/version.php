<?php
require( dirname(__FILE__) . '/_version.php' );
global $NTS_APP, $NTS_SKIP_PANELS;

/* check which application is here */
$app = '';
if( $NTS_APP )
{
	$app = $NTS_APP;
}
else
{
	$dev_app_file = NTS_RUN_DIR . '/_app.php';
	if( file_exists($dev_app_file) )
	{
		require( $dev_app_file ); // $app defined there
	}
}

if( ! $app )
{
	$versions = array();
	// list files here that start with version_
	$list = ntsLib::listFiles( dirname(__FILE__), '.php' );
	reset( $list );
	foreach( $list as $l )
	{
		if( substr($l, 0, strlen('version_')) == 'version_' )
		{
			$this_app = substr($l, strlen('version_'), -strlen('.php'));
			$versions[] = $this_app;
		}
	}

	if( count($versions) == 1 )
	{
		$app = $versions[0];
	}
	else
	{
		$precedence = array( 'joebooking_salon_pro', 'hitappoint_salon_pro', 'joebooking_pro', 'hitappoint_pro', 'joebooking', 'hitappoint_pro' );
		foreach( $precedence as $app )
		{
			if( in_array($app, $versions) )
			{
				break;
			}
		}
	}
}

if( ! $app )
{
	echo "Can't find out which app is this!";
	exit;
}

$modify_version = 0;
$version_file = 'version_' . $app . '.php';
$disabled_features = array();
require( dirname(__FILE__) . '/' . $version_file );

global $NTS_APP_INFO;
$NTS_APP_INFO = array(
	'core_version'		=> $core_version,
	'app'				=> $app,
	'app_short'			=> $app_short,
	'modify_version'	=> $modify_version,
	'disabled_features'	=> $disabled_features,
	);

if( ! isset($GLOBALS['NTS_APP']))
{
	$GLOBALS['NTS_APP'] = $app;
}

if( isset($order_link) )
{
	$NTS_APP_INFO['order_link'] = $order_link;
}
if( isset($order_link_title) )
{
	$NTS_APP_INFO['order_link_title'] = $order_link_title;
}

/* skip panels */
global $NTS_SKIP_PANELS;
if( ! $NTS_SKIP_PANELS )
	$NTS_SKIP_PANELS = array();
reset( $skip );
foreach( $skip as $p )
{
	$NTS_SKIP_PANELS[] = $p;
}

if( file_exists(dirname(__FILE__) . '/../developer.php') )
{
	define( 'NTS_APP_WHITELABEL', TRUE );
	define( 'NTS_APP_DEVELOPER', TRUE );
}
else
{
	if( file_exists(dirname(__FILE__) . '/../whitelabel.php') )
		define( 'NTS_APP_WHITELABEL', true );
	else
		define( 'NTS_APP_WHITELABEL', false );
}

/* check which we are using */
if( ! defined('NTS_APP_LEVEL') )
{
	if( in_array('system/payment', $NTS_SKIP_PANELS) )
	{
		define( 'NTS_APP_LEVEL', 'lite' );
	}
	else
	{
		define( 'NTS_APP_LEVEL', 'pro' );
	}
}
?>