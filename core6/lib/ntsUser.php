<?php
define( 'NTS_USER_LOGIN_HASH', 'nts_user_hash' );

class ntsUser extends ntsObject {
	var $_disabled_panels = array();

	function ntsUser(){
		parent::ntsObject( 'user' );
		}

	static function getCurrent()
	{
		global $NTS_CURRENT_USER;
		return $NTS_CURRENT_USER;
	}

	function setProp( $pName, $pValue, $fromStorage = false )
	{
		switch( $pName )
		{
			case '_timezone':
				if( $this->getId() == 0 )
				{
					$_SESSION['nts_timezone'] = $pValue;
				}
				else
				{
					return parent::setProp( $pName, $pValue, $fromStorage );
				}
				break;

			default:
				return parent::setProp( $pName, $pValue, $fromStorage );
				break;
		}
	}

	function setLanguage( $lng )
	{
		$lm =& ntsLanguageManager::getInstance();
		$activeLanguages = $lm->getActiveLanguages();
		reset( $activeLanguages );
		$languageExists = false;
		foreach( $activeLanguages as $l )
		{
			if( $l == $lng )
			{
				$languageExists = true;
				break;
			}
		}
		if( $languageExists)
		{
			$expireIn = time() + 30 * 24 * 60 * 60;
			setcookie( NTS_LANGUAGE_COOKIE_NAME, $lng, $expireIn );

			$this->setProp( '_lang', $lng );
			$cm =& ntsCommandManager::getInstance();
			$cm->runCommand( $this, 'update' );
		}
	}

	function getLanguage()
	{
		$lng = '';
		if( isset($_COOKIE[NTS_LANGUAGE_COOKIE_NAME]) )
		{
			$lng = $_COOKIE[NTS_LANGUAGE_COOKIE_NAME];
		}
		$savedLng = $this->getProp( '_lang' );
		if( $savedLng )
		{
			$lng = $savedLng;
		}

		$lm =& ntsLanguageManager::getInstance(); 
		$activeLanguages = $lm->getActiveLanguages();
		if( ! $activeLanguages )
			$activeLanguages = array( 'en' );

		if( $lng )
		{
			reset( $activeLanguages );
			$languageExists = false;
			foreach( $activeLanguages as $l )
			{
				if( $l == $lng )
				{
					$languageExists = true;
					break;
				}
			}
			if( ! $languageExists )
				$lng = $lm->getDefaultLanguage();
		}
		else
		{
			$lng = $lm->getDefaultLanguage();
		}
		return $lng;
	}

	function statusLabel( $force_text = NULL, $html_element = 'span' )
	{
		$restrictions = $this->getProp( '_restriction' );
		return ntsUser::_statusLabel( $restrictions, $force_text, $html_element );
	}

	static function _statusLabel( $restrictions, $force_text = NULL, $html_element = 'span' )
	{
		$class = array();
		$main = 'btn';
		$class[] = $main;
		$class[] = $main . '-xs';
		$message = '';

		if( in_array('email_not_confirmed', $restrictions) )
		{
			$class[] = $main . '-default';
			$message = M('Email') . ': ' . M('Not Confirmed');
		}
		elseif( in_array('not_approved', $restrictions) )
		{
			$class[] = $main . '-warning';
			$message = M('Not Approved');
		}
		elseif( in_array('suspended', $restrictions) )
		{
			$class[] = $main . '-danger';
			$message = M('Suspended');
		}
		else
		{
			$class[] = $main . '-success';
			$message = M('Active');
		}

		$class = join( ' ', $class );
		$out = '<' . $html_element . ' class="' . $class . '" title="' . $message . '"';
		$out .= '>';
		if( $force_text === NULL )
			$out .= $message;
		else
		{
			if( ! strlen($force_text) )
				$force_text = '&nbsp';
			$out .= $force_text;
		}
		$out .= '</' . $html_element . '>';
		return $out;
	}

	function statusText()
	{
		list( $alert, $cssClass, $message ) = $this->getStatus();
		return $message;
	}

	function getStatus(){
		$alert = 0;
		$cssClass = '';
		$message = '';
		$return = array( $alert, $cssClass, $message );

		$restrictions = $this->getProp( '_restriction' );

		if( in_array('email_not_confirmed', $restrictions) ){
			$alert = 1;
			$cssClass = 'warning';
			$message = M('Email') . ': ' . M('Not Confirmed');
			}
		elseif( in_array('not_approved', $restrictions) ){
			$alert = 1;
			$cssClass = 'warning';
			$message = M('Not Approved');
			}
		elseif( in_array('suspended', $restrictions) ){
			$alert = 1;
			$cssClass = 'danger';
			$message = M('Suspended');
			}
		else {
			$alert = 0;
			$cssClass = 'ok';
			$message = M('Active');
			}

		$return = array( $alert, $cssClass, $message );
		return $return;
		}
		
	function setId( $id, $load = true )
	{
		if( $id == -111 )
		{
			$this->id = $id;
			$this->setProp( '_role', array('admin') );
			$this->setProp( 'username', '-superadmin-' );
			$this->setProp( 'first_name', '-superadmin-' );

		// resource schedules
			$resApps = array();
			$resSchedules = array();
			$allResourcesIds = ntsObjectFactory::getAllIds( 'resource' );
			reset( $allResourcesIds );
			foreach( $allResourcesIds as $resId )
			{
				$resApps[ $resId ] = array( 'view' => 1, 'edit' => 1, 'modify' => 1 );
				$resSchedules[ $resId ] = array( 'view' => 1, 'edit' => 1 );
			}
			$this->setAppointmentPermissions( $resApps );
			$this->setSchedulePermissions( $resSchedules );
			return;
		}
		elseif( $id == -1 )
		{
			$this->id = $id;
			$this->setProp( '_role', array('admin') );
			$this->setProp( 'username', '-system-' );
			$this->setProp( 'first_name', '-system-' );
			return;
		}
		parent::setId( $id, $load );
	}

	function getAppointmentPermissions(){
		$return = array();
		$raw = $this->getProp( '_resource_apps' );
		reset( $raw );
		foreach( $raw as $resId => $accLevel ){
			$perm = array( 'view' => 0, 'edit' => 0, 'notified' => 0 );
			if( $accLevel & 1 ){
				$perm['view'] = 1;
				}
			if( $accLevel & 2 ){
				$perm['edit'] = 1;
				}
			if( $accLevel & 4 ){
				$perm['notified'] = 1;
				}
			$return[ $resId ] = $perm;
			}
		return $return;
		}

	function setAppointmentPermissions( $pa ){
		$return = array();
		reset( $pa );
		foreach( $pa as $resId => $perm ){
			if( ! $perm )
				continue;
			$final = 0;
			if( isset($perm['view']) && $perm['view'] ){
				$final += 1;
				}
			if( isset($perm['edit']) && $perm['edit'] ){
				$final += 2;
				if( ! (isset($perm['view']) && $perm['view']) ){
					$final += 1; // also set view
					}
				}
			if( isset($perm['notified']) && $perm['notified'] ){
				$final += 4;
				if( ! (isset($perm['view']) && $perm['view']) ){
					if( ! (isset($perm['edit']) && $perm['edit']) ){
						$final += 1; // also set view
						}
					}
				}
			$return[ $resId ] = $final;
			}
		$this->setProp( '_resource_apps', $return );
		}

	function getSchedulePermissions(){
		$return = array();
		$raw = $this->getProp( '_resource_schedules' );
		reset( $raw );
		foreach( $raw as $resId => $accLevel ){
			$perm = array( 'view' => 0, 'edit' => 0 );
			if( $accLevel & 1 ){
				$perm['view'] = 1;
				}
			if( $accLevel & 2 ){
				$perm['edit'] = 1;
				}
			$return[ $resId ] = $perm;
			}
		return $return;
		}

	function setSchedulePermissions( $pa ){
		$return = array();
		reset( $pa );
		foreach( $pa as $resId => $perm ){
			if( ! $perm )
				continue;
			$final = 0;
			if( isset($perm['view']) && $perm['view'] ){
				$final += 1;
				}
			if( isset($perm['edit']) && $perm['edit'] ){
				$final += 2;
				if( ! (isset($perm['view']) && $perm['view']) ){
					$final += 1; // also set view
					}
				}
			$return[ $resId ] = $final;
			}
		$this->setProp( '_resource_schedules', $return );
		}

	function getProp( $pName, $unserialize = FALSE ){
		$return = parent::getProp( $pName, $unserialize );

		switch( $pName ){
			case '_calendar_field':
				if( isset($_COOKIE['nts_calendar_field']) )
				{
					$return = $_COOKIE['nts_calendar_field']; 
				}
				break;
			case '_resource_apps':
			case '_resource_schedules':
				if( ! is_array($return) )
					$return = array();
				foreach( $return as $resId => $accLevel ){
					if( ! $accLevel ){
						unset( $return[$resId] );
						}
					}
			break;
			}
		return $return;
		}

	function hasRole( $role )
	{
		if( ! is_array($role) )
			$role = array( $role );
		$myRoles = $this->getProp( '_role' );
		$myId = $this->getId();

		$return = array_intersect( $myRoles, $role ) ? TRUE : FALSE;

		/* automatically save new admins */
		if( 
			(! $return) && 
			( in_array('admin', $role) )
			)
		{
			$uif =& ntsUserIntegratorFactory::getInstance();
			$integrator =& $uif->getIntegrator();

			$is_admin = $integrator->isAdmin( $this );
			if( $is_admin )
			{
				$myRoles[] = 'admin';
				$this->setProp( '_role', $myRoles );

				$cm =& ntsCommandManager::getInstance();
				$cm->runCommand( $this, 'update' );
				$return = TRUE;
			}
		}
		return $return;
	}

	function getTimezone(){
		$return = $this->getProp('_timezone');
		if( $this->getId() == 0 ){
			if( isset($_SESSION['nts_timezone']) ){
				if( NTS_ENABLE_TIMEZONES > 0 )
					$return = $_SESSION['nts_timezone'];
				else
					unset( $_SESSION['nts_timezone'] );
				}
			}
		return $return;
		}

	function getPreference( $k )
	{
		$return = '';
		$all = $this->getPreferences();
		if( isset($all[$k]) )
		{
			$return = $all[$k];
		}
		return $return;
	}

	function getPreferences()
	{
		$return = '';
		if( $this->getId() > 0 )
		{
			$return = $this->getProp('_preferences');
		}
		else
		{
			if( isset($_SESSION['nts_preferences']) )
			{
				$return = $_SESSION['nts_preferences'];
			}
		}

		if( strlen($return) )
		{
			$return = unserialize( $return );
		}
		else
		{
			$return = array();
		}
		return $return;
	}

	function setPreference( $k, $v )
	{
		$array = $this->getPreferences();
		$array[ $k ] = $v;

		$set = serialize( $array );

		if( $this->getId() > 0 )
		{
			$return = $this->setProp('_preferences', $set);
			$cm =& ntsCommandManager::getInstance();
			$cm->runCommand( $this, 'update' );
		}
		else
		{
			$_SESSION['nts_preferences'] = $set;
		}
	}

	function getPanelPermissions(){
		$return = array();
		$apn =& ntsAdminPermissionsManager::getInstance();
		$allPanels = $apn->getPanels();

		$disabledPanels = $this->getProp( '_disabled_panels' );
		foreach( $allPanels as $p ){
			if( ! in_array($p, $disabledPanels) )
				$return[] = $p;
			}
		return $return;
		}

	function setDisabledPanel( $panel )
	{
		$this->_disabled_panels[] = $panel;
	}

	function getDisabledPanels()
	{
		$disabled_level = array(
			'admin'	=> array(),
			'staff'	=> array(
				'admin/company',
				'admin/payments',
				'admin/conf',
				'admin/forms',
				'admin/promo',
				'admin/customers/edit/admin',
				'admin/customers/edit/delete',
				'admin/sms',
				'admin/freshbooks',
				)
			);

		$level = $this->getProp( '_admin_level' );
		$disabledPanels = isset($disabled_level[$level]) ? $disabled_level[$level] : array();

		global $NTS_SKIP_PANELS;
		if( $NTS_SKIP_PANELS )
		{
			$disabledPanels = array_merge( $disabledPanels, $NTS_SKIP_PANELS );
		}

		$disabledPanels = array_merge( $disabledPanels, $this->_disabled_panels );
		return $disabledPanels;
	}

	function isPanelDisabled( $checkPanel )
	{
		$return = FALSE;
		$disabledPanels = $this->getDisabledPanels();
		reset( $disabledPanels );
		foreach( $disabledPanels as $dp )
		{
			if( substr($checkPanel, 0, strlen($dp)) == $dp ){
				// not allowed
				$return = TRUE;
				break;
				}
		}
		return $return;
	}

	function isPanelDisabled_old( $checkPanel ){
		$return = false;
		$disabledPanels = $this->getProp( '_disabled_panels' );

		global $NTS_SKIP_PANELS;
		if( $NTS_SKIP_PANELS ){
			$disabledPanels = array_merge( $disabledPanels, $NTS_SKIP_PANELS );
			}

		reset( $disabledPanels );
		foreach( $disabledPanels as $dp ){
			if( substr($checkPanel, 0, strlen($dp)) == $dp ){
				// not allowed
				$return = true;
				break;
				}
			}
		return $return;
		}
	}

class ntsUserIntegrator {
	var $usersById = array();
	var $usersByUsername = array();
	var $cacheCount = array();
	var $idIndex = 'id';
	var $db = null;
	var $plugins = array();

	function __construct()
	{
		$this->init();

	/* load plugins if any */
		$plm =& ntsPluginManager::getInstance();
		$activePlugins = $plm->getActivePlugins();
		$this->plugins = array();
		reset( $activePlugins );
		foreach( $activePlugins as $plg )
		{
			$checkFile = $plm->getPluginFolder( $plg ) . '/filterUsers.php';
			if( file_exists($checkFile) )
			{
				$this->plugins[] = $checkFile;
			}
		}
	}

	function getError(){
		return $this->error;
		}

	function setError( $err ){
		$this->error = $err;
		}

	function getUsers( $where = array(), $order = array(), $limit = '', $userStatus = '' ){
		$return = array();

	/* modifies $where */
		reset( $this->plugins );
		foreach( $this->plugins as $pf )
		{
			require( $pf );
		}

	/* user ids */
		$ids = $this->loadUsers( $where, $order, $limit, $userStatus );
		if(
			(count($where) == 1) && 
			isset($where['id']) && 
			($where['id'][0] == 'IN')
			)
		{
			ntsObjectFactory::preload( 'user', $where['id'][1] );
			reset( $ids );
			foreach( $ids as $id )
			{
				$u = new ntsUser;
				$u->setId( $id );
				if( ! $u->notFound() )
				{
					$return[] = $u->getByArray();
				}
			}
		}
		else
		{
			reset( $ids );
			foreach( $ids as $id )
			{
				$u = $this->getUserById( $id );
				if( isset($u['id']) )
				{
					$return[] = $u;
				}
			}
		}
		return $return;
		}

	/* cleans up object meta for integrated ones */
	function cleanUp()
	{
		return TRUE;
	}

	function countUsers( $where = array() ){
	/* modifies $where */
		reset( $this->plugins );
		foreach( $this->plugins as $pf )
		{
			require( $pf );
		}

		$cacheString = serialize( $where );

	/* ALREADY HAVE THIS? */
		if( isset($this->cacheCount[$cacheString]) ){
			$return = $this->cacheCount[$cacheString];
//			echo "<b>ON CACHE = $return</b><br>";
			}
		else {
//			echo "<b>NOT ON CACHE</b><br>";
			/* create a simple query to store count */
			$this->loadUsers( $where, array(), '1' );
			$return = $this->cacheCount[$cacheString];
			}
		return $return;
		}

	function loadUser( $id ){
		}

	function loadUsers( $where = array(), $order = array(), $limit = '', $userStatus = '' ){
		$return = array();

		if( isset($where[0]) ){
			$count = 0;
			reset( $where );
			foreach( $where as $wh ){
				$thisIds = $this->loadUsers( $wh, $order, $limit, $userStatus );
				$return = array_merge( $return, $thisIds );
				$thisWhereString = serialize( $wh ) . $userStatus;
				$thisCount = $this->cacheCount[ $thisWhereString ];
				if( $thisCount > $count )
					$count = $thisCount;
				}
			$return = array_unique( $return );

		/* cache count */
			$whereString = serialize( $where ) . $userStatus;
			$this->cacheCount[ $whereString ] = $count;
			return $return;
			}

	/* split where in builtin and custom part */
		list( $whereB, $whereC ) = $this->_splitWhere( $where );
		list( $orderB, $orderC ) = $this->_splitOrder( $order );

	/* check if we need to force user status search before */
		if( $userStatus && ($userStatus != '-any-') ){
			$whereC['_restriction'] = array('=', $userStatus);
			
//echo '<h2>AH</h2>';
//			_print_r( $whereB );
			}

	/* IF BOTH BUILTIN AND CUSTOM HERE */
		if( ($whereB || $orderB) && ($whereC || $orderC) ){
			if( $orderB && $orderC ){
			/* can't order by both builtin and meta */
				echo "sorry I can't order listings by both builtin and meta properties, skipping meta order<br>";
				$orderC = array();
				}

			if( $orderC ){
//				echo "<h3>BACA</h3>";
			/* first builtin then custom ordered */
				$limitB = '';

				$statusIds = isset($whereB[' id ']) ? $whereB[' id '] : '';
				$whereB = $this->convertTo( $whereB );
				if( $statusIds )
					$whereB[ ' ' . $this->idIndex . ' ' ] = array('=', $statusIds);

				list( $ids, $count ) = $this->queryUsers( $whereB, $orderB, $limitB );

				if( $ids ){
					$whereC['id'] = array('IN', $ids);
					list( $ids, $count ) = $this->queryUsersMeta( $whereC, $orderC, $limit, $userStatus );
					}
				}
			else {
//				echo "<h3>CABA</h3>";
			/* first custom then builtin ordered */
				$limitC = '';
				$noIds = array();
				$ids = array();

				if( isset($whereC['_role']) && ($whereC['_role'][0] == '=') && ($whereC['_role'][1] == 'customer') ){
					$whereC['_role'] = array('<>', 'customer');
					list( $noIds, $count ) = $this->queryUsersMeta( $whereC, $orderC, $limitC, $userStatus );
					unset( $whereC['_role'] );

					if( $whereC ){
						if( $ids ){
							$whereC['id'] = array('NOT IN', $ids);
							}
						list( $ids, $count ) = $this->queryUsersMeta( $whereC, $orderC, $limitC, $userStatus );
						}
					}
				else {
					list( $ids, $count ) = $this->queryUsersMeta( $whereC, $orderC, $limitC, $userStatus );
					}

				if( $ids || $noIds ){
					if( $ids ){
						if( isset($whereB['id']) )
							$whereB['id '] = $whereB['id'];
						$whereB['id'] = array('IN', $ids);

						$statusIds = isset($whereB[' id ']) ? $whereB[' id '] : '';
						$whereB = $this->convertTo( $whereB );
						if( $statusIds )
							$whereB[ ' ' . $this->idIndex . ' ' ] = $statusIds;
						}
					if( $noIds ){
						$whereB = $this->convertTo( $whereB );
						if( $noIds ){
							$whereB[' id  '] = array('NOT IN', $noIds);
							}
						}

					list( $ids, $count ) = $this->queryUsers( $whereB, $orderB, $limit );
					}
				}
			}

	/* ELSE CUSTOM ONLY */
		elseif( ($whereC || $orderC) ){
//			echo "CACA";

			if( isset($whereC['_role']) && ($whereC['_role'][0] == '=') && ($whereC['_role'][1] == 'customer') ){
				$whereC['_role'] = array('<>', 'customer');
				list( $noIds, $count ) = $this->queryUsersMeta( $whereC );
				unset( $whereC['_role'] );

				if( $whereC ){
					if( $noIds ){
						$whereC['id'] = array('NOT IN', $ids);
						}
					list( $ids, $count ) = $this->queryUsersMeta( $whereC, $orderC, $limitC, $userStatus );
					}

				if( $noIds ){
					$whereB = $this->convertTo( $whereB );
					if( $noIds ){
						$whereB[' id  '] = array('NOT IN', $noIds);
						}
					}
				list( $ids, $count ) = $this->queryUsers( $whereB, $orderB, $limit );
				}
			else {
				list( $ids, $count ) = $this->queryUsersMeta( $whereC, $orderC, $limit, $userStatus );
				}
			}

	/* ELSE IF BUILTIN ONLY, OR ALL */
		elseif( ($whereB || $orderB) || 1 ){
//			echo "<h3>BABA</h3>";
			$statusIds = isset($whereB[' id ']) ? $whereB[' id '] : '';
			$whereB = $this->convertTo( $whereB );
			if( $statusIds )
				$whereB[ ' ' . $this->idIndex . ' ' ] = $statusIds;
			list( $ids, $count ) = $this->queryUsers( $whereB, $orderB, $limit, $userStatus );
			}

	/* cache count */
		$whereString = serialize( $where ) . $userStatus;
		$this->cacheCount[ $whereString ] = $count;

		return $ids;
		}

	function loadUserMeta( $userId ){
		global $NTS_OBJECT_PROPS_CONFIG;

		$mainDb =& dbWrapper::getInstance();
		$return = array();
		$metaClass = "user";

		$sql =<<<EOT
SELECT 
	meta_name, meta_value, meta_data
FROM 
	{PRFX}objectmeta 
WHERE
	obj_id = $userId AND obj_class = "$metaClass"
EOT;

		$result = $mainDb->runQuery( $sql );
		if( $result ){
			while( $n = $result->fetch() ){
				if( isset($return[$n['meta_name']]) ){
					if( isset($NTS_OBJECT_PROPS_CONFIG[$metaClass][$n['meta_name']]) && $NTS_OBJECT_PROPS_CONFIG[$metaClass][$n['meta_name']]['isArray'] ){
						if( ! is_array($return[$n['meta_name']]) )
							$return[$n['meta_name']] = array( $return[$n['meta_name']] );
						if( isset($n['meta_data']) && strlen($n['meta_data']) )
							$return[$n['meta_name']][ $n['meta_value'] ] = $n['meta_data'];
						else {
							if( ! in_array($n['meta_value'], $return[$n['meta_name']] ) ) 
								$return[$n['meta_name']][] = $n['meta_value'];
							}
						}
					}
				else {
					if( isset($n['meta_data']) && strlen($n['meta_data']) )
						$return[ $n['meta_name'] ] = array( $n['meta_value'] => $n['meta_data'] );
					else
						$return[ $n['meta_name'] ] = $n['meta_value'];
					}
				}
			}

		return $return;
		}

	function queryUsersMeta( $whereC = array(), $orderC = array(), $limit = '', $userStatus = '' ){
		$ids = array();

		$limitString = ( $limit ) ? 'LIMIT ' . $limit : '';

		$alsoId = '';
		if( isset($whereC['id']) ){
			$alsoId = $whereC['id'];
			unset( $whereC['id'] );
			}

		$orderString = '';
		if( $orderC ){
			$orderQueries = array();
			reset( $orderC );
			foreach( $orderC as $oa ){
				$k = $oa[0];
				$v = isset($oa[1]) ? $oa[1] : 'DESC';
				$q =<<<EOT
(
SELECT
	meta_value
FROM
	{PRFX}objectmeta AS tmeta
WHERE
	tmeta.obj_class = {PRFX}objectmeta.obj_class AND 
	tmeta.obj_id = {PRFX}objectmeta.obj_id AND 
	meta_name = "$k"
) $v
EOT;
				$orderQueries[] = $q;
				}
			$orderString = 'ORDER BY ' . join( ",\n", $orderQueries );
			}

	/* init in case there're no conditions */
		$tempWhere = array(
			'obj_class' => array('=', 'user'),
			);

		$tempWhereString = 'WHERE ' . $this->db->buildWhere($tempWhere);
		$finalQuery =<<<EOT
SELECT 
	DISTINCT(obj_id)
FROM 
	{PRFX}objectmeta 
$tempWhereString
$orderString
$limitString
EOT;

		$countQuery =<<<EOT
SELECT 
	COUNT(obj_id) AS count
FROM 
	{PRFX}objectmeta 
$tempWhereString
EOT;

	/* process conditions */
		$stackCount = 0;
		$stackIn = array();
		reset( $whereC );
		foreach( $whereC as $k => $v ){
			$tempWhere = array(
				'obj_class'		=> array('=', 'user'),
				'meta_name'		=> array('=', $k),
				'meta_value'	=> $v,
				);
			if( $stackCount ){
				$tempWhere[ 'obj_id' ] = array('IN', '(' . $stackIn[$stackCount-1] . ')', 1);
				}
			$tempWhereString = 'WHERE ' . $this->db->buildWhere($tempWhere);

		/* last one */
			$stackCount++;
			if( $stackCount == count($whereC) ){
				if( $alsoId ){
//					echo "<h3>also = '$alsoId'</h3>";
					$tempWhere[ 'obj_id ' ] = $alsoId;
					}

				$tempWhereString = 'WHERE ' . $this->db->buildWhere($tempWhere);
				$finalQuery =<<<EOT
SELECT 
	DISTINCT(obj_id)
FROM 
	{PRFX}objectmeta 
$tempWhereString
$orderString
$limitString
EOT;

				$countQuery =<<<EOT
SELECT 
	COUNT(obj_id) AS count
FROM 
	{PRFX}objectmeta 
$tempWhereString
EOT;
				}
			else {
				$stackIn[] =<<<EOT
SELECT 
	DISTINCT(obj_id)
FROM 
	{PRFX}objectmeta 
$tempWhereString
EOT;
				}
			}

		$mainDb =& dbWrapper::getInstance();
		$result = $mainDb->runQuery( $finalQuery );
		if( $result ){
			while( $u = $result->fetch() ){
				$ids[] = $u['obj_id'];
	 			}
			}

	/* count */
		$result = $mainDb->runQuery( $countQuery );
		if( $result ){
			$u = $result->fetch();
			$count = $u['count'];
			}
		else {
			$count = 0;
			}

		return array( $ids, $count );
		}

/* reloaded methods */
	function init(){
		$this->db =& dbWrapper::getInstance();
		}

	// this function adapts user information to common form.
	// user info array should have: 'id', 'username', 'first_name', 'last_name', 'created'
	function convertFrom( $userInfo ){
		$return = $userInfo;
		// built-in, no conversion required
		return $return;
		}

	function convertTo( $userInfo ){
		$return = $userInfo;
		// built-in, no conversion required
		
	/* password */
		if( isset($userInfo['new_password']) ){
			$return['password'] = md5( $userInfo['new_password'] );
			unset( $return['new_password'] );
			}

		return $return;
		}
/* end of reloaded methods */

/* internal methods */
	function buildOrder( $order ){
		$parts = array();
		reset( $order );
		foreach( $order as $oa ){
			$how = isset($oa[1]) ? $oa[1] : 'DESC';
			$parts[] = $oa[0] . ' ' . $how;
			}
		$orderString = join( ', ', $parts );
		return $orderString;
		}

	function getUserById( $userId ){
		if( $userId <= 0 ){
			$return = array(
				'id'		=> 0,
				);
			return $return;
			}

		if( ! isset($this->usersById[$userId]) ){
			$userInfo = $this->loadUser( $userId );
			if( $userInfo ){
				$userInfo = $this->convertFrom( $userInfo );
				$metaInfo = $this->loadUserMeta( $userId );
				$userInfo = array_merge( $userInfo, $metaInfo );
				$this->usersById[$userId] = $userInfo;
				}
			else {
				$this->usersById[$userId] = array();
				}
			}

		return $this->usersById[$userId];
		}

	function getUserByUsername( $userName ){
		if( ! isset($this->usersByUsername[$userName]) ){
			$userInfo = array();
			$users = $this->getUsers( 
				array(
					'username' => array('=', $userName),
					)
				);

			if( $users )
				$userInfo = $users[0];
			$this->usersByUsername[$userName] = $userInfo;
			}

		return $this->usersByUsername[$userName];
		}

	function getUserByEmail( $userEmail ){
		$return = array();
		$users = $this->getUsers( 
			array(
				'email' => array('=', $userEmail),
				)
			);

		if( $users )
			$return = $users[0];
		return $return;
		}

	function _splitWhere( $where ){
		$om =& objectMapper::getInstance();
		list( $coreProps, $metaProps ) = $om->getPropsForClass( 'user' );
		$builtinFields = array_keys( $coreProps );
		$builtinFields[] = 'id';

		$ri = ntsLib::remoteIntegration();
		if( $ri == 'wordpress' ){
			$builtinFields[] = 'user_nicename';
			$builtinFields[] = 'display_name';
		}

	/* split where in builtin and custom part */
		$whereB = array();
		$whereC = array();

		reset( $where );
		foreach( $where as $k => $v ){
			if( in_array(trim($k), $builtinFields) ){
				$whereB[ $k ] = $v;
				}
			else{
				$whereC[ $k ] = $v;
				}
			}
		$return = array( $whereB, $whereC );
		return $return;
		}

	function _splitOrder( $order ){
		$om =& objectMapper::getInstance();
		list( $coreProps, $metaProps ) = $om->getPropsForClass( 'user' );
		$builtinFields = array_keys( $coreProps );

		$ri = ntsLib::remoteIntegration();
		if( $ri == 'wordpress' ){
			$builtinFields[] = 'user_nicename';
			$builtinFields[] = 'display_name';
		}

	/* split where in builtin and custom part */
		$orderB = array();
		$orderC = array();

		reset( $order );
		foreach( $order as $oa ){
			if( in_array($oa[0], $builtinFields) ){
				$orderB[] = $oa;
				}
			else {
				$orderC[] = $oa;
				}
			}
		$return = array( $orderB, $orderC );
		return $return;
		}

	function getAdmins()
	{
		$return = array();
		$cm =& ntsCommandManager::getInstance();
		$ids = $this->getAdminIds();

		$ri = ntsLib::remoteIntegration();
		if( $ri )
		{
			/* also add those that are inside our database */
			$where = array(
				'_role'	=> array('=', 'admin')
				);
			list( $my_ids, $count ) = $this->queryUsersMeta( $where );
			$ids = array_merge( $ids, $my_ids );
			$ids = array_unique( $ids );
		}

		$where = array();
		$where['id'] = array( 'IN', $ids );

		if( ! NTS_EMAIL_AS_USERNAME )
		{
			$order = array(
				array( 'username', 'ASC' ),
				);
		}
		else
		{
			$order = array(
				array( 'email', 'ASC' ),
				);
		}

		$users = $this->getUsers(
			$where,
			$order
			);

		reset( $users );
		foreach( $users as $ua )
		{
			$u = new ntsUser;
			$u->setId( $ua['id'] );
			$my_roles = $u->getProp( '_role' );
			if( ! in_array('admin', $my_roles) )
			{
				$my_roles[] = 'admin';
				$u->setProp( '_role', $my_roles );
				$cm->runCommand( $u, 'update' );
			}
			$return[] = $u;
		}
		return $return;
	}
	}

class ntsUserIntegratorBuiltin extends ntsUserIntegrator {
	var $_users_table_exists = NULL;

	function __construct()
	{
		parent::__construct();
//		$this->init();

		if( $_SERVER['SERVER_NAME'] == 'localhost' )
		{
			if( $this->_users_table_exists === NULL )
			{
				$res = $this->db->runQuery( "SHOW TABLES LIKE '{PRFX}users'" );
				if( $res && $i = $res->fetch() )
				{
					$this->_users_table_exists = TRUE;
				}
				else
				{
					$this->_users_table_exists = FALSE;
				}
			}
		}
		else
		{
			$this->_users_table_exists = TRUE;
		}
	}

	function dumpUsers()
	{
		$return = array();
		$table = 'users';
		if( $this->db->tableExists($table) )
			return $this->db->dumpTable( $table, true );
		else
			return $return;
	}

/* DATABASE FUNCTIONS */
	function queryUsers( $whereB = array(), $orderB = array(), $limit = '' ){
		$ids = array();

		$whereString = ( $whereB ) ? 'WHERE ' . $this->db->buildWhere($whereB) : '';
		$orderString = ( $orderB ) ? 'ORDER BY ' . $this->buildOrder($orderB) : '';
		$limitString = ( $limit ) ? 'LIMIT ' . $limit : '';

	/* ids */
		$sql =<<<EOT
SELECT 
	id 
FROM 
	{PRFX}users 
$whereString 
$orderString 
$limitString 
EOT;

		$result = $this->db->runQuery( $sql );
		if( $result ){
			while( $u = $result->fetch() ){
				$ids[] = $u['id'];
	 			}
			}

	/* count */
		$sql =<<<EOT
SELECT 
	COUNT(id) AS count
FROM 
	{PRFX}users 
$whereString 
EOT;

		$result = $this->db->runQuery( $sql );
		if( $result ){
			$u = $result->fetch();
			$count = $u['count'];
			}
		else {
			$count = 0;
			}

		return array( $ids, $count );
		}

	function loadUser( $userId )
	{
		$return = array();
		if( ! $this->_users_table_exists )
			return $return;

		if( ! is_array($userId) )
		{
			$where = array(
				'id'	=> array('=', $userId),
				);

			$result = $this->db->select( '*', 'users', $where );
			if( $result )
			{
				if( $n = $result->fetch() )
				{
					$return = $n;
				}
			}
		}
		else
		{
			$splitBy = 100;
			$splitSteps = ceil( count($userId) / $splitBy );
			for( $s = 0; $s < $splitSteps; $s++ )
			{ 
				$these_ids = array_slice($userId, $s * $splitBy, $splitBy);
				$where = array(
					'id'	=> array('IN', $these_ids),
					);
				$result = $this->db->select( '*', 'users', $where );
				if( $result )
				{
					while( $n = $result->fetch() )
					{
						$return[] = $n;
					}
				}
			}
		}
		return $return;
	}

	function updateUser( $id, $info ){
		if( ! $info )
			return true;

		$info = $this->convertTo( $info );
		$result = $this->db->update( 
			'users',
			$info,
			array(
				'id' => array('=', $id)
				)
			);

		if( ! $result ){
			$this->setError( $this->db->getError() );
			}
		return $result;
		}

	function createUser( $info, $netaInfo = array() ){
		$info = $this->convertTo( $info );
		$result = $this->db->insert( 'users', $info );

		if( $result ){
			$newId = $this->db->getInsertId();
			}
		else {
			$newId = 0;
			$this->setError( $this->db->getError() );
			}
		return $newId;
		}

	function deleteUser( $id ){
		$sql =<<<EOT
DELETE FROM {PRFX}users
WHERE id = $id
EOT;

		$result = $this->db->runQuery( $sql );
		return $result;
		}

/* AUTHENTICATION FUNCTIONS */
	function checkPassword( $username, $password )
	{
		$return = FALSE;
		if( NTS_EMAIL_AS_USERNAME )
			$userInfo = $this->getUserByEmail( $username );
		else
			$userInfo = $this->getUserByUsername( $username );

		if( $userInfo )
		{
			$storedHash = $userInfo['password'];
			if( substr($storedHash, 0, strlen('$P$')) == '$P$' )
			{
//				include_once( dirname(__FILE__) . '/ntsPasswordHash.php' );
				$t_hasher = new ntsPasswordHash(8, TRUE);
				$return = $t_hasher->CheckPassword($password, $storedHash);
			}
			else
			{
				$myHash = md5($password);
				if( $myHash == $storedHash )
				{
					$return = 1;
				}
			}
		}
		return $return;
	}

	function currentUserId(){
		$return = 0;
		if( isset($_SESSION['userid']) )
		{
			$return = $_SESSION['userid'];
		}
		elseif( isset($_COOKIE[NTS_USER_LOGIN_HASH]) )
		{
			$hash = $_COOKIE[NTS_USER_LOGIN_HASH];
		/* find user with this hash */
			$where = array(
				'obj_class'		=> array('=', 'user'),
				'meta_name'		=> array('=', '_login_hash'),
				'meta_value'	=> array('=', $hash),
				);

			$result = $this->db->select( 'obj_id', 'objectmeta', $where );
			if( $result )
			{
				$e = $result->fetch();
				if( isset($e['obj_id']) )
				{
					$_SESSION['userid'] = $e['obj_id'];
					$return = $_SESSION['userid'];
				}
			}
		}
		return $return;
		}

	function login( $userId, $userPass = '', $remember = FALSE )
	{
		$userInfo = $this->getUserById( $userId );
		if( $userInfo )
		{
			$_SESSION['userid'] = $userId;
			if( $remember )
			{
				$rand = ntsLib::generateRand(12);
				$loginCookieHash = md5(sha1($userId . $rand));
				setcookie( NTS_USER_LOGIN_HASH, $loginCookieHash, time() + 365*24*60*60 );
				$user = new ntsUser;
				$user->setId( $userId );
				$user->setProp( '_login_hash', $loginCookieHash );

				$cm =& ntsCommandManager::getInstance();
				$cm->runCommand( $user, 'update' );
			}
		}
	}

	function logout(){
		$userId = $_SESSION['userid'];
	
		unset( $_SESSION['userid'] );
		setcookie( NTS_USER_LOGIN_HASH, '', time() - 365*24*60*60 );

		$user = new ntsUser;
		$user->setId( $userId );
		$user->setProp( '_login_hash', '' );

		$cm =& ntsCommandManager::getInstance();
		$cm->runCommand( $user, 'update' );
		}

	function isAdmin( $user )
	{
		$check = array( 'admin' );
		$myRoles = $user->getProp( '_role' );
		$return = array_intersect( $myRoles, $check ) ? TRUE : FALSE;
		return $return;
	}

	function getAdminIds()
	{
		$return = array();
		$where = array(
			'_role'	=> array('=', 'admin')
			);
		list( $ids, $count ) = $this->queryUsersMeta( $where );
		return $ids;
	}

/* END OF SPECIFIC METHODS */
	}

class ntsUserIntegratorFactory
{
	var $integrator;

	function __construct()
	{
		$ri = ntsLib::remoteIntegration();
		if( $ri )
		{
			$authIntegratorFile = $ri . '/' . $ri . '.php';
			$authIntegratorClass = $ri . 'Integrator';
			include_once( NTS_APP_DIR . '/integration/' . $authIntegratorFile );
			$this->integrator = new $authIntegratorClass;
		}
		else
		{
			$this->integrator = new ntsUserIntegratorBuiltin;
		}
	}

	function &getIntegrator()
	{
		return $this->integrator;
	}

	// Singleton stuff
	static function &getInstance()
	{
		return ntsLib::singletonFunction( 'ntsUserIntegratorFactory' );
	}
}

class ntsAdminPermissionsManager
{
	var $keys = array();

	function ntsAdminPermissionsManager(){
		$this->keys = array(
			array( 'admin/customers', M('Customers'), 1 ),
				array( 'admin/customers/browse',	M('View') ),
				array( 'admin/customers/edit', 		M('Edit') ),
				array( 'admin/customers/create',	M('Create') ),
				array( 'admin/customers/notified',	M('Get Notified') ),
				array( 'admin/customers/import',	M('Import') ),

			array( 'admin/company/resources', M('Bookable Resources'), 1 ),
				array( 'admin/company/resources/browse',	M('View') ),
				array( 'admin/company/resources/edit', 		M('Edit') ),
				array( 'admin/company/resources/create',	M('Create') ),

			array( 'admin/company/services', M('Services'), 1 ),
				array( 'admin/company/services/browse',	M('View') ),
				array( 'admin/company/services/edit',	M('Edit') ),
				array( 'admin/company/services/create',	M('Create') ),
				array( 'admin/company/services/cats',	M('Categories') ),
				array( 'admin/company/services/packs',	M('Packages') ),
				array( 'admin/company/services/promotions', M('Promotions') ),
				array( 'admin/company/services/addons', M('Add-ons') ),

			array( 'admin/company/locations', M('Locations'), 1 ),
				array( 'admin/company/locations/browse',	M('View') ),
				array( 'admin/company/locations/edit', 		M('Edit') ),
				array( 'admin/company/locations/create',	M('Create') ),
				array( 'admin/company/locations/travel',	M('Travel Time') ),

			array( 'admin/company/staff', M('Administrative Users'), 1 ),
				array( 'admin/company/staff/browse',	M('View') ),
				array( 'admin/company/staff/edit', 		M('Edit') ),
				array( 'admin/company/staff/create',	M('Create') ),

			array( 'admin/payments', M('Payments'), 1 ),
				array( 'admin/payments/invoices',		M('Invoices') ),
				array( 'admin/payments/transactions', 	M('Transactions') ),
				array( 'admin/payments/orders', 		M('Package Orders') ),

			array( 'admin/forms', M('Forms'), 1 ),
				array( 'admin/forms/customers', M('Customers') ),
				array( 'admin/forms/appointments', M('Appointments') ),

			array( 'admin', M('Synchronization'), 1 ),
			array( 'admin/sync', M('Synchronization') ),

			array( 'admin/promo', M('Promotion'), 1 ),
				array( 'admin/promo/newsletter', M('Newsletter') ),

			array( 'admin/conf', M('Settings'), 1 ),
				array( 'admin/conf/customers', M('Customers') ),
				array( 'admin/conf/email_settings', M('Email') ),
				array( 'admin/conf/email_templates', M('Notifications') ),
				array( 'admin/conf/reminders', M('Reminders') ),
				array( 'admin/conf/cron', M('Automatic Actions') ),
				array( 'admin/conf/datetime', M('Date and Time') ),
				array( 'admin/conf/currency', M('Currency') ),
				array( 'admin/conf/payment_gateways', M('Payment Gateways') ),
				array( 'admin/conf/languages', M('Languages') ),
				array( 'admin/conf/flow', M('Appointment Flow') ),
				array( 'admin/conf/plugins', M('Plugins') ),
				array( 'admin/conf/misc', M('Misc') ),
				array( 'admin/conf/upgrade', M('Info') ),
				array( 'admin/conf/backup', M('Backup') ),
			);

	/* add panels from plugins */
		$plm =& ntsPluginManager::getInstance();
		$activePlugins = $plm->getActivePlugins();
		reset( $activePlugins );
		foreach( $activePlugins as $plg ){
			$panels = $plm->getPanels( $plg );
			reset( $panels );
			foreach( $panels as $p ){
				$this->keys[] = $p;
				}
			}
		}

	function getPanels(){
		$return = array();
		reset( $this->keys );
		foreach( $this->keys as $ka ){
			if( isset($ka[2]) && $ka[2] )
				continue;
			$return[] = $ka[0];
			}
		return $return;
		}

	function getPanelsDetailed(){
		return $this->keys;
		}

	// Singleton stuff
	static function &getInstance(){
		return ntsLib::singletonFunction( 'ntsAdminPermissionsManager' );
		}
}

/**
 * Portable PHP password hashing framework.
 * @package phpass
 * @since 2.5
 * @version 0.3 / WordPress
 * @link http://www.openwall.com/phpass/
 */

#
# Written by Solar Designer <solar at openwall.com> in 2004-2006 and placed in
# the public domain.  Revised in subsequent years, still public domain.
#
# There's absolutely no warranty.
#
# Please be sure to update the Version line if you edit this file in any way.
# It is suggested that you leave the main version number intact, but indicate
# your project name (after the slash) and add your own revision information.
#
# Please do not change the "private" password hashing method implemented in
# here, thereby making your hashes incompatible.  However, if you must, please
# change the hash type identifier (the "$P$") to something different.
#
# Obviously, since this code is in the public domain, the above are not
# requirements (there can be none), but merely suggestions.
#

/**
 * Portable PHP password hashing framework.
 *
 * @package phpass
 * @version 0.3 / WordPress
 * @link http://www.openwall.com/phpass/
 * @since 2.5
 */
class ntsPasswordHash {
	var $itoa64;
	var $iteration_count_log2;
	var $portable_hashes;
	var $random_state;

	function ntsPasswordHash($iteration_count_log2, $portable_hashes)
	{
		$this->itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

		if ($iteration_count_log2 < 4 || $iteration_count_log2 > 31)
			$iteration_count_log2 = 8;
		$this->iteration_count_log2 = $iteration_count_log2;

		$this->portable_hashes = $portable_hashes;

		$this->random_state = microtime() . uniqid(rand(), TRUE); // removed getmypid() for compatibility reasons
	}

	function get_random_bytes($count)
	{
		$output = '';
		if ( @is_readable('/dev/urandom') &&
		    ($fh = @fopen('/dev/urandom', 'rb'))) {
			$output = fread($fh, $count);
			fclose($fh);
		}

		if (strlen($output) < $count) {
			$output = '';
			for ($i = 0; $i < $count; $i += 16) {
				$this->random_state =
				    md5(microtime() . $this->random_state);
				$output .=
				    pack('H*', md5($this->random_state));
			}
			$output = substr($output, 0, $count);
		}

		return $output;
	}

	function encode64($input, $count)
	{
		$output = '';
		$i = 0;
		do {
			$value = ord($input[$i++]);
			$output .= $this->itoa64[$value & 0x3f];
			if ($i < $count)
				$value |= ord($input[$i]) << 8;
			$output .= $this->itoa64[($value >> 6) & 0x3f];
			if ($i++ >= $count)
				break;
			if ($i < $count)
				$value |= ord($input[$i]) << 16;
			$output .= $this->itoa64[($value >> 12) & 0x3f];
			if ($i++ >= $count)
				break;
			$output .= $this->itoa64[($value >> 18) & 0x3f];
		} while ($i < $count);

		return $output;
	}

	function gensalt_private($input)
	{
		$output = '$P$';
		$output .= $this->itoa64[min($this->iteration_count_log2 +
			((PHP_VERSION >= '5') ? 5 : 3), 30)];
		$output .= $this->encode64($input, 6);

		return $output;
	}

	function crypt_private($password, $setting)
	{
		$output = '*0';
		if (substr($setting, 0, 2) == $output)
			$output = '*1';

		$id = substr($setting, 0, 3);
		# We use "$P$", phpBB3 uses "$H$" for the same thing
		if ($id != '$P$' && $id != '$H$')
			return $output;

		$count_log2 = strpos($this->itoa64, $setting[3]);
		if ($count_log2 < 7 || $count_log2 > 30)
			return $output;

		$count = 1 << $count_log2;

		$salt = substr($setting, 4, 8);
		if (strlen($salt) != 8)
			return $output;

		# We're kind of forced to use MD5 here since it's the only
		# cryptographic primitive available in all versions of PHP
		# currently in use.  To implement our own low-level crypto
		# in PHP would result in much worse performance and
		# consequently in lower iteration counts and hashes that are
		# quicker to crack (by non-PHP code).
		if (PHP_VERSION >= '5') {
			$hash = md5($salt . $password, TRUE);
			do {
				$hash = md5($hash . $password, TRUE);
			} while (--$count);
		} else {
			$hash = pack('H*', md5($salt . $password));
			do {
				$hash = pack('H*', md5($hash . $password));
			} while (--$count);
		}

		$output = substr($setting, 0, 12);
		$output .= $this->encode64($hash, 16);

		return $output;
	}

	function gensalt_extended($input)
	{
		$count_log2 = min($this->iteration_count_log2 + 8, 24);
		# This should be odd to not reveal weak DES keys, and the
		# maximum valid value is (2**24 - 1) which is odd anyway.
		$count = (1 << $count_log2) - 1;

		$output = '_';
		$output .= $this->itoa64[$count & 0x3f];
		$output .= $this->itoa64[($count >> 6) & 0x3f];
		$output .= $this->itoa64[($count >> 12) & 0x3f];
		$output .= $this->itoa64[($count >> 18) & 0x3f];

		$output .= $this->encode64($input, 3);

		return $output;
	}

	function gensalt_blowfish($input)
	{
		# This one needs to use a different order of characters and a
		# different encoding scheme from the one in encode64() above.
		# We care because the last character in our encoded string will
		# only represent 2 bits.  While two known implementations of
		# bcrypt will happily accept and correct a salt string which
		# has the 4 unused bits set to non-zero, we do not want to take
		# chances and we also do not want to waste an additional byte
		# of entropy.
		$itoa64 = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

		$output = '$2a$';
		$output .= chr(ord('0') + $this->iteration_count_log2 / 10);
		$output .= chr(ord('0') + $this->iteration_count_log2 % 10);
		$output .= '$';

		$i = 0;
		do {
			$c1 = ord($input[$i++]);
			$output .= $itoa64[$c1 >> 2];
			$c1 = ($c1 & 0x03) << 4;
			if ($i >= 16) {
				$output .= $itoa64[$c1];
				break;
			}

			$c2 = ord($input[$i++]);
			$c1 |= $c2 >> 4;
			$output .= $itoa64[$c1];
			$c1 = ($c2 & 0x0f) << 2;

			$c2 = ord($input[$i++]);
			$c1 |= $c2 >> 6;
			$output .= $itoa64[$c1];
			$output .= $itoa64[$c2 & 0x3f];
		} while (1);

		return $output;
	}

	function HashPassword($password)
	{
		$random = '';

		if (CRYPT_BLOWFISH == 1 && !$this->portable_hashes) {
			$random = $this->get_random_bytes(16);
			$hash =
			    crypt($password, $this->gensalt_blowfish($random));
			if (strlen($hash) == 60)
				return $hash;
		}

		if (CRYPT_EXT_DES == 1 && !$this->portable_hashes) {
			if (strlen($random) < 3)
				$random = $this->get_random_bytes(3);
			$hash =
			    crypt($password, $this->gensalt_extended($random));
			if (strlen($hash) == 20)
				return $hash;
		}

		if (strlen($random) < 6)
			$random = $this->get_random_bytes(6);
		$hash =
		    $this->crypt_private($password,
		    $this->gensalt_private($random));
		if (strlen($hash) == 34)
			return $hash;

		# Returning '*' on error is safe here, but would _not_ be safe
		# in a crypt(3)-like function used _both_ for generating new
		# hashes and for validating passwords against existing hashes.
		return '*';
	}

	function CheckPassword($password, $stored_hash)
	{
		$hash = $this->crypt_private($password, $stored_hash);
		if ($hash[0] == '*')
			$hash = crypt($password, $stored_hash);

		return $hash === $stored_hash;
	}
}
