<?php
global $NTS_CORE_DIRS;

$what = isset( $_GET['nts-what'] ) ? $_GET['nts-what'] : 'css';
$finalFiles = array();

switch( $what ){
	case 'font':
		$contentType = 'application/octet-stream';
		$file = $_GET['nts-file'];
		$thisFolder = NTS_APP_DIR . '/assets/font';
		array_unshift( $finalFiles, $thisFolder . '/' . $file );
		break;

	case 'css':
		$contentType = 'text/css';
		if( isset($_GET['nts-files']) )
		{
			$files = $_GET['nts-files'];
			$files = trim( $files );
			$files = explode( '|', $files );
		}
		else
		{
			require( dirname(__FILE__) . '/../assets/happ_files.php' );
			require( dirname(__FILE__) . '/../assets/files.php' );
			$files = $css_files;
		}
		foreach( $files as $f )
		{
			if( is_array($f) )
				$f = $f[0];
			$f = trim( $f );
			if( ! $f )
				continue;

			if( substr($f, 0, strlen('core6')) == 'core6' )
				$f = substr($f, strlen('core6'));
			else
				$f = 'assets/css/' . $f;

			$fullPath = NTS_LIB_DIR . '/' . $f;
			if( file_exists($fullPath) )
				$finalFiles[] = $fullPath;
		}
		break;

	case 'js':
		$contentType = 'text/javascript';

		if( isset($_GET['nts-files']) )
		{
			$files = $_GET['nts-files'];
			$files = trim( $files );
			$files = explode( '|', $files );
		}
		else
		{
			require( dirname(__FILE__) . '/../assets/happ_files.php' );
			require( dirname(__FILE__) . '/../assets/files.php' );
			$files = $js_files;
		}

		foreach( $files as $f )
		{
			if( is_array($f) )
				$f = $f[0];

			$f = trim( $f );
			if( ! $f )
				continue;

			if( substr($f, 0, strlen('core6')) == 'core6' )
				$f = substr($f, strlen('core6'));
			else
				$f = 'assets/js/' . $f;

			$fullPath = NTS_LIB_DIR . '/' . $f;
			if( file_exists($fullPath) )
				$finalFiles[] = $fullPath;
		}
		break;
	}

$finalFiles = array_unique( $finalFiles );
reset( $finalFiles );

header("Content-type: $contentType");

foreach( $finalFiles as $f ){
	if( file_exists($f) ){
		readfile( $f );
		}
	}
exit;
?>