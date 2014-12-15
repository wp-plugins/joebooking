<?php
class ntsConf {
	var $rawValues;
	var $arrayType = array();
	var $codeSet = '';
	var $_cache;
	var $error;

	function ntsConf(){
		$this->rawValues = array();
		$this->arrayType = array(
			'disabledNotifications',
			);

		$codeFile = NTS_APP_DIR . '/model/confSet.php';
		$code2run = file_get_contents( $codeFile );
		$code2run = str_replace( '<?php', '', $code2run );
		$code2run = str_replace( '?>', '', $code2run );
		$this->codeSet = $code2run;
		$this->_cache = array();
		$this->load();
		$this->error = '';
		}

	function getError(){
		return $this->error;
		}

	function load(){
		$this->rawValues = array();
		$ntsdb =& dbWrapper::getInstance();

		$ntsdb->alert_error = FALSE;
		$result = $ntsdb->select( array('name', 'value'), 'conf' );
		$ntsdb->alert_error = TRUE;
		if( $result ){
			while( $oInfo = $result->fetch() ){
				if( in_array($oInfo['name'], $this->arrayType)){
					if( ! isset($this->rawValues[ $oInfo['name'] ]) ){
						$this->rawValues[ $oInfo['name'] ] = array();
						}
					$this->rawValues[ $oInfo['name'] ][] = $oInfo['value'];
					}
				else {
					if( isset($this->rawValues[$oInfo['name']]) ){
						if( ! is_array($this->rawValues[ $oInfo['name'] ]) )
							$this->rawValues[ $oInfo['name'] ] = array( $this->rawValues[ $oInfo['name'] ] );
						$this->rawValues[ $oInfo['name'] ][] = $oInfo['value'];
						}
					else {
						$this->rawValues[ $oInfo['name'] ] = $oInfo['value'];
						}
					}
				}
			}
		else {
			return;
			}
		}

	function getLoadedNames(){
		$return = array_keys( $this->rawValues );
		return $return;
		}

	function get( $name ){
		if( ! isset($this->_cache[$name]) ){
			if( in_array($name, $this->arrayType) || (isset($this->rawValues[$name]) && is_array($this->rawValues[$name])) ){
				$rawValue = isset($this->rawValues[$name]) ? $this->rawValues[$name] : array();
				}
			else {
				$rawValue = isset($this->rawValues[$name]) ? $this->rawValues[$name] : '';
				$rawValue = trim( $rawValue );
				}

		/* actual code file */
			$return = $this->confGet( $name, $rawValue );

			$this->_cache[$name] = $return;
			}
		$return = $this->_cache[$name];
		return $return;
		}

	function force( $name, $value )
	{
		$conf->_cache[ $name ] = $value;
	}

	function set( $name, $value ){
		$return = $value;

	/* actual code file */
		eval( $this->codeSet );

		$this->saveProp( $name, $return );
		return $return;
		}

	function reset( $name ){
		$ntsdb =& dbWrapper::getInstance();
		$result = $ntsdb->delete( 
			'conf',
			array(
				'name' => array('=', $name)
				)
			);
		return $result;
		}

	function saveProp( $name, $newValue ){
		$ntsdb =& dbWrapper::getInstance();
		if( is_array($newValue) || in_array($name, $this->arrayType) ){
			$result = $ntsdb->delete( 
				'conf',
				array(
					'name' => array('=', $name)
					)
				);
			reset( $newValue );
			foreach( $newValue as $nv ){
				$result = $ntsdb->insert( 'conf', array('value' => $nv, 'name' => $name) );
				}
			}
		else {
			$sql = "SELECT value FROM {PRFX}conf WHERE name = '$name'";
			$result = $ntsdb->runQuery( $sql );
			$update = ( $oInfo = $result->fetch() ) ? true : false;

		/* update */
			if( $update ){
				$result = $ntsdb->update(
					'conf',
					array('value' => $newValue),
					array(
						'name' => array('=', $name)
						)
					);
				}
		/* insert */
			else {
				$result = $ntsdb->insert( 'conf', array('value' => $newValue, 'name' => $name) );
				}
			}

		$this->error = $result ? '' : $ntsdb->getError();
		if( ! $this->error ){
			unset( $this->_cache[$name] );
			$this->rawValues[$name] = $newValue;
			}
		return $result;
		}

	// Singleton stuff
	static function &getInstance(){
		return ntsLib::singletonFunction( 'ntsConf' );
		}

	function confGet( $name, $rawValue )
	{
		$return = $rawValue;
		switch( $name )
		{
			case 'autoResource':
				if( strlen($rawValue) == 0 )
					$return = 0;
				else
					$return = $rawValue;
				break;

			case 'autoLocation':
				if( strlen($rawValue) == 0 )
					$return = 0;
				else
					$return = $rawValue;
				break;

			case 'taxRate':
				if( strlen($rawValue) == 0 )
					$return = 0;
				else
					$return = $rawValue;
				break;

			case 'taxTitle':
				if( strlen($rawValue) == 0 )
					$return = 'Tax';
				else
					$return = $rawValue;
				break;

			case 'taxInclude':
				if( strlen($rawValue) == 0 )
					$return = 0;
				else
					$return = $rawValue;
				break;

			case 'htmlTitle':
				if( ! isset($this->rawValues[$name]) )
					$return = 'Appointment Scheduler';
				else
					$return = $rawValue;
				break;

			case 'remindOfBackup':
				if( strlen($rawValue) == 0 )
					$return = 7 * 24 * 60 * 60;
				else
					$return = $rawValue;
				break;

			case 'backupLastRun':
				if( strlen($rawValue) == 0 )
					$return = 0;
				else
					$return = $rawValue;
				break;

			case 'attachIcal':
				if( strlen($rawValue) == 0 )
					$return = 1;
				else
					$return = $rawValue;
				break;

			case 'remindBefore':
				if( ! $rawValue )
					$return = 3600;
				else
					$return = $rawValue;
				break;

			case 'autoComplete':
				if( ! $rawValue )
					$return = 0;
				else
					$return = $rawValue;
				break;

			case 'autoReject':
				if( ! $rawValue )
					$return = 0;
				else
					$return = $rawValue;
				break;

			case 'disabledNotifications':
				if( ! $rawValue ){
					$return = array();
					}
				else
					$return = $rawValue;
				break;

			case 'currency':
				if( ! $rawValue ){
					$return = 'usd';
					}
				break;

			case 'userEmailConfirmation':
				if( strlen($rawValue) == 0 )
					$return = 1;
				else
					$return = $rawValue;
				break;

			case 'emailAsUsername':
				if( strlen($rawValue) == 0 )
					$return = 0;
				else
					$return = $rawValue;
				break;

			case 'allowNoEmail':
				if( strlen($rawValue) == 0 )
					$return = 0;
				else
					$return = $rawValue;
				break;

			case 'userAdminApproval':
				if( strlen($rawValue) == 0 )
					$return = 1;
				else
					$return = $rawValue;
				break;

			case 'userLoginRequired':
				if( strlen($rawValue) == 0 )
					$return = 0;
				else
					$return = $rawValue;
				break;

			case 'enableRegistration':
				if( strlen($rawValue) == 0 )
					$return = 1;
				else
					$return = $rawValue;
				break;

			case 'enableTimezones':
				if( strlen($rawValue) == 0 )
					$return = 0;
				else
					$return = $rawValue;
				break;

			case 'emailSentFrom':
				if( ! $rawValue ){
					$return = 'your@email.here';
					}
				break;

			case 'emailSentFromName':
				if( ! $rawValue ){
					$return = 'Automated Mailer';
					}
				break;

			case 'emailDebug':
				if( ! $rawValue ){
					$return = 0;
					}
				$return = 0;
				break;

			case 'disablePrice':
				if( ! $rawValue )
					$return = 0;
				else
					$return = $rawValue;
				break;

			case 'priceFormat':
				if( ! $rawValue ){
					$return = array( '$', '.', ',', '' );
					}
				else
					$return = explode( '||', $rawValue );
				break;

			case 'languages':
				if( ! $rawValue )
				{
					$return = array( 'en' );
				}
				else
					$return = explode( '||', $rawValue );
				break;

			case 'weekStartsOn':
				if( ! strlen($rawValue) )
					$return = 0;
				elseif( $rawValue == 7 )
					$return = 0;
				else
					$return = $rawValue;
				break;

			case 'dateFormat':
				if( ! $rawValue )
					$return = 'j M Y';
				else
					$return = $rawValue;
				break;

			case 'timeFormat':
				if( ! $rawValue )
					$return = 'g:ia';
				else
					$return = $rawValue;
				break;

			case 'companyTimezone':
				if( ! $rawValue )
					$return = 'America/Los_Angeles';
				break;

			case 'theme':
				if( ! $rawValue )
					$return = 'default';
				break;

			case 'minFromNowTomorrow':
				if( ! $rawValue )
					$return = 'tomorrow';
				break;

			case 'appointmentFlow':
				if( ! $rawValue )
				{
					$return = array(
						array( 'location',	'manual' ),
						array( 'resource',	'manual' ),
						array( 'service',	'manual' ),
						array( 'seats',		'manual' ),
						array( 'time',		'manual' ),
						);
				}
				else
				{
					$raw = explode( '|', $rawValue );
					reset( $raw );
					$return = array();
					foreach( $raw as $rr )
					{
						$r = explode( ':', $rr ); 
						$return[] = $r;
						if( $r[0] == 'service' )
							$return[] = array( 'seats', 'manual' );
					}
				}
				break;

			case 'appointmentFlowJustOne':
				if( strlen($rawValue) == 0 )
					$return = 0;
				else
					$return = $rawValue;
				break;

			case 'paymentGateways':
				if( ! $rawValue ){
					$return = array();
					}
				else
					$return = explode( '||', $rawValue );
				break;

			case 'plugins':
				if( ! $rawValue ){
					$return = array();
					}
				else
					$return = explode( '||', $rawValue );
				break;

			case 'monthsToShow':
				if( ! $rawValue )
					$return = 1;
				else
					$return = $rawValue;
				break;

			case 'limitTimeMeasure':
				if( ! $rawValue )
					$return = 'day';
				else
					$return = $rawValue;
				break;

			case 'csvDelimiter':
				if( ! $rawValue )
					$return = ',';
				else
					$return = $rawValue;
				break;

			case 'requireCancelReason':
				if( strlen($rawValue) == 0 )
					$return = 1;
				else
					$return = $rawValue;
				break;

			case 'sosCode':
				if( ! $rawValue )
					$return = '';
				break;

			case 'allowDuplicateEmails':
				if( ! $rawValue )
					$return = 0;
				break;

			case 'timeUnit':
				if( ! $rawValue )
					$return = 15;
				break;

			case 'timeStarts':
				if( ! strlen($rawValue) )
					$return = 9 * 60 * 60;
				break;

			case 'appsInCart':
				if( ! strlen($rawValue) )
					$return = 3;
				break;

			case 'timeEnds':
				if( ! $rawValue )
					$return = 18 * 60 * 60;
				break;

			case 'useCaptcha':
				if( strlen($rawValue) == 0 )
					$return = 0;
				else
					$return = $rawValue;
				break;

			case 'strongPassword':
				if( strlen($rawValue) == 0 )
					$return = 0;
				else
					$return = $rawValue;
				break;

			case 'sendCcForAppointment':
				if( strlen($rawValue) == 0 )
					$return = 0;
				else
					$return = $rawValue;
				break;

			case 'autoActivatePackage':
				if( strlen($rawValue) == 0 )
					$return = 0;
				else
					$return = $rawValue;
				break;

			case 'customerCanCancel':
				if( strlen($rawValue) == 0 )
					$return = 1;
				else
					$return = $rawValue;
				break;

			case 'customerCanReschedule':
				if( strlen($rawValue) == 0 )
					$return = 1;
				else
					$return = $rawValue;
				break;

			case 'invoiceHeader':
				if( ! isset($this->rawValues[$name]) )
					$return =<<<EOT
<strong>Our Company</strong>
Our Address
http://www.oursite.com
EOT;
				else
					$return = $rawValue;
				break;

			case 'attachMaxSize':
				if( strlen($rawValue) == 0 )
					$return = 200 * 1024;
				else
					$return = $rawValue;
				break;

			case 'attachAllowed':
				if( (! is_array($rawValue)) OR (! $rawValue) )
					$return = array( 'gif', 'jpg', 'png', 'doc', 'docx', 'pdf' );
				else
					$return = $rawValue;
				break;

			case 'variableDurationServices':
				if( strlen($rawValue) == 0 )
					$return = 0;
				else
					$return = $rawValue;
				break;
		}
		return $return;
	}
}

class ntsPluginManager {
	var $dir;
	var $plugins;

	function ntsPluginManager(){
		$this->dir = array(
			NTS_APP_DIR . '/plugins',
			NTS_EXTENSIONS_DIR . '/plugins'
			);
		$this->plugins = $this->_getActivePlugins();
		}

	function getActivePlugins(){
		return $this->plugins;
		}

	function isActive( $plugin )
	{
		$return = FALSE;
		$active = $this->getActivePlugins();
		if( in_array($plugin, $active) )
		{
			$return = TRUE;
		}
		return $return;
	}

	function getPanels( $plugin ){
		$return = array();
		$panelsFile = $this->getPluginFolder( $plugin ) . '/panels.php';
		if( file_exists($panelsFile) ){
			require( $panelsFile );
			$return = $panels;
			}
		return $return;
		}

	function _getActivePlugins(){
		$conf =& ntsConf::getInstance();
		$currentPlugins = $conf->get( 'plugins' );
		$currentPlugins = array_unique( $currentPlugins );

		reset( $currentPlugins );
		// check if every plugin is still there
		foreach( $currentPlugins as $p ){
			$pluginFolder = $this->getPluginFolder( $p );
			if( ! file_exists($pluginFolder) ){
				$this->pluginDisable( $p );
				continue;
				}
			$infoFile = $pluginFolder . '/info.php';
			if( ! file_exists($infoFile) ){
				$this->pluginDisable( $p );
				continue;
				}
			require( $infoFile );
			$require = ntsLib::parseVersionNumber( $requireVersion );

			$appInfo = ntsLib::getAppInfo();
			$systemVersion = ntsLib::parseVersionNumber( $appInfo['core_version'] );
			if( $systemVersion < $require ){
				$this->pluginDisable( $p );
				continue;
				}

			$functionsFile = $pluginFolder . '/functions.php';
			if( file_exists($functionsFile) ){
				include_once( $functionsFile );
				}
			}

		return $currentPlugins;
		}

	function pluginActivate( $plugin ){
		$conf =& ntsConf::getInstance();

		$currentPlugins = $this->getActivePlugins();
		$currentPlugins[] = $plugin;
		$currentPlugins = array_unique( $currentPlugins );

		$conf->set( 'plugins', $currentPlugins );

		/* run install file */
		$plgFolder = $this->getPluginFolder( $plugin );
		$installFile = $plgFolder . '/install.php';
		if( file_exists($installFile) ){
			require($installFile);
			}

		$result = true;
		return $result;
		}

	function pluginDisable( $plugin ){
		$conf =& ntsConf::getInstance();

		$currentPlugins = $this->getActivePlugins();
		$newCurrentPlugins = array();

		if( is_array($currentPlugins) )
		{
			reset( $currentPlugins );
			foreach( $currentPlugins as $plg )
			{
				if( $plg == $plugin )
					continue;
				$newCurrentPlugins[] = $plg;
			}
		}
		$newCurrentPlugins = array_unique( $newCurrentPlugins );

		$conf->set( 'plugins', $newCurrentPlugins );

		/* run uninstall file */
		$plgFolder = $this->getPluginFolder( $plugin );
		$uninstallFile = $plgFolder . '/uninstall.php';
		if( file_exists($uninstallFile) ){
			require($uninstallFile);
			}

		$result = true;
		return $result;
		}

	function getPlugins(){
		$plugins = array();

		reset( $this->dir );
		foreach( $this->dir as $dir ){
			$folders = ntsLib::listSubfolders( $dir );
			reset( $folders );
			foreach( $folders as $f ){
				$plugins[$f] = $f;
				}
			}

		$return = array_keys($plugins);
		return $return;
		}

	function getPluginFolder( $plg ){
		$folderName = $plg;
		reset( $this->dir );
		foreach( $this->dir as $dir ){
			$fullFolderName = $dir . '/' . $folderName;
			if( file_exists($fullFolderName) )
				break;
			}
		return $fullFolderName;
		}

	function getPluginSettings( $plg ){
		$return = array();
		$conf =& ntsConf::getInstance();

		$confPrefix = 'plugin-' . $plg . '-';
		$allSettingsNames = $conf->getLoadedNames();
		reset( $allSettingsNames );
		foreach( $allSettingsNames as $confName ){
			if( substr($confName, 0, strlen($confPrefix)) == $confPrefix ){
				$shortName = substr($confName, strlen($confPrefix));
				$confValue = $conf->get( $confName );
				$return[ $shortName ] = $confValue;
				}
			}
		return $return;
		}

	function getPluginSetting( $plg, $settingName ){
		$conf =& ntsConf::getInstance();
		$confPrefix = 'plugin-' . $plg . '-';
		$settingName = $confPrefix . $settingName;
		return $conf->get( $settingName );
		}

	function savePluginSetting( $plg, $settingName, $settingValue ){
		$conf =& ntsConf::getInstance();
		$confPrefix = 'plugin-' . $plg . '-';
		$settingName = $confPrefix . $settingName;
		return $conf->set( $settingName, $settingValue );
		}

	// Singleton stuff
	static function &getInstance(){
		return ntsLib::singletonFunction( 'ntsPluginManager' );
		}
	}

class ntsAttachManager
{
	var $dir = '';
	var $error = '';
	var $allowed_types = '';
	var $max_size = 0;

	function __construct()
	{
		$ntsConf =& ntsConf::getInstance();
		$allowed_types = $ntsConf->get('attachAllowed');
		$this->allowed_types = $allowed_types;

		$max_size = $ntsConf->get('attachMaxSize');
		$this->max_size = $max_size / 1024;

		$ri = ntsLib::remoteIntegration();
		if( $ri == 'wordpress' )
		{
			$wp_upload_dir = wp_upload_dir();
			$dir = $wp_upload_dir['basedir'];

			$app_info = ntsLib::getAppInfo();
			$subdir = $app_info['app_short'];

			$dir = $dir . '/' . $subdir;
			if( ! file_exists($dir) )
			{
				@wp_mkdir_p( $dir );
			}
		}
		else
		{
			$base_dir = realpath( NTS_APP_DIR . '/..' );
			$dir = $base_dir . '/uploads';
		}
		$this->dir = $dir;

		if( ! file_exists($dir) )
		{
			$this->set_error( M("Upload dir does not exist") );

			$ntsConf = ntsConf::getInstance();
			$enabled = $ntsConf->get('attachEnableCompany');
			if( $enabled )
			{
				$ntsConf->set( 'attachEnableCompany', 0 );
			}
			return;
		}
		if( ! ntsLib::is_really_writable($dir) )
		{
			$this->set_error( M("Upload dir is not writable") );

			$ntsConf = ntsConf::getInstance();
			$enabled = $ntsConf->get('attachEnableCompany');
			if( $enabled )
			{
				$ntsConf->set( 'attachEnableCompany', 0 );
			}
			return;
		}
		$this->dir = $dir;
	}

	function get_error()
	{
		return $this->error;
	}

	function set_error( $err )
	{
		$this->error = $err;
	}

	private function _complete_info( $e )
	{
		$return = array();

		$file = $e['meta_value'];
		$full_file = $this->dir . '/' . $file;

		if( file_exists($full_file) )
		{
			$hash = substr( $file, 2, 16 );
			$data = explode( ':', $e['meta_data'] );
			$created_at = isset($data[0]) ? $data[0] : 0;
			$created_by = isset($data[1]) ? $data[1] : 0;
			$file_type = isset($data[2]) ? $data[2] : '';
			$file = $e['meta_value'];
			$full_file = $this->dir . '/' . $file;

			$return = array(
				'id'			=> $e['id'],
				'hash'			=> $hash,
				'file'			=> $file,
				'full_file'		=> $full_file,
				'created_at'	=> $created_at,
				'created_by'	=> $created_by,
				'file_type'		=> $file_type,
				'is_image'		=> $this->is_image($file_type)
				);
		}
		return $return;
	}

	function get_by_hash( $hash )
	{
		$return = array();

		$ntsdb =& dbWrapper::getInstance();
		$where = array(
			'meta_value'	=> array( 'LIKE', 'hc' . $hash . '_%' ),
			);

		$result = $ntsdb->select( array('id', 'meta_value', 'meta_data'), 'objectmeta', $where );
		if( $result && ($e = $result->fetch()) )
		{
			$return = $this->_complete_info( $e );
		}
		return $return;
	}

	function get_by_id( $id )
	{
		$return = array();

		$ntsdb =& dbWrapper::getInstance();
		$where = array(
			'id'	=> array( '=', $id ),
			);

		$result = $ntsdb->select( array('id', 'meta_value', 'meta_data'), 'objectmeta', $where );
		if( $result && ($e = $result->fetch()) )
		{
			$return = $this->_complete_info( $e );
		}
		return $return;
	}

	function get( $parentClass, $parentId )
	{
		$return = array();

		$ntsdb =& dbWrapper::getInstance();
		$where = array(
			'obj_class'		=> array( '=', $parentClass ),
			'obj_id'		=> array( '=', $parentId ),
			'meta_name'		=> array( '=', '_attach' ),
			);

		$result = $ntsdb->select( array('id', 'meta_value', 'meta_data'), 'objectmeta', $where );
		while( $e = $result->fetch() )
		{
			$info = $this->_complete_info( $e );
			if( $info )
				$return[] = $info;
		}
		return $return;
	}

	function delete( $id )
	{
		$return = TRUE;
		$file = $this->get_by_id( $id );
		if( $file )
		{
			/* remove file */
			$full_file = $this->dir . '/' . $file['file'];
			if( unlink( $full_file ) )
			{
				$ntsdb =& dbWrapper::getInstance();
				$where = array(
					'id'	=> array('=', $id)
					);
				$result = $ntsdb->delete('objectmeta', $where );
			}
		}
		return $return;
	}

	function original_name( $full_name )
	{
		$return = substr( $full_name, (16+3) );
		return $return;
	}

	function add( $file_array, $parentClass, $parentId )
	{
		/* generate new name */
		$rand = ntsLib::generateRand( 16, array('caps' => FALSE, 'digits' => FALSE, 'letters' => FALSE, 'hex' => TRUE) );
		$new_name = $file_array['name'];
		$new_name = str_replace( ' ', '_', $new_name );
		$final_name = 'hc' . $rand . '_' . $new_name;

		$conf = array(
			'upload_path'	=> $this->dir,
			'file_name'		=> $final_name,
			'allowed_types'	=> join( '|', $this->allowed_types ),
			'max_size'		=> $this->max_size,
			);
		$upload = new ntsUpload( $conf );
		if( $upload->do_upload('nts-attach') )
		{
			$data = $upload->data();

			$ntsdb =& dbWrapper::getInstance();
			$now = time();

			$metaData = join(
				':', 
				array(
					$now,
					ntsLib::getCurrentUserId(),
					$data['file_type']
					)
				);

			$newValues = array(
				'obj_class'		=> $parentClass,
				'obj_id'		=> $parentId,
				'meta_name'		=> '_attach',
				'meta_value'	=> $final_name,
				'meta_data'		=> $metaData,
				);
			$result = $ntsdb->insert('objectmeta', $newValues );
		}
		else
		{
			$err = $upload->display_errors();
			$this->set_error( $err );
		}
	}

	public function is_image( $file_type )
	{
		$png_mimes  = array('image/x-png');
		$jpeg_mimes = array('image/jpg', 'image/jpe', 'image/jpeg', 'image/pjpeg');

		if (in_array($file_type, $png_mimes))
		{
			$file_type = 'image/png';
		}

		if (in_array($file_type, $jpeg_mimes))
		{
			$file_type = 'image/jpeg';
		}

		$img_mimes = array(
							'image/gif',
							'image/jpeg',
							'image/png',
						);

		return (in_array($file_type, $img_mimes, TRUE)) ? TRUE : FALSE;
	}
}

define( 'NTS_LANGUAGE_COOKIE_NAME', 'rtr-language' );
include_once( NTS_LIB_DIR . '/lib/xml/xml-simple.php' );

function M( $str, $params = array(), $skipCustom = false )
{
	if( ! isset($GLOBALS['NTS_CURRENT_LANGUAGE']) )
	{
		$lm =& ntsLanguageManager::getInstance();
	}

	$return = '';

	$current_lang = $GLOBALS['NTS_CURRENT_LANGUAGE'];
	if( $current_lang == 'en-builtin' )
		$current_lang = 'en';

/* replace html if any */
	$str = preg_replace( '/\<(.+)\>/U', '[\\1]', $str );

	$languageCustom = isset($GLOBALS['NTS_LANGUAGE_CUSTOM'][$current_lang]) ? $GLOBALS['NTS_LANGUAGE_CUSTOM'][$current_lang] : array();
	if( isset($languageCustom[$str]) )
	{
		$return = $languageCustom[$str];
	}
	else
	{
		if( $current_lang == 'en' )
		{
			$return = $str;
		}
		else
		{
			$languageConf = $GLOBALS['NTS_CURRENT_LANGUAGE_CONF'];
			if( isset($languageConf['interface'][$str]) )
			{
				$return = $languageConf['interface'][$str];
			}
			else
			{
				$return = $str;
			}
		}
	}

	/* put back html if any */
	$return = preg_replace( '/\[(.+)\]/U', '<\\1>', $return );

	if( $params )
	{
		reset( $params );
		foreach( $params as $key => $value )
		{
			$return = str_replace( '{' . $key . '}', $value, $return );
		}
	}
	return $return;
}

function M2( $str, $params = array() ){
	return M( $str, $params );
	}

class ntsLanguageManager {
	var $dir;
	var $languages;

	function ntsLanguageManager(){
//		$this->dir = NTS_EXTENSIONS_DIR . '/languages';

		$this->dir = array(
			NTS_EXTENSIONS_DIR . '/languages',
			NTS_APP_DIR . '/languages'
			);

		$this->languages = array();
		$this->init();
		}

	function init()
	{
		global $NTS_CURRENT_USER;
		if( isset($NTS_CURRENT_USER) && $NTS_CURRENT_USER )
		{
			$this->setLanguage( $NTS_CURRENT_USER->getLanguage() );
		}
		else
		{
			$this->setLanguage( $this->getDefaultLanguage() );
		}
	}

	function reset_custom( $lng, $original )
	{
		$ntsdb =& dbWrapper::getInstance();
		$where = array(
			'lang'		=> array('=', $lng),
			'original'	=> array('=', $original),
			);
		$ntsdb->delete(
			'languages',
			$where
			);
	}

	function set_custom( $lng, $original, $custom )
	{
		$current_custom = $this->get_custom( $lng );
		$update = isset($current_custom[$original]) ? TRUE : FALSE;

		$ntsdb =& dbWrapper::getInstance();
		if( $update )
		{
			if(
				( $lng == 'en' )
				&&
				( $original == $custom )
			)
			{
				$this->reset_custom( $lng, $original );
			}
			else
			{
				$what = array(
					'custom'	=> $custom,
					);
				$where = array(
					'lang'		=> array('=', $lng),
					'original'	=> array('=', $original),
					);

				$ntsdb->update(
					'languages',
					$what,
					$where
					);
			}
		}
		else
		{
			if(
				( $lng == 'en' )
				&&
				( $original == $custom )
			)
			{
				/* no need for that as it's not changed */
				$this->reset_custom( $lng, $original );
			}
			else
			{
				$what = array(
					'lang'		=> $lng,
					'original'	=> $original,
					'custom'	=> $custom,
					);
				$ntsdb->insert(
					'languages',
					$what
					);
			}
		}
	}

	function get_custom( $lng )
	{
		if( $lng == 'en-builtin' )
			$lng = 'en';

		if( ! isset($GLOBALS['NTS_LANGUAGE_CUSTOM']) )
		{
			$GLOBALS['NTS_LANGUAGE_CUSTOM'] = array();
		}

		if( ! isset($GLOBALS['NTS_LANGUAGE_CUSTOM'][$lng]) )
		{
			$GLOBALS['NTS_LANGUAGE_CUSTOM'][$lng] = array();

			$ntsdb =& dbWrapper::getInstance();
//			$ntsdb->_debug = TRUE;
			if( $ntsdb->tableExists('languages') )
			{
				$where = array(
					'lang'	=> array('=', $lng),
					);
				$custom = $ntsdb->get_select(
					array('original', 'custom'),
					'languages',
					$where
					);
				foreach( $custom as $c )
				{
					$GLOBALS['NTS_LANGUAGE_CUSTOM'][$lng][$c['original']] = $c['custom'];
				}
			}
//			$ntsdb->_debug = FALSE;
		}
		return $GLOBALS['NTS_LANGUAGE_CUSTOM'][$lng];
	}

	function setLanguage( $lng )
	{
		$currentLanguage = $lng;
		$languageConf = $this->getLanguageConf( $currentLanguage );

	/* problem with this file */
		while( isset($languageConf['error']) && $languageConf['error'] )
		{
		/* remove from active languages */
			$newSetting = $this->languageDisable( $currentLanguage );
			$conf->set( 'languages', $newSetting );
			if( $newSetting )
			{
				$currentLanguage = $newSetting[0];
			}
			else
			{
				$currentLanguage = 'en';
			}
			$languageConf = $this->getLanguageConf( $currentLanguage );
		}
		$this->get_custom( $lng );

	/* file ok */
		$GLOBALS['NTS_CURRENT_LANGUAGE'] = $currentLanguage;
		$GLOBALS['NTS_CURRENT_LANGUAGE_CONF'] = $languageConf;
	}

	function languageActivate( $newLanguage ){
		$conf =& ntsConf::getInstance();
		$setting = $conf->get( 'languages' );

		$languageAdded = '';
		if( ! in_array($newLanguage, $setting) ){
			$setting[] = $newLanguage;
//			$setting = array( $newLanguage );
			$languageAdded = $newLanguage;
			}

		if( $languageAdded == 'en' ){
			$temp = $setting;
			$setting = array();
			reset( $temp );
			foreach( $temp as $t ){
				if( $t != 'en-builtin' ){
					$setting[] = $t;
//					$setting = array( $newLanguage );
					}
				}
			}
		if( $languageAdded == 'en-builtin' ){
			$temp = $setting;
			$setting = array();
			reset( $temp );
			foreach( $temp as $t ){
				if( $t != 'en' ){
					$setting[] = $t;
//					$setting = array( $newLanguage );
					}
				}
			}
		return $setting;
		}

	function languageDisable( $disableLanguage ){
		$conf =& ntsConf::getInstance();
		$setting = $conf->get( 'languages' );

		$newSetting = array();
		reset( $setting );
		foreach( $setting as $s ){
			if( $s == $disableLanguage )
				continue;
			$newSetting[] = $s;
			}

		return $newSetting;
		}

	function getDefaultLanguage(){
		$activeLanguages = $this->getActiveLanguages();
		if( ! $activeLanguages )
		{
			$activeLanguages = array( 'en' );
		}

		if( defined('NTS_DEFAULT_LANGUAGE') && NTS_DEFAULT_LANGUAGE && in_array(NTS_DEFAULT_LANGUAGE, $activeLanguages) )
		{
			$lng = NTS_DEFAULT_LANGUAGE;
		}
		else
		{
			$lng = $activeLanguages[0];
		}
		return $lng;
		}

	function getActiveLanguages()
	{
		$active = array();
		$conf =& ntsConf::getInstance();
		$languages = $this->getLanguages();
		$activeLanguages = $conf->get('languages');

		reset( $activeLanguages );
		foreach( $activeLanguages as $l )
		{
			if( in_array($l, $languages) )
				$active[] = $l;
		}

		if( ! $active )
		{
			$active = array( 'en' );
		}

		return $active;
	}

	function getLanguages(){
		$languages = array();

		reset( $this->dir );
		$folders = array();
		foreach( $this->dir as $d )
		{
			$this_folders = ntsLib::listSubfolders( $d );
			$folders = array_merge( $folders, $this_folders );
		}

		reset( $folders );
		foreach( $folders as $folder )
		{
			reset( $this->dir );
			foreach( $this->dir as $d )
			{
				$fileName = $d . '/' . $folder . '/interface.xml';
				if( file_exists($fileName) )
				{
					if( ! in_array($folder, $languages) )
					{
						$languages[] = $folder;
						break;
					}
				}
			}
		}

		if( ! in_array('en', $languages) )
		{
			array_unshift( $languages, 'en' );
		}
		return $languages;
		}

	function getLanguageConf( $lng )
	{
		if( ($lng == 'en') )
		{
			$return = $this->getLanguageConf( 'languageTemplate' );
			if( $this->languageFileExists($lng) )
			{
				if( ! isset($this->languages[$lng]) )
				{
					$this->loadLanguageFile( $lng );
				}
				$sub_return = $this->languages[$lng];
				$sub_return['interface'] = array_merge( $return['interface'], $sub_return['interface'] );
				$return = $sub_return;
//				$return['language'] = 
//				$return['interface'] = array_merge( $return['interface'], $this->languages[$lng]['interface'] );
			}
			return $return;

			$return = array(
				'language'	=> 'English Built-In',
				'error'		=> '',
				'charset'	=> 'utf-8',
				);
			return $return;
		}

		if( ! isset($this->languages[$lng]) )
		{
			$this->loadLanguageFile( $lng );
		}
		return $this->languages[$lng];
	}

	function languageFileExists( $lng )
	{
		$f = $lng . '/interface.xml';
		$return = '';
		if( $lng == 'languageTemplate' )
		{
			$return = NTS_APP_DIR . '/defaults/language/interface.xml';
		}
		else
		{
			reset( $this->dir );
			foreach( $this->dir as $d )
			{
				$fullFileName = $d . '/' . $f;
				if( file_exists($fullFileName) )
				{
					$return = $fullFileName;
					break;
				}
			}
		}
		return $return;
	}

	function loadLanguageFile( $lng ){

		$file_exists = FALSE;
		if( $lng == 'languageTemplate' )
		{
			$fullFileName = NTS_APP_DIR . '/defaults/language/interface.xml';
		}
		else
		{
			$fullFileName = $this->languageFileExists( $lng );
		}

//echo "loading lang file $fullFileName<br>";
		if( ($lng != 'en-builtin') && $fullFileName ){
			$thisLangStrings = array();
			$xmlCode = ntsLib::fileGetContents( $fullFileName );

			/* get first line to see if encoding is defined */
			$firstLine = ntsLib::fileGetFirstLine( $fullFileName );
			$re = '/encoding\s*=\s*[\'|\"](.+)[\'|\"]/U';
			if( preg_match($re, $firstLine, $ma) ){
				$encoding = $ma[1];
				$parser = new xml_simple( $encoding );
				}
			else {
				$parser = new xml_simple();
				}

			$languageConf = $parser->parse( $xmlCode );
			$languageConf['error'] = '';
			$thisLangStrings = array();

			if( ! $parser->error ){
				if( (! isset($languageConf['string'][0])) && is_array($languageConf['string']) ){
					$languageConf['string'] = array( $languageConf['string'] );
					}
				reset( $languageConf );
				foreach( $languageConf['string'] as $a ){
					$thisLangStrings[ $a['original'] ] = $a['translate'];
					}
				}
			else {
				$languageConf['error'] = $parser->error;
				}

			/* if more than one country is enabled, add the country names as well */
			if( $lng == 'languageTemplate' ){
				$conf =& ntsConf::getInstance();
				$currentCountries = $conf->get('countries');
				if( count($currentCountries) > 1 ){
					require( NTS_APP_DIR . '/helpers/countries.php' );
					reset( $currentCountries );
					foreach( $currentCountries as $ccode )
						$thisLangStrings[ $countries[$ccode] ] = $countries[$ccode];
					}
				}

			$languageConf['interface'] = $thisLangStrings;

			/* also add templates */
			$tm =& ntsEmailTemplateManager::getInstance();
			$templateLng = ( $lng == 'languageTemplate' ) ? 'en' : $lng;
			$templateKeys = $tm->getKeys( $templateLng );

			$languageConf['templates'] = array();
			reset( $templateKeys );
			foreach( $templateKeys as $tk )
				$languageConf['templates'][ $tk ] = '';

			$this->languages[ $lng ] = $languageConf;
			}
		else {
			echo "language file '$fullFileName' doesn't exist!";
			}
		}

	// Singleton stuff
	static function &getInstance(){
		return ntsLib::singletonFunction( 'ntsLanguageManager' );
		}
	}