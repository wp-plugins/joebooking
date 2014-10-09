<?php
global $NTS_OBJECT_CACHE, $NTS_OBJECT_PROPS_CONFIG, $NTS_ALL_IDS;

class ntsObjectFactory {
	static function clearCache( $className, $id = 0 ){
		global $NTS_OBJECT_CACHE, $NTS_ALL_IDS;
		if( $id )
			unset( $NTS_OBJECT_CACHE[$className][$id] );
		else {
			if( isset($NTS_OBJECT_CACHE[$className]) ){
				reset( $NTS_OBJECT_CACHE[$className] );
				$keys = array_keys($NTS_OBJECT_CACHE[$className]);
				foreach( $keys as $k ){
					unset( $NTS_OBJECT_CACHE[$className][$k] );
					}
				}
			if( isset($NTS_ALL_IDS[$className]) ){
				unset( $NTS_ALL_IDS[$className] );
				}
			}
		}

	static function preloadMeta( $className, $ids = array() ){
		global $NTS_OBJECT_PROPS_CONFIG;
		if( ! isset($NTS_OBJECT_PROPS_CONFIG[$className]) ){
			$om =& objectMapper::getInstance();
			$om->initPropsConfig( $className );
			}

		$ntsdb =& dbWrapper::getInstance();
		$return = array();
		$metaClass = $className;

		$splitBy = 100;
		$splitSteps = ceil( count($ids) / $splitBy );
		for( $s = 0; $s < $splitSteps; $s++ ){
			$idsString = join( ',', array_slice($ids, $s * $splitBy, $splitBy) );
			$sql =<<<EOT
SELECT 
		meta_name, meta_value, meta_data, obj_id
FROM 
		{PRFX}objectmeta 
WHERE
		obj_class = "$metaClass" AND obj_id IN ($idsString)
EOT;

			$result = $ntsdb->runQuery( $sql );
			if( $result ){
				while( $n = $result->fetch() ){
					$n['meta_data'] = trim( $n['meta_data'] );

					if( isset($return[$n['obj_id']][$n['meta_name']]) ){
						if( isset($NTS_OBJECT_PROPS_CONFIG[$metaClass][$n['meta_name']]) && $NTS_OBJECT_PROPS_CONFIG[$metaClass][$n['meta_name']]['isArray'] ){
							if( ! is_array($return[$n['obj_id']][$n['meta_name']]) )
								$return[$n['obj_id']][$n['meta_name']] = array( $return[$n['obj_id']][$n['meta_name']] );

							if( $NTS_OBJECT_PROPS_CONFIG[$metaClass][$n['meta_name']]['isArray'] == 3 ){
								$return[$n['obj_id']][$n['meta_name']][] = array($n['meta_value'], $n['meta_data'] );
								}
							elseif( strlen($n['meta_data']) && ($NTS_OBJECT_PROPS_CONFIG[$metaClass][$n['meta_name']]['isArray'] == 2) ){
								$return[$n['obj_id']][$n['meta_name']][ $n['meta_value'] ] = $n['meta_data'];
								}
							else {
								if( ! in_array($n['meta_value'], $return[$n['obj_id']][$n['meta_name']] ) ) 
									$return[$n['obj_id']][$n['meta_name']][] = $n['meta_value'];
								}
							}
						}
					else {
						if( isset($NTS_OBJECT_PROPS_CONFIG[$metaClass][$n['meta_name']]) && ($NTS_OBJECT_PROPS_CONFIG[$metaClass][$n['meta_name']]['isArray'] == 3) ){
							$return[$n['obj_id']][$n['meta_name']] = array( array($n['meta_value'], $n['meta_data']) );
							}
						elseif( (isset($NTS_OBJECT_PROPS_CONFIG[$metaClass][$n['meta_name']]) && ($NTS_OBJECT_PROPS_CONFIG[$metaClass][$n['meta_name']]['isArray'] == 2) ) ){
							$return[$n['obj_id']][ $n['meta_name'] ] = array( $n['meta_value'] => $n['meta_data'] );
							}
						else {
							$return[$n['obj_id']][ $n['meta_name'] ] = $n['meta_value'];
							}
						}
					}
				}
			}
		return $return;
		}

	static function setOnCache( $className, $id, $what )
	{
		global $NTS_OBJECT_CACHE;
		$NTS_OBJECT_CACHE[$className][ $id ] = $what;
	}

	static function preload( $className, $ids = array() ){
		global $NTS_OBJECT_CACHE, $NTS_OBJECT_PROPS_CONFIG;

		$oldIds = $ids;
		$ids = array();
		foreach( $oldIds as $id ){
			if( ! isset($NTS_OBJECT_CACHE[$className][$id]) ){
				$ids[] = $id;
				}
			}
		if( ! $ids )
			return;

		$metaInfo = ntsObjectFactory::preloadMeta( $className, $ids );

		switch( $className ){
			case 'user':
				$uif =& ntsUserIntegratorFactory::getInstance();
				$integrator =& $uif->getIntegrator();
				$usersInfo = $integrator->loadUser( $ids );
				reset( $usersInfo );
				foreach( $usersInfo as $u ){
					if( isset( $metaInfo[$u['id']] ) ){
						$u = array_merge( $u, $metaInfo[$u['id']] );
						}
					ntsObjectFactory::setOnCache( $className, $u['id'], $u );
					}
				break;

			default:
				$ntsdb =& dbWrapper::getInstance();
				$om =& objectMapper::getInstance();
				$tblName = $om->getTableForClass( $className );

				$splitBy = 100;
				$splitSteps = ceil( count($ids) / $splitBy );
				for( $s = 0; $s < $splitSteps; $s++ ){
					$thisIds = array_slice($ids, $s * $splitBy, $splitBy);
					$where = array(
						'id'	=> array('IN', $thisIds)
						);
					$result = $ntsdb->select( '*', $tblName, $where );
					while( $u = $result->fetch() ){
						if( isset( $metaInfo[$u['id']] ) ){
							$u = array_merge( $u, $metaInfo[$u['id']] );
							}
						ntsObjectFactory::setOnCache( $className, $u['id'], $u );
						}
					}

				if( $className == 'invoice' )
				{
					$pm =& ntsPaymentManager::getInstance();
					$pm->preloadInvoiceItems( $ids );
				}
				break;
			}
		}

	static function getIds( $className, $where = array() )
	{
		$return = array();
		$ntsdb =& dbWrapper::getInstance();
		$om =& objectMapper::getInstance();
		$tblName = $om->getTableForClass( $className );

		$result = $ntsdb->select( 
			'id',
			$tblName,
			$where
			);
		if( $result )
		{
			while( $u = $result->fetch() )
			{
				$return[] = $u['id'];
			}
		}
		return $return;
	}

	static function getAllIds( $className, $addonString = '' )
	{
		global $NTS_ALL_IDS;
		if( ! isset($NTS_ALL_IDS[$className]) )
		{
			$NTS_ALL_IDS[$className] = array();

			$ntsdb =& dbWrapper::getInstance();
			$om =& objectMapper::getInstance();
			$tblName = $om->getTableForClass( $className );

			if( (! $addonString) && $om->isPropRegistered($className, 'show_order') )
				$addonString .= ' ORDER BY show_order ASC';

			$where = array();
//			if( $om->isPropRegistered($className, 'archive') )
//			{
//				$where['archive'] = array('<>', 1);
//			}

			$result = $ntsdb->select( 
				'id',
				$tblName,
				$where,
				$addonString
				);
			if( $result )
			{
				while( $u = $result->fetch() )
				{
					$NTS_ALL_IDS[$className][] = $u['id'];
				}
			}
		}

		return $NTS_ALL_IDS[$className];
	}

	static function find_one( $className, $where, $addonString = '' )
	{
		$return = NULL;
		$all = ntsObjectFactory::find( $className, $where, $addonString );
		if( isset($all[0]) )
		{
			$return = $all[0];
		}
		return $return;
	}

	static function find( $className, $where, $addonString = '' )
	{
		$return = array();
		$ntsdb =& dbWrapper::getInstance();
		$om =& objectMapper::getInstance();
		$tblName = $om->getTableForClass( $className );

		if( (! $addonString) )
		{
			if( $om->isPropRegistered($className, 'show_order') )
			{
				$addonString .= ' ORDER BY show_order ASC';
			}
			else
			{
				switch( $className )
				{
					case 'appointment':
						$addonString .= ' ORDER BY starts_at ASC';
						break;
				}
			}
		}

		$ids = array();
		$result = $ntsdb->select( 'id', $tblName, $where, $addonString );
		if( $result )
		{
			while( $u = $result->fetch() )
			{
				$ids[] = $u['id'];
			}
		}
		if( $ids )
		{
			ntsObjectFactory::preload( $className, $ids );
			reset( $ids );
			foreach( $ids as $id )
			{
				$o = ntsObjectFactory::get( $className );
				$o->setId( $id );
				$return[] = $o;
			}
		}
		return $return;
	}

	static function count( $className, $where = array(), $addonString = '' )
	{
		$return = 0;
		$ntsdb =& dbWrapper::getInstance();
		$om =& objectMapper::getInstance();
		$tblName = $om->getTableForClass( $className );

		$ids = array();
		$return = $ntsdb->count( $tblName, $where, $addonString );
		return $return;
	}

	static function getAll( $className, $addonString = '', $returnById = FALSE ){
		$return = array();

		$ids = ntsObjectFactory::getAllIds( $className, $addonString );
		ntsObjectFactory::preload( $className, $ids );
		reset( $ids );
		foreach( $ids as $id ){
			$o = ntsObjectFactory::get( $className );
			$o->setId( $id );
			if( $returnById )
				$return[$id] = $o;
			else
				$return[] = $o;
			}
		return $return;
		}

	static function get( $className, $id = 0 ){
		static $classes;
		if( ! isset($classes[$className]) ){
			$classes[$className] = '';
			$customClassName = 'nts' . ucfirst( $className );
			$customClassFileName = $customClassName . '.php';
			$realClassFileName = NTS_APP_DIR . '/objects/' . $customClassFileName;
			if( file_exists($realClassFileName) ){
				include_once( $realClassFileName );
				$classes[$className] = $customClassName;
				}
			}

		$customClassName = $classes[$className];
		if( $customClassName ){
			$return = new $customClassName;
			}
		else {
			$return = new ntsObject( $className );
			}
		if( $id ){
			$return->setId( $id );
			}
		return $return;
		}
	}

class ntsObject {
	var $className;
	var $props = array();
	var $updatedProps = array();
	var $id = 0;
	var $notFound = false;
	var $_change_reason = '';

	protected $cost_actions = array(
		'coupon::apply',
		'promotion::apply',
		'appointment::discount'
		);

	function getLogs()
	{
		$return = array();

		$ntsdb =& dbWrapper::getInstance();
		$myId = $this->getId();
		$myClassName = $this->getClassName();

		$where = array(
			'obj_class'	=> array( '=', $myClassName ),
			'obj_id'	=> array( '=', $myId ),
			);
		$my_logs = $ntsdb->get_select( 
			array( 'user_id', 'action_time', 'property_name', 'old_value', 'description' ),
			'logaudit',
			$where,
			'ORDER BY action_time DESC'
			);
		if( ! $my_logs )
		{
			return $return;
		}

		$new_ones = array();
		foreach( $my_logs as $l )
		{
			if( array_key_exists($l['property_name'], $new_ones) )
				$l['new_value'] = $new_ones[ $l['property_name'] ];
			else
				$l['new_value'] = $this->getProp( $l['property_name'] );
			$new_ones[ $l['property_name'] ] = $l['old_value'];

			if( ! isset($return[$l['action_time']]) )
				$return[$l['action_time']] = array();
			$return[$l['action_time']][] = $l;
		}
		return $return;
	}

	function ntsObject( $className ){
		$this->className = $className;
		$this->id = 0;
		$this->props = array();
		$this->notFound = false;

		global $NTS_OBJECT_PROPS_CONFIG;
		$myClasses = $this->getMyClasses();
		reset( $myClasses );
		foreach( $myClasses as $myClass ){
			if( ! isset($NTS_OBJECT_PROPS_CONFIG[$myClass]) ){
				$om =& objectMapper::getInstance();
				$om->initPropsConfig( $myClass );
				}
			}
		$this->resetUpdatedProps();
		}

	function get_accounting_postings()
	{
		$amn =& ntsAccountingManager::getInstance();
		return $amn->get_postings( $this->getClassName(), $this->getId() );
	}

	function reset_accounting_postings()
	{
		$am =& ntsAccountingManager::getInstance();
		return $am->reset_accounting_postings( $this->getClassName(), $this->getId() );
	}

	function getPaidAmount()
	{
		$return = 0;
		$postings = $this->get_accounting_postings();
		if( $postings )
		{
			$calc = new ntsMoneyCalc;
			reset( $postings );
			foreach( $postings as $p )
			{
				if( $p['asset_id'] != 0 )
					continue;

				$action = $p['obj_class'] . '::' . $p['action'];
				if( in_array($action, $this->cost_actions) )
				{
					continue;
				}
				$calc->add( $p['asset_value'] );
			}
			$return = $calc->result();
		}
		return $return;
	}

	function getParents(){
		$return = array();
		$ntsdb =& dbWrapper::getInstance();
		$myId = $this->getId();
		$myClassName = $this->getClassName();

		$where = array(
			'meta_name'		=> array( '=', '_' . $myClassName ),
			'meta_value'	=> array( '=', $myId ),
			);
		$result = $ntsdb->select( array('obj_class', 'obj_id'), 'objectmeta', $where );

		if( $result ){
			while( $pInfo = $result->fetch() ){
				$p = ntsObjectFactory::get( $pInfo['obj_class'] );
				$p->setId( $pInfo['obj_id'] );
				$return[] = $p;
				}
			}
		return $return;
		}

	function getChildren( $filterClass = '' ){
		$return = array();
		$ntsdb =& dbWrapper::getInstance();
		$myId = $this->getId();
		$myClassName = $this->getClassName();

		$where = array(
			'obj_id'	=> array( '=', $myId ),
			'obj_class'	=> array( '=', $myClassName ),
			);
		if( $filterClass )
			$where['meta_name']	= array( '=', '_' . $filterClass );
		else
			$where['meta_name']	= array( 'LIKE', '_%' );

		$result = $ntsdb->select( array('meta_name', 'meta_value'), 'objectmeta', $where );

		if( $result ){
			while( $pInfo = $result->fetch() ){
				$childClass = substr( $pInfo['meta_name'], 1 );
				$p = ntsObjectFactory::get( $childClass );
				$p->setId( $pInfo['meta_value'] );
				$return[] = $p;
				}
			}
		return $return;
		}

	function getMyClasses(){
		$myClasses = ( $this->className == 'user' ) ? array('customer', 'user') : array($this->className);
		return $myClasses;
		}
		
	function getDefaultProp( $pName ){
		global $NTS_OBJECT_PROPS_CONFIG;
		$return = null;

		$myClasses = ( $this->className == 'user' ) ? array('customer', 'user') : array($this->className);
		reset( $myClasses );
		foreach( $myClasses as $myClass ){
			if( isset($NTS_OBJECT_PROPS_CONFIG[$myClass][$pName]) ){
				$return = $NTS_OBJECT_PROPS_CONFIG[$myClass][$pName]['default'];
				break;
				}
			}
		return $return;
		}
	
	function getProp( $pName, $unserialize = FALSE ){
		global $NTS_OBJECT_PROPS_CONFIG;
		$return = null;
		if( isset($this->props[$pName]) ){
			if(
				isset($NTS_OBJECT_PROPS_CONFIG[$this->className][$pName]) && 
				$NTS_OBJECT_PROPS_CONFIG[$this->className][$pName]['isArray'] && 
				( ! is_array($this->props[$pName]) )
				){
				$this->props[$pName] = trim( $this->props[$pName] );
				if( $this->props[$pName] )
					$this->props[$pName] = array( $this->props[$pName] );
				else
					$this->props[$pName] = array();
				}
			$return = $this->props[$pName];
			}
		else {
			$myClasses = ( $this->className == 'user' ) ? array('customer', 'user') : array($this->className);
			reset( $myClasses );
			foreach( $myClasses as $myClass ){
				if( isset($NTS_OBJECT_PROPS_CONFIG[$myClass][$pName]) ){
					$return = $NTS_OBJECT_PROPS_CONFIG[$myClass][$pName]['default'];
					break;
					}
				}
			}
		if( $unserialize )
		{
			$return = unserialize( $return );
		}
		return $return;
		}

	function setId( $id, $load = true )
	{
		if( preg_match("/[^\d]/", $id) )
		{
			return;
		}
		$this->id = $id;
		if( ($id > 0) && $load )
		{
			$this->load();
		}
	}

	function notFound(){
		return $this->notFound;
		}

	function getId(){
		return $this->id;
		}

	function getClassName(){
		return $this->className;
		}

	function resetUpdatedProps(){
		$this->updatedProps = array();
		}

	function getMetaClass(){
		$useMetaIn = array( 'user', 'service', 'appointment', 'location', 'resource', 'order', 'invoice', 'transaction' );

		$return = '';
		$className = $this->getClassName();
		if( in_array($className, $useMetaIn) )
			$return = $className;

		return $return;
		}

	function load(){
		global $NTS_OBJECT_CACHE;
		$className = $this->getClassName();
		$id = $this->getId();
		if( ! $id )
			return;

		switch( $className ){
			case 'user':
//				echo "<h3>LOADING: $id</h3>";
				if( isset($NTS_OBJECT_CACHE[$className][$id]) ){
					$userInfo = $NTS_OBJECT_CACHE[$className][$id];
					}
				else {
					$uif =& ntsUserIntegratorFactory::getInstance();
					$integrator =& $uif->getIntegrator();
					$userInfo = $integrator->getUserById( $id );
					$NTS_OBJECT_CACHE[$className][$id] = $userInfo;
					}
				if( $userInfo ){
					$this->setByArray( $userInfo );
					$this->resetUpdatedProps();
					}
				else {
					$this->notFound = true;
					}
				break;

			default:
				$ntsdb =& dbWrapper::getInstance();
				$className = $this->getClassName();

				if( isset($NTS_OBJECT_CACHE[$className][$id]) ){
					$this->setByArray( $NTS_OBJECT_CACHE[$className][$id], true );
					$this->resetUpdatedProps();
					}
				else {
					$om =& objectMapper::getInstance();
					$tblName = $om->getTableForClass( $className );

					$sql = "SELECT * FROM {PRFX}$tblName WHERE id = $id";

					$result = $ntsdb->runQuery( $sql );
					if( $result && ($u = $result->fetch()) ){
						$metaClass = $this->getMetaClass();
					/* load meta as well */
						$metaInfo = $this->loadMeta();
						$u = array_merge( $u, $metaInfo );
//_print_r( $u );
						$this->setByArray( $u, true );
						$this->resetUpdatedProps();

						$NTS_OBJECT_CACHE[$className][$id] = $u;
						}
					else {
						$this->notFound = true;
						}
					}
				break;
			}
		}

	function loadMeta(){
		global $NTS_OBJECT_PROPS_CONFIG;
		$return = array();
		$objId = $this->getId();
		if( ! $objId )
			return;
		$metaClass = $this->getMetaClass();
		if( ! $metaClass )
			return $return;

		$ntsdb =& dbWrapper::getInstance();
		$sql =<<<EOT
SELECT 
	meta_name, meta_value, meta_data
FROM 
	{PRFX}objectmeta 
WHERE
	obj_id = $objId AND obj_class = "$metaClass"
EOT;

		$result = $ntsdb->runQuery( $sql );
		if( $result ){
			while( $n = $result->fetch() ){
				$n['meta_data'] = trim( $n['meta_data'] );
				if( isset($return[$n['meta_name']]) ){
					if( isset($NTS_OBJECT_PROPS_CONFIG[$metaClass][$n['meta_name']]) && $NTS_OBJECT_PROPS_CONFIG[$metaClass][$n['meta_name']]['isArray'] ){
						if( ! is_array($return[$n['meta_name']]) )
							$return[$n['meta_name']] = array( $return[$n['meta_name']] );
						if( strlen($n['meta_data']) && ($NTS_OBJECT_PROPS_CONFIG[$metaClass][$n['meta_name']]['isArray'] == 2) )
							$return[$n['meta_name']][ $n['meta_value'] ] = $n['meta_data'];
						elseif( ($NTS_OBJECT_PROPS_CONFIG[$metaClass][$n['meta_name']]['isArray'] == 3) )
							$return[$n['meta_name']][] = array( $n['meta_value'], $n['meta_data'] );
						else {
							if( ! in_array($n['meta_value'], $return[$n['meta_name']] ) ) 
								$return[$n['meta_name']][] = $n['meta_value'];
							}
						}
					}
				else {
					if( strlen($n['meta_data']) && (isset($NTS_OBJECT_PROPS_CONFIG[$metaClass][$n['meta_name']]) && ($NTS_OBJECT_PROPS_CONFIG[$metaClass][$n['meta_name']]['isArray'] == 2) ) ){
						$return[ $n['meta_name'] ] = array( $n['meta_value'] => $n['meta_data'] );
						}
					elseif( isset($NTS_OBJECT_PROPS_CONFIG[$metaClass][$n['meta_name']]) && ($NTS_OBJECT_PROPS_CONFIG[$metaClass][$n['meta_name']]['isArray'] == 3)  ){
						$return[ $n['meta_name'] ] = array( array($n['meta_value'], $n['meta_data']) );
						}
					else {
						$return[ $n['meta_name'] ] = $n['meta_value'];
						}
					}
				}
			}
		return $return;
		}

	function deleteProp( $pName, $pValue ){
		if( ! isset($this->props[$pName]) )
			return;
			
		if( ! is_array($this->props[$pName]) )
			return;
			
		$result = array();
		reset( $this->props[$pName] );
		foreach( $this->props[$pName] as $v ){
			if( $v == $pValue )
				continue;
			$result[] = $v;
			}
		$this->props[$pName] = $result;
		}

	function setProp( $pName, $pValue, $fromStorage = false ){
		if( $pValue === 0 )
			$pValue = '0';

		global $NTS_OBJECT_PROPS_CONFIG;
	/* if updated */
		if( ! $fromStorage ){
			if( 
				(! isset($this->props[$pName])) OR 
				($pValue != $this->props[$pName]) OR 
				( (! is_array($pValue)) && (strlen($pValue) != strlen($this->props[$pName])) )
				)
				{
				if( isset($this->props[$pName]) )
					$this->updatedProps[$pName] = $this->props[$pName];
				else
					$this->updatedProps[$pName] = null;
				}
			}

		if( isset($NTS_OBJECT_PROPS_CONFIG[$this->className][$pName]) ){
			if( 
				$NTS_OBJECT_PROPS_CONFIG[$this->className][$pName]['isCore'] && 
				$NTS_OBJECT_PROPS_CONFIG[$this->className][$pName]['isArray'] 
				){
				if( $fromStorage ){
					$pValue = trim($pValue);
					if( strlen($pValue) )
						$pValue = unserialize( $pValue );
					else
						$pValue = array();
					$this->props[$pName] = $pValue;
					}
				else {
					if( is_array($pValue) ){
						$this->props[$pName] = $pValue;
						}
					else {
						if( ! isset($this->props[$pName]) )
							$this->props[$pName] = array();
						$pValue = trim($pValue);
						if( strlen($pValue) )
							$this->props[$pName][] = $pValue;
						}
					}
				}
			else {
				$this->props[$pName] = $pValue;
				}
			}
		else {
			$this->props[$pName] = $pValue;
			}
		}

	function setByArray( $array, $fromStorage = false ){
		reset( $array );
		foreach( $array as $pName => $pValue ){
			$this->setProp( $pName, $pValue, $fromStorage );
			if( $pName == 'id' )
				$this->setId( $pValue, false );
			}
		}

	function getChanges()
	{
		$return = array();
		reset( $this->updatedProps );
		foreach( $this->updatedProps as $upn => $upv )
		{
			$return[$upn] = $upv;
		}
		return $return;
	}

	function to_array()
	{
		return $this->getByArray();
	}

	function getByArray( $split = false, $updated = false ){
		global $NTS_OBJECT_PROPS_CONFIG;
		if( $updated ){
			$props = array();
			reset( $this->updatedProps );
			foreach( $this->updatedProps as $upn => $upv ){
				$props[ $upn ] = $this->getProp( $upn );
				}
			}
		else {
//			$props = $this->props;
			reset( $this->props );
			foreach( $this->props as $k => $v ){
				$props[ $k ] = $this->getProp( $k );
				}

		/* check if any default props missing */
			reset( $NTS_OBJECT_PROPS_CONFIG[$this->className] );
			foreach( $NTS_OBJECT_PROPS_CONFIG[$this->className] as $pName => $pConfig ){
				if( ! isset($props[$pName]) )
					$props[$pName] = $NTS_OBJECT_PROPS_CONFIG[$this->className][$pName]['default'];
				}
			}

		if( $split ){
			$core = array();
			$meta = array();

			$om =& objectMapper::getInstance();
			list( $coreProps, $metaProps ) = $om->getPropsForClass( $this->getClassName() );
			$corePropsNames = array_keys( $coreProps );

			reset( $props );
			foreach( $props as $k => $v ){
				if( in_array($k, $corePropsNames) )
					$core[ $k ] = $v;
				else
					$meta[ $k ] = $v;
				}
			$return = array( $core, $meta );
			}
		else {
			$return = $props;
			}
		return $return;
		}
	}

class ntsObjectMapper {
	var $tables;
	var $props;
	var $controls = array();
	var $service2form = array();
	var $fieldsCache = array(); 

	function ntsObjectMapper(){
		$this->tables = array();
		$this->props = array();
		$this->controls = array();
		$this->service2form = array();
		$this->fieldsCache = array();
		$this->init();

	// common props registration
		$this->registerClass( 'user', 'users' );
		if( ! NTS_EMAIL_AS_USERNAME )
			$this->registerProp( 'user',	'username' );
		$this->registerProp( 'user',	'email' );
		$this->registerProp( 'user',	'password' );
		$this->registerProp( 'user',	'first_name' );
		$this->registerProp( 'user',	'last_name' );
		$this->registerProp( 'user',	'lang' );
		$this->registerProp( 'user',	'created' );
		$this->registerProp( 'user',	'_restriction',	false,	1,	array() );
		$this->registerProp( 'user',	'_timezone',	false,	0,	NTS_COMPANY_TIMEZONE );
		$this->registerProp( 'user',	'_role',				false,	1,	array('customer') );
		$this->registerProp( 'user',	'_disabled_panels',		false,	1,	array() );

		$this->registerClass( 'form', 'forms' );
		$this->registerProp( 'form',	'title' );
		$this->registerProp( 'form',	'class' );
		$this->registerProp( 'form',	'details' );

		$this->registerClass( 'form_control', 'form_controls' );
		$this->registerProp( 'form_control',	'form_id' );
		$this->registerProp( 'form_control',	'name' );
		$this->registerProp( 'form_control',	'type' );
		$this->registerProp( 'form_control',	'title' );
		$this->registerProp( 'form_control',	'description' );
		$this->registerProp( 'form_control',	'show_order' );
		$this->registerProp( 'form_control',	'ext_access' );
		$this->registerProp( 'form_control',	'attr' );
		$this->registerProp( 'form_control',	'validators' );
		$this->registerProp( 'form_control',	'default_value' );
		}

	function initPropsConfig( $className ){
		global $NTS_OBJECT_PROPS_CONFIG;
		if( ! isset($NTS_OBJECT_PROPS_CONFIG[$className]) ){
			list( $coreProps, $metaProps ) = $this->getPropsForClass( $className );
			$NTS_OBJECT_PROPS_CONFIG[ $className ] = array_merge( $coreProps, $metaProps );
			}
		}

	function makeTags_Customer( $object, $access = 'external' ){
		$fields = $this->getFields( 'customer', $access );
		$tags = array( array(), array() );

		$allInfo = '';
		foreach( $fields as $f ){
			$value = $object->getProp( $f[0] );
			if( $f[2] == 'checkbox' ){
				$value = $value ? M('Yes') : M('No');
				}

			$c = $this->getControl( 'customer', $f[0], false );
			if( $c[2]['description'] ){
				$value .= ' (' . $c[2]['description'] . ')';
				}

			$tags[0][] = '{USER.' . strtoupper($f[0]) . '}';
			$tags[1][] = $value;

		/* build the -ALL- tag */
			$allInfo .= M($f[1]) . ': ' . $value . "\n";
			}

		if( NTS_EMAIL_AS_USERNAME ){
			$tags[0][] = '{USER.USERNAME}';
			$tags[1][] = $object->getProp( 'email' );
			}

		$tags[0][] = '{USER.PASSWORD}';
		$newPasssword = $object->getProp( 'new_password' );
		if( $newPasssword )
			$tags[1][] = $newPasssword;
		else
			$tags[1][] = M('Not Shown For Security Reasons');

		$tags[0][] = '{USER.-ALL-}';
		$tags[1][] = $allInfo;
		return $tags;
		}

	function registerClass( $className, $storeTable ){
		$this->tables[ $className ] = $storeTable;
		$this->props[ $className ] = array();
		$this->registerProp( $className, 'id', true, false, 0 );
		}

/* $pArray: (pName, isCore, isArray, default) */
	function registerProp( $className, $pName, $isCore = true, $isArray = false, $default = '' ){
		$this->props[ $className ][ $pName ] = array(
			'isCore'	=> $isCore,
			'isArray'	=> $isArray,
			'default'	=> $default,
			);
		}

	function getTableForClass( $className ){
		$return = '';
		if( isset($this->tables[ $className ]) )
			$return = $this->tables[ $className ];
		else
			echo "getTableForClass: Class '$className' is not registered!";
		return $return;
		}

	function isClassRegistered( $className ){
		$return = FALSE;
		if( isset($this->tables[ $className ]) )
			$return = TRUE;
		return $return;
		}
	
	function isPropRegistered( $className, $propName ){
		$return = false;
		list( $core, $meta ) = $this->getPropsForClass( $className );
		$propNames = array_merge( array_keys($core), array_keys($meta) );
		if( in_array($propName, $propNames) ){
			$return = true;
			}
		return $return;
		}

	function getPropsForClass( $className ){
		$core = array();
		$meta = array();
		$return = array( $core, $meta );

		if( isset($this->props[ $className ]) ){
			reset( $this->props[ $className ] );
			foreach( $this->props[ $className ] as $pName => $pa ){
				if( $pa['isCore'] )
					$core[ $pName ] = $pa;
				else
					$meta[ $pName ] = $pa;
				}
			$return = array( $core, $meta );
			}
		else {
//			echo "getPropsForClass: Class '$className' is not registered!";
			}

		return $return;
		}

	function isFormForService( $serviceId ){
		$return = isset($this->service2form[$serviceId]) ? $this->service2form[$serviceId] : 0;
		return $return;
		}

	/*
	$side = internal|external
	*/
	function getFields( $className, $side = 'internal', $otherProps = array() ){
//		$this->fieldsCache;

		$cacheString = $className . '-' . $side;
		if( $otherProps ){
			$cacheString .= '-' . serialize($otherProps);
			}

		if( ! isset($this->fieldsCache[$cacheString]) ){
			$return = array();
			if( isset($this->controls[$className]) ){
				uasort( $this->controls[$className], create_function('$a, $b', 'return ($a["show_order"] - $b["show_order"]);') );
				reset( $this->controls[$className] );
				foreach( $this->controls[$className] as $cName => $c ){
					if( $className == 'appointment' ){
						$serviceId = isset($otherProps['service_id']) ? $otherProps['service_id'] : 0;
						if( $serviceId != -1 ){
							$requireForm = $this->isFormForService( $serviceId );
							if( $requireForm != $c['form_id'] )
								continue;
							}
						}

					if( $side == 'external' ){
						if( $c['ext_access'] == 'hidden' )
							continue;
						}
					if( ($side == 'external') && ($c['ext_access'] == 'read') ){
						$accessLevel = 'read';
						}
					else {
						$accessLevel = 'write';
						}

					if( NTS_EMAIL_AS_USERNAME && ($c['name'] == 'username') )
						continue;
						
					$return[] = array( $c['name'], $c['title'], $c['type'], $c['default'], $accessLevel );
					}
				}
			$this->fieldsCache[$cacheString] = $return;
			}
		else {
//			echo "fields '$cacheString' already set<br>";
			}
		$return = $this->fieldsCache[$cacheString];
		return $return;
		}

	function getControl( $className, $name, $forSearch = false ){
		$return = array();

		if( ! isset($this->controls[$className][$name]) ){
			echo "Control '$name' not defined for '$className'!<br>";
			return $return;
			}

		$c = $this->controls[$className][$name];
		if( preg_match('/\[m\](.+)\[\/m\]/', $c['title'], $ma) )
		{
			$c['title'] = M($ma[1]);
		}

		if( ! $forSearch ){
			if( isset($c['validators']) && $c['validators'] ){
				$c['title'] .= ' *';
				}
			}

		$attributes = array(
			'id'	=> $c['name'],
			'attr'	=> $c['attr']
			);

		$attributes = array();
		if( $c['type'] == 'select' )
		{
			$attributes['options'] = $c['attr']['options'];
			unset($c['attr']['options']);
			if( $forSearch )
			{
				array_unshift( $attributes['options'], array( '-any-', '- ' . M('Any') . ' -' ) );
			}
		}

		$c['attr']['_class'] = $className;
		$attributes['id'] = $c['name'];
		$attributes['attr'] = $c['attr'];
		if( isset($c['description']) )
			$attributes['description'] = $c['description'];
		else
			$attributes['description'] = '';

		$attributes['default'] = $c['default'];

		$return = array(
			$c['title'],
			$c['type'],
			$attributes,
			$c['validators']
			);

		return $return;
		}

	function prepareMeta( $objId, $metaClass, $metaInfo, $full = true ){
		global $NTS_OBJECT_PROPS_CONFIG;
		$return = array();
		reset( $metaInfo );
		foreach( $metaInfo as $k => $va ){
//			if( ! isset( $NTS_OBJECT_PROPS_CONFIG[$metaClass][$k] ) )
//				continue;

			if( ! is_array($va) )
				$va = array( $va );

			reset( $va );
			foreach( $va as $kk => $v ){
				if( ! strlen($v) )
					continue;
				$v = trim($v);
				if(
					isset( $NTS_OBJECT_PROPS_CONFIG[$metaClass][$k]['isArray'] ) && 
					( $NTS_OBJECT_PROPS_CONFIG[$metaClass][$k]['isArray'] == 2 ) 
					){
					$metaArray = array(
						'meta_name'		=> $k,
						'meta_value'	=> $kk,
						'meta_data'		=> $v,
						);
					}
				elseif( 
					isset( $NTS_OBJECT_PROPS_CONFIG[$metaClass][$k]['isArray'] ) && 
					( $NTS_OBJECT_PROPS_CONFIG[$metaClass][$k]['isArray'] == 3 ) 
					){
					$metaArray = array(
						'meta_name'		=> $k,
						'meta_value'	=> $kk,
						'meta_data'		=> $v,
						);
					}
				else {
					$metaArray = array(
						'meta_name'		=> $k,
						'meta_value'	=> $v,
						'meta_data'		=> '',
						);
					}
				if( $full ){
					$metaArray[ 'obj_id' ] = $objId;
					$metaArray[ 'obj_class' ] = $metaClass;
					}
				$return[] = $metaArray;
				}
			}
		return $return;
		}

	function deleteMeta( $object, $metaName, $metaValue = '' ){
		$result = true;
		$ntsdb =& dbWrapper::getInstance();
		$id = $object->getId();
		$metaClass = $object->getMetaClass();
		if( $metaClass ){
			if( $metaValue ){
				$result = $ntsdb->delete(
					'objectmeta',
					array(
						'obj_id'		=> array( '=', $id ),
						'obj_class'		=> array( '=', $metaClass ),
						'meta_name'		=> array( '=', $metaName ),
						'meta_value'	=> array( '=', $metaValue ),
						)
					);
				}
			else {
				$result = $ntsdb->delete(
					'objectmeta',
					array(
						'obj_id'		=> array( '=', $id ),
						'obj_class'		=> array( '=', $metaClass ),
						'meta_name'		=> array( '=', $metaName ),
						)
					);
				}
			}
		return $result;
		}

	function init(){
		$ntsdb =& dbWrapper::getInstance();
		$controls = array();

		$appInfo = ntsLib::getAppInfo();
		if( ! $appInfo['installed_version'] )
		{
			return;
		}

	// user's
		$userControls = array();
		if( ! NTS_EMAIL_AS_USERNAME )
			$userControls[] = array( 'username',		'Username',	 	'text',	array('size' => 24), array( array('code' => 'notEmpty', 'error' => 'Required field'), array('code' => 'checkUsername', 'error' => 'Already in use', 'params' => array('skipMe'	=> 1) ) ) );
		$userControls[] = array( 'email',			'Email',			'text',	array('size' => 32), array( array('code' => 'notEmpty', 'error' => 'Required field'), array('code' => 'checkUserEmail', 'error' => 'Already in use', 'params' => array('skipMe'	=> 1) ) ) );
		$userControls[] = array( 'first_name',	'First Name',	'text',	array('size' => 32), array( array('code' => 'notEmpty', 'error' => 'Required field') ) );
		$userControls[] = array( 'last_name',		'Last Name',		'text',	array('size' => 32), array( array('code' => 'notEmpty', 'error' => 'Required field') ) );

		$order = 1;
		foreach( $userControls as $c ){
			$cInfo['class'] = 'user';
			$cInfo['ext_access'] = 'read';
			$cInfo['name'] = $c[0];
			$cInfo['title'] = $c[1];
			$cInfo['type'] = $c[2];
			$cInfo['attr'] = $c[3];

			$cInfo['validators'] = array();
			foreach( $c[4] as $v ){
				$v['code'] = $v['code'] . '.php';
				$cInfo['validators'][] = $v;
				}

			$cInfo['show_order'] = $order++;
			$controls[] = $cInfo;
			}

		// provider's
		$providerControls = array();
		if( ! NTS_EMAIL_AS_USERNAME )
			$providerControls[] = array( 'username',		'Username',	 	'text',	array('size' => 24), array( array('code' => 'notEmpty', 'error' => 'Required field'), array('code' => 'checkUsername', 'error' => 'Already in use', 'params' => array('skipMe'	=> 1) ) ) );
		$providerControls[] = array( 'email',			'Email',		'text',	array('size' => 32), array( array('code' => 'notEmpty', 'error' => 'Required field'), array('code' => 'checkUserEmail', 'error' => 'Already in use', 'params' => array('skipMe'	=> 1) ) ) );
		$providerControls[] = array( 'first_name',	'First Name',	'text',	array('size' => 32), array( array('code' => 'notEmpty', 'error' => 'Please enter the first name') ) );
		$providerControls[] = array( 'last_name',		'Last Name',	'text',	array('size' => 32), array( array('code' => 'notEmpty', 'error' => 'Please enter the last name') ) );
//		$providerControls[] = array( '_description',	'Description',	'textarea',	array('cols' => 42, 'rows' => 4), array() );

		$order = 1;
		foreach( $providerControls as $c ){
			$cInfo['class'] = 'provider';
			$cInfo['ext_access'] = 'read';
			$cInfo['name'] = $c[0];
			$cInfo['title'] = $c[1];
			$cInfo['type'] = $c[2];
			$cInfo['attr'] = $c[3];

			$cInfo['validators'] = array();
			foreach( $c[4] as $v ){
				$v['code'] = $v['code'] . '.php';
				$cInfo['validators'][] = $v;
				}

			$cInfo['show_order'] = $order++;
			$controls[] = $cInfo;
			}

		$columns = array( 'form_id', 'name', 'type', 'title', 'show_order', 'ext_access', 'attr', 'validators', 'default_value', 'description' );

		$selectColumnsString = join( ",\n", array_map( create_function('$a', 'return "{PRFX}form_controls.' . '$a' . '";'), $columns) );
	// LOAD FROM DATABASE
		$sql =<<<EOT
SELECT 
	$selectColumnsString,
	(
	SELECT class FROM {PRFX}forms WHERE {PRFX}forms.id = {PRFX}form_controls.form_id
	) AS class
FROM 
	{PRFX}form_controls
ORDER BY
	show_order ASC
EOT;

		$result = $ntsdb->runQuery( $sql );
		if( $result ){
			while( $c = $result->fetch() ){
				if( isset($c['attr']) && $c['attr'] )
					$c['attr'] = unserialize($c['attr']);
				else
					$c['attr'] = array();

				if( isset($c['validators']) && $c['validators'] ){
					$validators = @unserialize($c['validators']);
					if( $validators ){
						reset( $validators );
						$c['validators'] = array();
						foreach( $validators as $v ){
							$v['code'] = $v['code'] . '.php';
							$c['validators'][] = $v;
							}
						}
					}
				else {
					$c['validators'] = array();
					}

				$c['default'] = $c['default_value'];
				$controls[] = $c;
				}
			}

		reset( $controls );
		foreach( $controls as $c ){
			$className = $c['class'];
			if( ! isset($this->controls[$className]) )
				$this->controls[$className] = array();

			if( ! isset($c['type']) )
				$c['type'] = 'text';

		/* default */
			if( ! isset($c['default']) )
				$c['default'] = '';

			if( $c['type'] == 'select' ){
				if( isset($c['attr']['options']) ){
					$rawOptions = $c['attr']['options'];
					$c['attr']['options'] = array();
					$c['default'] = $rawOptions[0];
					reset( $rawOptions );
					foreach( $rawOptions as $ro ){
						if( substr($ro, 0, 1) == '*' ){
							$ro = substr($ro, 1);
							$c['default'] = $ro;
							}
						$c['attr']['options'][] = array( $ro, $ro );
						}
					}
				else {
					$c['attr']['options'] = array();
					}
				}

			$this->controls[$className][$c['name']] = $c;
			/* className, pName, isCore, isArray, default */
			$this->registerProp( $className, $c['name'], false, false, $c['default'] );

			if( $c['name'] == 'mobile_phone' )
			{
				$plm =& ntsPluginManager::getInstance();
				$plugin = 'sms';
				$sms_settings = $plm->getPluginSettings( $plugin );
				if( 
					isset($sms_settings['gateway']) &&
					($sms_settings['gateway'] == 'email2sms') &&
					isset($sms_settings['carriers']) && 
					$sms_settings['carriers']
					)
				{
					$carrier_options = array();
					reset( $sms_settings['carriers'] );
					foreach( $sms_settings['carriers'] as $carrier )
					{
						$carrier_options[] = array( $carrier, $carrier );
					}
					if( $carrier_options ) 
					{
						array_unshift( $carrier_options, array('', ' - ' . 'Select' . ' - ') );
						$c2 = $c;
						$c2['name'] = 'mobile_carrier';
						$c2['type'] = 'select';
						$c2['default'] = '';
						$c2['title'] = 'Mobile Carrier';
						$c2['validators'] = array(
							array(
								'code' => 'notEmpty.php',
								'error' => 'Required field'
								)
							);

						$c2['attr'] = array(
							'options'	=> $carrier_options,
							);

						$this->controls[$className][$c2['name']] = $c2;
						/* className, pName, isCore, isArray, default */
						$this->registerProp( $className, $c2['name'], false, false, $c2['default'] );

					}
				}
			}
			}

	/* services to forms relation */
		$sql =<<<EOT
SELECT 
	obj_id AS service_id, meta_value AS form_id
FROM
	{PRFX}objectmeta
WHERE
	obj_class = "service" AND 
	meta_name = "_form"
EOT;
		$result = $ntsdb->runQuery( $sql );
		if( $result ){
			while( $c = $result->fetch() ){
				$this->service2form[ $c['service_id'] ] = $c['form_id'];
				}
			}
		}
	}