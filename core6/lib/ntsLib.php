<?php
global $NTS_VARS;
$NTS_VARS = array();

if ( ! function_exists('nts_log_message'))
{
	function nts_log_message( $level, $msg )
	{
	//	echo $level . ': ' . $msg;
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

if ( ! function_exists('_print_r2'))
{
	function _print_r2( $array )
	{
		$out = array();
		foreach( $array as $k => $v )
		{
			$out[] = $k . '=' . $v;
		}
		$out = join( '; ', $out );
		echo $out;
	}
}

class ntsHttpClient {
	var $error;
	var $timeout;

	function ntsHttpClient(){
		$this->setError( '' );
		$this->timeout = 10;
		}

	function get( $url2get ){
		$old = ini_set('default_socket_timeout', $this->timeout);

		ob_start();
		if(intval(get_cfg_var('allow_url_fopen')) && function_exists('readfile')){
//			if( ! ($return = @file($url2get)) ){
//				$this->setError( $php_errormsg );
//				}
			if( ! @readfile($url2get) ){
				$this->setError( $php_errormsg );
				}
			}
		elseif(function_exists('curl_init')) {
			$ch = curl_init( $url2get );
			curl_setopt( $ch, CURLOPT_HEADER, 0 );
			curl_exec( $ch );
			if( $error = curl_error($ch)){
				$this->setError( $error );
				}
			curl_close ($ch);
			}
		else {
			$error = "outside connections are not allowed";
			$this->setError( $error );
			}
		$return = ob_get_contents();
		ob_end_clean();

		ini_set('default_socket_timeout', $old);		
		return $return;
		}

	function isError(){
		$return = $this->error ? true : false;
		return $return;
		}

	function getError(){
		return $this->error;
		}

	function setError( $error ){
		$this->error = $error;
		}
	}

class ntsMoneyCalc
{
	var $result = 0;
	function __construct( $start = 0 )
	{
		$this->result = 0;
		if( $start )
		{
			$this->add( $start );
		}
	}

	function add( $amount )
	{
		$this->result += $amount;
	}

	function result()
	{
		$return = $this->result;
		$return = $return * 100;

		$test1 = (int) $return;
		$diff = abs($return - $test1);
		if( $diff < 0.01 )
		{
		}
		else
		{
			$return = ($return > 0) ? ceil( $return ) : floor( $return );
		}

		$return = (int) $return;
		$return = $return/100;
		return $return;
	}
}

class ntsLib {
	static function profiler( $label = '' )
	{
		static $last_time;
		if( ! $last_time )
		{
			$last_time = 0;
		}

		if( $label )
		{
			echo $label . ': ';
		}
		$time = ntsLib::getCurrentExecutionTime();
		if( $last_time )
		{
			ntsLib::printCurrentExecutionTime( $time - $last_time );
			echo ' [';
			ntsLib::printCurrentExecutionTime();
			echo ']';
		}
		else
		{
			ntsLib::printCurrentExecutionTime( $time );
		}
		$last_time = $time;
		echo ', ';
		if( class_exists('dbWrapper') )
		{
			$ntsdb =& dbWrapper::getInstance();
			echo $ntsdb->_queryCount . ' queries';
			echo ', ';
		}
		echo sprintf("%.2f", memory_get_usage()/(1024*1024)) . 'M';
		echo '<br>';
	}

	static function isAjax()
	{
		global $NTS_VIEW;
		$return = FALSE;
		if( isset($NTS_VIEW[NTS_PARAM_VIEW_MODE]) && ($NTS_VIEW[NTS_PARAM_VIEW_MODE] == 'ajax') )
		{
			$return = TRUE;
		}

		if( ! $return )
		{
			if( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') )
			{
				$return = TRUE;
			}
		}

//		echo "is ajax: ";
//		echo $return ? "TRUE" : "FALSE";
//		echo '<br>';
		return $return;
	}

	static function setCurrentUserId( $id )
	{
		$app = ntsLib::getAppProduct();
		$GLOBALS['NTS_CONFIG'][$app]['CURRENT_USER_ID'] = $id;
	}

	static function getFrontendWebpage()
	{
		$app = ntsLib::getAppProduct();
		if( isset($GLOBALS['NTS_CONFIG'][$app]['FRONTEND_WEBPAGE']) )
			$return = $GLOBALS['NTS_CONFIG'][$app]['FRONTEND_WEBPAGE'];
		else
			$return = ntsLib::pureUrl( ntsLib::currentPageUrl() );
		return $return;
	}

	static function getRootWebpage()
	{
		$app = ntsLib::getAppProduct();
		$base_url = $GLOBALS['NTS_CONFIG'][$app]['BASE_URL'];
		$index_page = $GLOBALS['NTS_CONFIG'][$app]['INDEX_PAGE'];

		$return = $base_url;
		if( strlen($index_page) )
			$return .= $index_page;

		return $return;
	}

	static function &getCurrentUser()
	{
		global $NTS_CURRENT_USER;
		if( ! $NTS_CURRENT_USER )
		{
			$id = ntsLib::getCurrentUserId();
			$NTS_CURRENT_USER = new ntsUser;
			$NTS_CURRENT_USER->setId( $id );
		}
		return $NTS_CURRENT_USER;
	}

	static function getCurrentUserId()
	{
		$return = 0;
		$app = ntsLib::getAppProduct();

		if( isset($GLOBALS['NTS_CONFIG'][$app]['FORCE_LOGIN_ID']) )
		{
			ntsLib::setCurrentUserId( $GLOBALS['NTS_CONFIG'][$app]['FORCE_LOGIN_ID'] );
		}

		if( isset($_SESSION['temp_customer_id']) )
		{
			ntsLib::setCurrentUserId( $_SESSION['temp_customer_id'] );
		}

		if( isset($GLOBALS['NTS_CONFIG'][$app]['CURRENT_USER_ID']) )
			$return = $GLOBALS['NTS_CONFIG'][$app]['CURRENT_USER_ID'];

		return $return;
	}

	static function remoteIntegration()
	{
		$return = '';
		$app = ntsLib::getAppProduct();
		if( isset($GLOBALS['NTS_CONFIG'][$app]['REMOTE_INTEGRATION']) )
		{
			$return = $GLOBALS['NTS_CONFIG'][$app]['REMOTE_INTEGRATION'];
		}
		return $return;
	}

	static function getAppVersion()
	{
		global $NTS_APP_INFO;
		$return = ntsLib::parseVersion($NTS_APP_INFO['core_version']) + $NTS_APP_INFO['modify_version'];
		$return = ntsLib::versionFromNumber( $return );
		return $return;
	}

	static function getAppProduct()
	{
		global $NTS_APP_INFO;
		$return = $NTS_APP_INFO['app'];
		return $return;
	}

	static function getAppInfo()
	{
		global $NTS_APP_INFO;
		$return = $NTS_APP_INFO;

		$conf =& ntsConf::getInstance();
		$return['current_version'] = $conf->get('currentVersion');
		$return['installed_version'] = $return['current_version'];
		if( ! $return['current_version'] )
		{
			$return['current_version'] = ntsLib::getAppVersion();
		}

		return $return;
	}

	static function is_really_writable($file)
	{
		// If we're on a Unix server with safe_mode off we call is_writable
		if (DIRECTORY_SEPARATOR == '/' AND @ini_get("safe_mode") == FALSE)
		{
			return is_writable($file);
		}

		// For windows servers and safe_mode "on" installations we'll actually
		// write a file then read it.  Bah...
		if (is_dir($file))
		{
			$file = rtrim($file, '/').'/'.md5(mt_rand(1,100).mt_rand(1,100));

			if (($fp = @fopen($file, 'ab')) === FALSE)
			{
				return FALSE;
			}

			fclose($fp);
			@chmod($file, 0777);
			@unlink($file);
			return TRUE;
		}
		elseif ( ! is_file($file) OR ($fp = @fopen($file, 'ab')) === FALSE)
		{
			return FALSE;
		}

		fclose($fp);
		return TRUE;
	}

	static function normalizePath( $path )
	{
		$return = str_replace( '\\', '/', $path );
		return $return;
	}

	static function debug( $file, $line, $more = '' )
	{
		static $last_time = 0;
		echo "<br>=====<br>";
		echo "$file: $line<br>";

		if( $more )
		{
			echo '<strong><i>' . $more . '</i></strong><br>';
		}

		echo "<strong>";
		$time = ntsLib::getCurrentExecutionTime();
		$delta = ($time - $last_time);
//		printf("%.2fs", $time );
		printf("%.2fs", $delta );
		echo '; ';
		$ntsdb =& dbWrapper::getInstance();
		echo $ntsdb->_queryCount . '';
		echo '; ';
		echo ntsLib::humanFilesize(memory_get_usage());
		echo "</strong>";
		$last_time = $time;
	}

	static function returnBytes( $size_str )
	{
		switch (substr ($size_str, -1))
		{
			case 'M': case 'm': return (int)$size_str * 1048576;
			case 'K': case 'k': return (int)$size_str * 1024;
			case 'G': case 'g': return (int)$size_str * 1073741824;
			default: return $size_str;
		}
	}

	static function humanFilesize($bytes, $decimals = 2){
		$sz = 'BKMGTP';
		$factor = floor((strlen($bytes) - 1) / 3);
		$return = sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
		return $return;
		}

	static function calcTax( $amount, $taxRate, $round = TRUE ) // taxRate in %
	{
		if( $round )
		{
			$return = ceil($taxRate * $amount) / 100;
		}
		else
		{
			$return = ($taxRate * $amount) / 100;
		}
		return $return;
	}

	static function removeTax( $amount, $taxRate ) // taxRate in %
	{
		$return = 100*($amount/(100 + $taxRate));
		$tax = ntsLib::calcTax( $return, $taxRate );
		$return = $amount - $tax;

		return $return;
	}

	static function checkLicenseUrl()
	{
		global $_NTS;
		$conf =& ntsConf::getInstance();

		$currentLicense = $conf->get('licenseCode');
		$installationId = $conf->get( 'installationId' );
		$installedVersion = $conf->get('currentVersion');
		$installedVersionNumber = ntsLib::parseVersion( $installedVersion );

		$appInfo = ntsLib::getAppInfo();
		$myProduct = $appInfo['app_short'];
		$app = $appInfo['app'];

		$myUrl = ntsLink::makeLinkFull( ntsLib::getFrontendWebpage() );
		// strip started http:// as apache seems to have troubles with it
		$myUrl = preg_replace( '/https?\:\/\//', '', $myUrl );

		$return = 
			$_NTS['CHECK_LICENSE_URL'] . 
			'?code=' . $currentLicense . 
			'&iid=' . $installationId . 
			'&ver=' . $installedVersion . 
			'&prd=' . urlencode($myProduct) . 
			'&url=' . urlencode($myUrl)
			;
		return $return;
	}

	static function upperCaseMe( $string ){
		$return = preg_replace( '/(\b)(\w)/e', "'\\1'.strtoupper('\\2')", $string );
		$return = str_replace( '-', ' ', $return );
		return $return;
		}

	static function findTitle( $title, $array ) {
		$return = '';
		for( $i = 0; $i < count($array); $i++ ){
			if( $title == $array[$i][0] ){
				$return = $array[$i][1];
				break;
				}
			}
		return $return;
		}

	static function findIndex( $title, $array ) {
		$return = -1;
		for( $i = 0; $i < count($array); $i++ ){
			if( $title == $array[$i][0] ){
				$return = $i;
				break;
				}
			}
		return $return;
		}

	static function reExistsInArray($re, $array){
		$return = false;
		reset( $array );
		foreach( $array as $a ){
			if( preg_match($re, $a) ){
				$return = true;
				break;
				}
			}
		return $return;
		}

	static function getVar( $key ){
		global $NTS_VARS;
		$return = null;

		if( array_key_exists($key, $NTS_VARS) ){
			$return = $NTS_VARS[$key];
			}
		else {
			echo "Var $key not found!<br>";
			}
		return $return;
		}

	static function hasVar( $key ){
		global $NTS_VARS;
		if( array_key_exists($key, $NTS_VARS) ){
			$return = TRUE;
			}
		else {
			$return = FALSE;
			}
		return $return;
		}

	static function setVar( $key, $value ){
		global $NTS_VARS;
		$NTS_VARS[$key] = $value;
		}

	static function setViewParams( $params, $file )
	{
		global $NTS_VARS;
		$key = dirname($file);
		$NTS_VARS[$key] = $params;
	}

	static function getViewParams( $file )
	{
		$return = array();
		global $NTS_VARS;
		$key = dirname($file);
		if( isset($NTS_VARS[$key]) )
			$return = $NTS_VARS[$key];
		return $return;
	}

	static function generateRand( $len = 12, $conf = array() ){
		$useLetters = isset($conf['letters']) ? $conf['letters'] : true;
		$useHex = isset($conf['hex']) ? $conf['hex'] : false;
		$useDigits = isset($conf['digits']) ? $conf['digits'] : true;
		$useCaps = isset($conf['caps']) ? $conf['caps'] : true;

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

	static function viewHighlighted( $what, $search = '' ){
		if( strlen($search) ){
			$re = '/' . $search . '/i';
			$return = preg_replace( $re, "<span class=\"ok\" style=\"font-weight: bold;\">$0</span>", $what );
			}
		else {
			$return = $what;
			}
		return $return;
		}

	static function log( $msg ){
		$outFile = realpath( NTS_APP_DIR . '/../halog.txt' );
		$date = date( "F j, Y, g:i a", time() );
		$fp = fopen( $outFile, 'a' );
		fwrite( $fp, $date . "\n" . $msg . "\n" );
		fclose($fp);
		}

	static function sortArrayByArray( $array, $orderArray ){
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

	static function findAllFiles( $path, $file ){
		$return = array();
		$rootFileFound = false;
		$rootPath = $path;
		do {
			$rootFile = ( $rootPath ) ? '/' . $rootPath . '/' . $file : '/' . $file;
			$rootFile = ntsLib::fileInCoreDirs( 'panels' . $rootFile );
			if( $rootFile ){
				array_unshift( $return, array($rootFile, $rootPath) );
				}
			$rootPath = ntsLib::getParentPath( $rootPath );
			}
		while( $rootPath );
		return $return;
		}

	static function findClosestFile( $path, $file, $skipMe = false ){
		$return = array();
		$rootFileFound = false;
		$rootPath = $skipMe ? ntsLib::getParentPath($path) : $path;
		if( ! is_array($file) ){
			$file = array( $file );
			}

		do {
			reset( $file );
			foreach( $file as $f ){
				$breakThis = false;
				$rootFile = ( $rootPath ) ? '/' . $rootPath . '/' . $f : '/' . $f;
				$rootFile = ntsLib::fileInCoreDirs( 'panels' . $rootFile );
				if( $rootFile ){
					$rootFileFound = true;
					$breakThis = true;
					}
				if( $breakThis ){
					break;
					}
				}
			if( $breakThis ){
				break;
				}
			$rootPath = ntsLib::getParentPath( $rootPath );
			}
		while( $rootPath );

		if( $rootFileFound ){
			$return = array( $rootFile, $rootPath );
			}
		return $return;
		}

	static function subfoldersInCoreDirs( $path ){
		global $NTS_CORE_DIRS;

		$return = array();
		reset( $NTS_CORE_DIRS );
		foreach( $NTS_CORE_DIRS as $d ){
			$finalPath = $d . '/' . $path;
			$thisSubfolders = ntsLib::listSubfolders( $finalPath );
			$return = array_merge( $return, $thisSubfolders );
			}
		$return = array_unique( $return );
		return $return;
		}

	static function fileInCoreDirs( $file ){
		global $NTS_CORE_DIRS, $NTS_FILE_LOOKUP_CACHE;

		if( ! isset($NTS_FILE_LOOKUP_CACHE[$file]) ){
			$return = '';
			reset( $NTS_CORE_DIRS );
			foreach( $NTS_CORE_DIRS as $d ){
				$finalFile = $d . '/' . $file;
				if( file_exists($finalFile) ){
					$return = $finalFile;
					break;
					}
				}
			$NTS_FILE_LOOKUP_CACHE[$file] = $return;
			}
		$return = $NTS_FILE_LOOKUP_CACHE[$file];
		return $return;
		}

	static function requireSubheaderFile( $subheaderFile ){
		global $req, $NTS_VIEW, $NTS_CURRENT_USER;
		static $SUBHEADER_FILES;
		if( ! isset($SUBHEADER_FILES[$subheaderFile]) ){
			$title = '';

			require( $subheaderFile );

			$SUBHEADER_FILES[$subheaderFile] = array( $title );
			}
		return $SUBHEADER_FILES[$subheaderFile];
		}

	static function parseVersion( $string )
	{
		$return = 0;
		if( strlen($string) && (substr_count($string, '.') == 2) )
		{
			list( $v1, $v2, $v3 ) = explode( '.', $string );
			$return = $v1 . $v2 . sprintf('%02d', $v3 );
		}
		return $return;
	}

	static function versionFromNumber( $number )
	{
		$return = substr($number, 0, 1) . '.' . substr($number, 1, 1) . '.' . ltrim(substr($number, 2, 2), '0');
		$v1 = substr($number, 0, 1);
		$v2 = substr($number, 1, 1);
		$v3 = substr($number, 2, 2);
		if( substr($v3, 0, 1) == '0' )
		{
			$v3 = substr($v3, 1, 1);
		}

		$return = join( '.', array($v1, $v2, $v3) );
		return $return;
	}

	static function parseVersionNumber( $string )
	{
		list( $v1, $v2, $v3 ) = explode( '.', $string );
		$return = $v1 . $v2 . sprintf('%02d', $v3 );
		return $return;
	}

	static function buildCsv( $array ){
		$conf =& ntsConf::getInstance();
		$csvDelimiter = $conf->get('csvDelimiter');

		$processedArray = array();
		reset( $array );
		foreach( $array as $a ){
			if( strpos($a, '"') !== false ){
				$a = str_replace( '"', '""', $a );
				}

			if( strpos($a, $csvDelimiter) !== false ){
				$a = '"' . $a . '"';
				}

			$a = trim(preg_replace('/\s+/', ' ', $a));
			$processedArray[] = $a;
			}

		$return = join( $csvDelimiter, $processedArray );
		return $return;
		}

	static function pickRandom( $array, $many = 1 ){
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

	static function currentPageUrl(){
		$pageURL = 'http';
		if( isset($_SERVER['HTTPS']) && ( $_SERVER['HTTPS'] == 'on' ) ){
			$pageURL .= 's';
			}
		$pageURL .= "://";
		if( isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80'){
			$pageURL .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'];
			}
		else {
			$pageURL .= $_SERVER['SERVER_NAME'];
			}

		if ( ! empty($_SERVER['REQUEST_URI']) )
			$pageURL .= $_SERVER['REQUEST_URI'];
		else
			$pageURL .= $_SERVER['SCRIPT_NAME'];
		return $pageURL;
		}

	static function pureUrl( $url ){
		preg_match( "/(.+)\?.*$/", $url, $matches );
		if( isset($matches[ 1 ]) ) 
			$url = $matches[ 1 ];
		return $url;
		}

	static function urlParamsPart( $url ){
		preg_match( "/(.+)(\?.*)$/", $url, $matches );
		if( isset($matches[ 2 ]) ) 
			$url = $matches[ 2 ];
		return $url;
		}

	static function webDirName( $fullWebPage ){
		preg_match( "/(.+)\/.*$/", $fullWebPage, $matches );
		if ( isset($matches[1]) )
			$webDir = $matches[1];
		else
			$webDir = '';
		return $webDir;
		}

	static function pushDownload( $localFileName, $pushName ){
		if( ob_get_contents() )
			ob_end_clean();
		$fileSize = filesize( $localFileName );

		header("Type: application/force-download");
		header("Content-Type: application/force-download");
		header("Content-Length: $fileSize");

		header("Content-Transfer-Encoding: binary");
		header("Content-Disposition: attachment; filename=\"$pushName\"");

		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Connection: close");

		readfile( $localFileName );
		exit;
		}

	static function pushDownloadContent( $content, $pushName, $contentType = 'application/force-download' ){
		if( ob_get_contents() )
			ob_end_clean();
		$fileSize = strlen( $content );

		header("Type: $contentType");
		header("Content-Type: $contentType");
		header("Content-Length: $fileSize");

		header("Content-Transfer-Encoding: binary");
		header("Content-Disposition: attachment; filename=\"$pushName\"");

		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Connection: close");

		readfile( $localFileName );
		exit;
		}

	static function startPushDownloadContent( $pushName ){
		if( ob_get_contents() )
			ob_end_clean();
//		$fileSize = strlen( $content );

		header("Type: application/force-download");
		header("Content-Type: application/force-download");
//		header("Content-Length: $fileSize");

		header("Content-Transfer-Encoding: binary");
		header("Content-Disposition: attachment; filename=\"$pushName\"");

		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Connection: close");
		}

	// A generic function to create and fetch static objects
	static function &singletonFunction( $class, $fileName = '' ) {
	    // Declare a static variable to hold the object instance
	    static $instances;

	    // If the instance is not there, create one
	    if( ! isset($instances[$class]) ){
	    	if( ! class_exists($class) ){
				echo "cannot create '$class' object!";
				return null;
	   			}
			$instances[$class] = new $class;
	    	}
	    return $instances[$class];
		}

	static function fileGetContents( $fileName ){
		$content = join( '', file($fileName) );
		return $content;
		}

	static function fileGetFirstLine( $fileName ){
		$file = file($fileName);
		$line = array_shift( $file );
		return $line;
		}

	static function fileSetContents( $fileName, $content ){
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

	static function numberCompare( $a, $b ){
		if( $a > $b )
			return 1;
		elseif( $a < $b )
			return -1;
		else
			return 0;
		}

	static function stringCompare( $a, $b ){
		return strcmp( strtolower($a), strtolower($b) );
		}

	static function getParentPath( $path ){
		if( substr($path, -1) == '/' )
			$path = substr($path, 0, -1);
		if( preg_match("/^(.+)\/.+$/", $path, $ma) )
			$parent = $ma[1];
		else
			$parent = '';
		return $parent;
		}

	static function listSubfolders( $dirName ){
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

	static function listFiles( $dirName, $extension = '', $allow_dots = FALSE ){
		if( ! is_array($dirName) )
			$dirName = array( $dirName );

		$files = array();
		foreach( $dirName as $thisDirName ){
	
			if ( file_exists($thisDirName) && ($handle = opendir($thisDirName)) ){
				while ( false !== ($f = readdir($handle)) ){
					if( ! $allow_dots )
					{
						if( substr($f, 0, 1) == '.' )
							continue;
					}

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

	static function utime() {
		$time = explode( ' ', microtime() );
		$usec = (double)$time[0];
		$sec = (double)$time[1];
		$return = $sec + $usec;
		return $return;
		}

	static function getCurrentExecutionTime(){
		global $NTS_EXECUTION_START;
		$return = ntsLib::utime() - $NTS_EXECUTION_START;
		return $return;
		}
	
	static function printCurrentExecutionTime( $time = 0 ){
		if( ! $time )
			$time = ntsLib::getCurrentExecutionTime();
		printf("%.2f sec", $time );
		}

	static function csvFile( $fileName, $sep = ';', $titles = true ){
		$return = array();
		$lines = file( $fileName );

		if( $titles ){
		/* first line with titles */
			$line = array_shift( $lines );
			$line = trim( $line );
			$line = strtolower( $line );
			$propNames = explode( ';', $line );
			$propCount = count( $propNames );
			}

		$count = 0;
		$created = 0;
		reset( $lines );
		foreach( $lines as $line ){
			$line = trim( $line );
			if( ! $line )
				continue;

			$count++;
			$rawValues = explode( ';', $line );
			if( $titles ){
				for( $i = 0; $i < $propCount; $i++ ){
					if( ! isset($rawValues[$i]) )
						$rawValues[$i] = '';
					$values[ $propNames[$i] ] = $rawValues[$i];
					}
				$return[] = $values;
				}
			else {
				$return[] = $rawValues;
				}
			}
		return $return;
		}

	static function sanitizeTitle($title) {
		$title = strip_tags($title);
		// Preserve escaped octets.
		$title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
		// Remove percent signs that are not part of an octet.
		$title = str_replace('%', '', $title);
		// Restore octets.
		$title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);

		$title = ntsLib::removeAccents($title);
		if (ntsLib::seemsUtf8($title)) {
			if (function_exists('mb_strtolower')) {
				$title = mb_strtolower($title, 'UTF-8');
				}
			$title = ntsLib::utf8UriEncode($title, 200);
			}

		$title = strtolower($title);

//		$title = preg_replace('/&.+?;/', '', $title); // kill entities
		$title = str_replace('.', '-', $title);
		$title = preg_replace('/[^%a-z0-9 _-]/', '', $title);
		$title = preg_replace('/\s+/', '-', $title);
		$title = preg_replace('|-+|', '-', $title);
		$title = trim($title, '-');

		return $title;
		}

	static function sanitizeFileName( $filename ) {
		$filename_raw = $filename;
		$special_chars = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}");
		$filename = str_replace($special_chars, '', $filename);
		$filename = preg_replace('/[\s-]+/', '-', $filename);
		$filename = trim($filename, '.-_');
		return $filename;
		}

	static function sanitizeSqlName( $filename ) {
		$filename = ntsLib::sanitizeFileName( $filename );
		$filename = str_replace('-', '_', $filename);
		return $filename;
		}

	static function removeAccents($string) {
		if ( !preg_match('/[\x80-\xff]/', $string) )
			return $string;

		if (ntsLib::seemsUtf8($string)) {
			$chars = array(
			// Decompositions for Latin-1 Supplement
			chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
			chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
			chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
			chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
			chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
			chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
			chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
			chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
			chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
			chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
			chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
			chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
			chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
			chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
			chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
			chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
			chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
			chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
			chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
			chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
			chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
			chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
			chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
			chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
			chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
			chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
			chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
			chr(195).chr(191) => 'y',
			// Decompositions for Latin Extended-A
			chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
			chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
			chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
			chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
			chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
			chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
			chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
			chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
			chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
			chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
			chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
			chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
			chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
			chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
			chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
			chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
			chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
			chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
			chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
			chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
			chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
			chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
			chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
			chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
			chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
			chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
			chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
			chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
			chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
			chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
			chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
			chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
			chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
			chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
			chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
			chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
			chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
			chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
			chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
			chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
			chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
			chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
			chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
			chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
			chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
			chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
			chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
			chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
			chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
			chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
			chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
			chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
			chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
			chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
			chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
			chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
			chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
			chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
			chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
			chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
			chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
			chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
			chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
			chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
			// Euro Sign
			chr(226).chr(130).chr(172) => 'E',
			// GBP (Pound) Sign
			chr(194).chr(163) => '');

			$string = strtr($string, $chars);
			} 
		else {
			// Assume ISO-8859-1 if not UTF-8
			$chars['in'] = chr(128).chr(131).chr(138).chr(142).chr(154).chr(158)
				.chr(159).chr(162).chr(165).chr(181).chr(192).chr(193).chr(194)
				.chr(195).chr(196).chr(197).chr(199).chr(200).chr(201).chr(202)
				.chr(203).chr(204).chr(205).chr(206).chr(207).chr(209).chr(210)
				.chr(211).chr(212).chr(213).chr(214).chr(216).chr(217).chr(218)
				.chr(219).chr(220).chr(221).chr(224).chr(225).chr(226).chr(227)
				.chr(228).chr(229).chr(231).chr(232).chr(233).chr(234).chr(235)
				.chr(236).chr(237).chr(238).chr(239).chr(241).chr(242).chr(243)
				.chr(244).chr(245).chr(246).chr(248).chr(249).chr(250).chr(251)
				.chr(252).chr(253).chr(255);

			$chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";

			$string = strtr($string, $chars['in'], $chars['out']);
			$double_chars['in'] = array(chr(140), chr(156), chr(198), chr(208), chr(222), chr(223), chr(230), chr(240), chr(254));
			$double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
			$string = str_replace($double_chars['in'], $double_chars['out'], $string);
			}
		return $string;
		}

	static function seemsUtf8($str) {
		$length = strlen($str);
		for ($i=0; $i < $length; $i++) {
			$c = ord($str[$i]);
			if ($c < 0x80) $n = 0; # 0bbbbbbb
			elseif (($c & 0xE0) == 0xC0) $n=1; # 110bbbbb
			elseif (($c & 0xF0) == 0xE0) $n=2; # 1110bbbb
			elseif (($c & 0xF8) == 0xF0) $n=3; # 11110bbb
			elseif (($c & 0xFC) == 0xF8) $n=4; # 111110bb
			elseif (($c & 0xFE) == 0xFC) $n=5; # 1111110b
			else return false; # Does not match any model
			for ($j=0; $j<$n; $j++) { # n bytes matching 10bbbbbb follow ?
				if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
					return false;
				}
			}
		return true;
		}

	static function utf8UriEncode( $utf8_string, $length = 0 ) {
		$unicode = '';
		$values = array();
		$num_octets = 1;
		$unicode_length = 0;

		$string_length = strlen( $utf8_string );
		for ($i = 0; $i < $string_length; $i++ ) {
			$value = ord( $utf8_string[ $i ] );
			if ( $value < 128 ) {
				if ( $length && ( $unicode_length >= $length ) )
					break;
				$unicode .= chr($value);
				$unicode_length++;
				} 
			else {
				if ( count( $values ) == 0 ) $num_octets = ( $value < 224 ) ? 2 : 3;

				$values[] = $value;

				if ( $length && ( $unicode_length + ($num_octets * 3) ) > $length )
					break;
				if ( count( $values ) == $num_octets ) {
					if ($num_octets == 3) {
						$unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]) . '%' . dechex($values[2]);
						$unicode_length += 9;
					} else {
						$unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]);
						$unicode_length += 6;
					}

					$values = array();
					$num_octets = 1;
					}
				}
			}
		return $unicode;
		}
	}
?>