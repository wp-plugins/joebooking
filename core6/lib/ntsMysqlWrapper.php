<?php
class ntsMysqlResult {
	var $_result;

	function ntsMysqlResult( &$res ){
		$this->_result =& $res;
		@mysql_data_seek( $this->_result, 0 );
		}
	function fetch() {
		return mysql_fetch_assoc( $this->_result );
		}
	function fetch_row() {
		return mysql_fetch_row( $this->_result );
		}
	function free(){
		return mysql_free_result( $this->_result );
		}
	function size() {
		$size = mysql_num_rows( $this->_result );
		return $size;
		}
	}

class ntsMysqlWrapper {
	var $_host;
	var $_user;
	var $_pass;
	var $_db;
	var $_prefix;
	var $_dbLink;

	var $_debug;
	var $_queryCount;
	var $_error;

	var $_cache;
	var $_enableCache;
	var $_cacheCount;

	var $alert_error = FALSE;
	var $add_prefix = TRUE;

	function ntsMysqlWrapper( $host, $user, $pass, $db, $prefix = '' ){
		$this->_host = $host;
		$this->_user = $user;
		$this->_pass = $pass;
		$this->_db = $db;
		$this->_prefix = $prefix;

		$this->_dbLink = null;
		$this->_debug = false;
		$this->_queryCount = 0;

		$this->_cache = array();
		$this->_enableCache = false;
		$this->_cacheCount = array();
		$this->add_prefix = TRUE;

		if( $_SERVER['SERVER_NAME'] == 'localhost' )
			$this->alert_error = TRUE;
		else
			$this->alert_error = FALSE;
		}

	function getError(){
		return $this->_error;
		}
	function setError( $err ){
		$this->_error = $err;
		}
	function resetError(){
		$this->_error = '';
		}

	function checkSettings(){
		$return = false;
		// CONNECT TO DB SERVER
		if(! $this->_dbLink = @mysql_connect($this->_host, $this->_user, $this->_pass, true)){
			$error = "<br>Cannot connect to the MySQL database.";
			$error .= "<br>MySQL error: " . mysql_error();
			$error .= "<br>Supplied info: ";
			$error .= "hostname: " . $this->_host . ', ';
			$error .= "username: " . $this->_user . ', ';
			$error .= "password: " . $this->_pass;
			$this->setError( $error );
			return $return;
			}

		// TRY TO SELECT DB
		if(! @mysql_select_db($this->_db, $this->_dbLink) ){
			$error = "Logged in but cannot select the specified MySQL database: " . $this->_db;
			$this->setError( $error );
			return $return;
			}

		$return = true;
		return $return;
		}

	function init() {
		if( ! $this->_dbLink ){
			// CONNECTS TO THE DATABASE
			if(! $this->_dbLink = @mysql_connect($this->_host, $this->_user, $this->_pass, true)){
				echo "Cannot login to the MySQL database with the specified login information. The following error occurs: <BR>";
				echo '<I>' . mysql_errno() . ': ' . mysql_error() . '</I>';
				exit;
				}

			if(! @mysql_select_db($this->_db, $this->_dbLink) ){
				echo "Cannot select the specified MySQL database. The following error occurs: <BR>";
				echo '<I>' . mysql_errno() . ': ' . mysql_error() . '</I>';
				exit;
				}
			}
		}

	function runQuery( $sqlQuery ){
		$return = false;
		if( ! $sqlQuery )
			return $return;

	/* add prefix */
		$sqlQuery = str_replace( '{PRFX}', $this->_prefix, $sqlQuery );
		if( $this->_debug ){
			echo '<BR>' . nl2br($sqlQuery) . '<BR>';
			}

		if( $this->_enableCache && isset($this->_cache[$sqlQuery]) ){
			$mySqlResult = $this->_cache[$sqlQuery];
			if( $this->_debug ){
				echo '==== ON CACHE =====<BR>';
				if( ! isset($this->_cacheCount[$sqlQuery]) ){
					$this->_cacheCount[$sqlQuery] = 1;
					}
				$this->_cacheCount[$sqlQuery]++;
				}
			}
		else {
			if( ! $mySqlResult = @mysql_query($sqlQuery, $this->_dbLink) )
			{
				$errStr = 'MySQL error - ' . mysql_errno() . ': ' . mysql_error();
				$this->setError( $errStr );
				if( $this->alert_error )
				{
					echo 'MySQL error - ' . mysql_errno() . ': ' . mysql_error() . '. The query was:<BR><pre>' . $sqlQuery . '</pre>';
					echo 'MySQL error occured<br>';
				}
				return $return;
			}
			if( $this->_enableCache )
				$this->_cache[$sqlQuery] = $mySqlResult;
			$this->_queryCount++;
			}

		$result = new ntsMysqlResult( $mySqlResult );
		return $result;
		}

	function getInsertId(){
		return mysql_insert_id( $this->_dbLink );
		}

	function insert( $tblName, $paramsArray, $forcedTypes = array() ){
		$propsAndValues = $this->prepareInsertStatement( $paramsArray, $forcedTypes );
		$sql =<<<EOT
INSERT INTO {PRFX}$tblName 
$propsAndValues
EOT;

		$return = 0;
		$result = $this->runQuery( $sql );
		if( $result )
			$return = $this->getInsertId();
		return $return;
		}

	function insert_multiple( $tblName, $paramsArray, $forcedTypes = array() )
	{
		$valuesArray = array();
		reset( $paramsArray );
		foreach( $paramsArray as $pArray )
		{
			list( $columnsString, $valuesString ) = $this->prepareInsertParts( $pArray, $forcedTypes );
			$valuesArray[] = $valuesString;
		}
		$valuesMultiString = join( ',', $valuesArray );

		$sql =<<<EOT
INSERT INTO {PRFX}$tblName 
$columnsString VALUES $valuesMultiString 
EOT;

		$return = 0;
		$result = $this->runQuery( $sql );
		if( $result )
			$return = $this->getInsertId();
		return $return;
	}

	function prepareInsertParts( $array, $forcedTypes = array() )
	{
		$columns = array();
		$values = array();

		$forcedTypes['meta_value'] = 'string';

		foreach( $array as $pName => $pValue ){
			$columns[] = $pName;
		/* is a string */
			if( is_array($pValue) ){
				$pValue = $pValue ? serialize( $pValue ) : '';
				}
			if ( strlen($pValue) == 0 || preg_match("/[^\d]/", $pValue) || ( isset($forcedTypes[$pName]) && ($forcedTypes[$pName] == 'string') )  )
				if( ! (isset($forcedTypes[$pName]) && ($forcedTypes[$pName] == 'number')) )
					$pValue = "'" . mysql_real_escape_string($pValue) . "'";
			$values[] = $pValue;
			}
		$columnsString = '(' . join( ', ', $columns ) . ')';
		$valuesString = '(' . join( ', ', $values ) . ')';

		$return = array( $columnsString, $valuesString );
		return $return;
	}

	function prepareInsertStatement( $array, $forcedTypes = array(), $fieldsOrder = array() ){
		$columns = array();
		$values = array();

		$forcedTypes['meta_value'] = 'string';

		if( $fieldsOrder ){
			foreach( $fieldsOrder as $f ){
				$pName = $f;
				$pValue = $array[$f];
			/* is a string */
				if( is_array($pValue) ){
					$pValue = $pValue ? serialize( $pValue ) : '';
					}
				if ( strlen($pValue) == 0 || preg_match("/[^\d]/", $pValue) || ( isset($forcedTypes[$pName]) && ($forcedTypes[$pName] == 'string') )  )
					if( ! (isset($forcedTypes[$pName]) && ($forcedTypes[$pName] == 'number')) )
						$pValue = "'" . mysql_real_escape_string($pValue) . "'";
				$values[] = $pValue;
				}
			$valuesString = '(' . join( ', ', $values ) . ')';

			$sql = "VALUES $valuesString";
			}
		else {
			foreach( $array as $pName => $pValue ){
				$columns[] = $pName;
			/* is a string */
				if( is_array($pValue) ){
					$pValue = $pValue ? serialize( $pValue ) : '';
					}
				if ( strlen($pValue) == 0 || preg_match("/[^\d]/", $pValue) || ( isset($forcedTypes[$pName]) && ($forcedTypes[$pName] == 'string') )  )
					if( ! (isset($forcedTypes[$pName]) && ($forcedTypes[$pName] == 'number')) )
						$pValue = "'" . mysql_real_escape_string($pValue) . "'";
				$values[] = $pValue;
				}
			$columnsString = '(' . join( ', ', $columns ) . ')';
			$valuesString = '(' . join( ', ', $values ) . ')';

			$sql = "$columnsString VALUES $valuesString";
			}
		return $sql;
		}

	function buildSelect( $what, $from, $whereArray = array(), $other = '', $join = array() ){
		$where = $this->buildWhere( $whereArray );

		if( ! is_array($what) ){
			$what = array( $what );
			}
		$what = join( ',', $what );

		if( ! is_array($from) ){
			$from = array( $from );
			}
		$from = array_map( create_function('$a', 'if( substr($a,0,1)=="("){return $a;} else {return "{PRFX}$a";};'), $from );
		$from = join( ',', $from );

		$sql =<<<EOT
SELECT
	$what
FROM
	$from
EOT;
		if( $join ){
			reset( $join );
			foreach( $join as $jo ){
				list( $tbl, $onArray ) = $jo;
				if( substr($tbl,0,1) != '(' )
					$tbl = '{PRFX}' . $tbl;
				$on = $this->buildWhere( $onArray );
				$sql .=<<<EOT

INNER JOIN
	$tbl
ON
	$on

EOT;
				}
			}

		if( $where )
			$sql .= " WHERE $where";
		if( $other )
			$sql .= " $other";
		return $sql;
		}

	function select( $what, $from, $whereArray = array(), $other = '', $join = array() ){
		$sql = $this->buildSelect( $what, $from, $whereArray, $other, $join );
		$result = $this->runQuery( $sql );
		return $result;
		}

	function get_select_flat( $what, $from, $whereArray = array(), $other = '', $join = array() )
	{
		$return = array();
		$results = $this->get_select( $what, $from, $whereArray, $other, $join );
		foreach( $results as $r )
		{
			foreach( $r as $r2 )
			{
				$return[] = $r2;
				break;
			}
		}
		return $return;
	}

	function get_select( $what, $from, $whereArray = array(), $other = '', $join = array() )
	{
		$return = array();
		$result = $this->select( $what, $from, $whereArray, $other, $join );

		$outflat = FALSE;
		$justone = FALSE;
		if(
			(! is_array($what)) &&
			(strpos($what, '*') === FALSE) &&
			(strpos($what, ',') === FALSE) &&
			(strpos($what, '(') === FALSE) &&
			(strpos($what, ')') === FALSE) &&
			(strpos($what, ' ') === FALSE)
			)
		{
			$outflat = TRUE;
		}
		if(
			(! is_array($what)) &&
				(
				(strpos($what, 'MAX(') !== FALSE) OR 
				(strpos($what, 'MIN(') !== FALSE)
				)
			)
		{
			$justone = TRUE;
		}

		if( $result )
		{
			while( $e = $result->fetch() )
			{
				if( $justone )
				{
					$return = array_shift($e);
				}
				else
				{
					if( $outflat )
						$return[] = $e[$what];
					else
						$return[] = $e;
				}
			}
		}
		return $return;
	}

	function affectedRows()
	{
		return @mysql_affected_rows($this->_dbLink);
	}

	function count( $from, $whereArray = array() ){
		$return = 0;

		$where = $this->buildWhere( $whereArray );

		if( ! is_array($from) ){
			$from = array( $from );
			}
		$from = array_map( create_function('$a', 'if( substr($a,0,1)=="("){return $a;} else {return "{PRFX}$a";};'), $from );
		$from = join( ',', $from );

		$sql =<<<EOT
SELECT
	COUNT(*) AS count
FROM
	$from
EOT;
		if( $where )
			$sql .= " WHERE $where";

		$result = $this->runQuery( $sql );
		if( $result && $e = $result->fetch() ){
			$return = $e['count'];
			}

		return $return;
		}

	function update( $tblName, $paramsArray, $whereArray, $forcedTypes = array() ){
		$propsAndValues = $this->prepareUpdateStatement( $paramsArray, $forcedTypes );
		$where = $this->buildWhere( $whereArray );

		$sql =<<<EOT
UPDATE {PRFX}$tblName 
SET $propsAndValues
WHERE $where
EOT;
		$result = $this->runQuery( $sql );
		return $result;
		}


	function buildWhere( $whereArray ){
		$simple = false;
		reset( $whereArray );
		foreach( $whereArray as $k => $w ){
			// non digit, real one
			if( preg_match('/[^\d]/', $k) ){
				$simple = true;
				break;
				}
			}

		reset( $whereArray );
		if( $simple ){
			$parts = array();
			foreach( $whereArray as $k => $w ){
			// check right part
				if( is_array($w[1]) ){
					if( ! $w[1] ){
						if( isset($w[2]) && $w[2] ){
							$w[1] = array('NULL');
							}
						else
							continue;
						}
					if( isset($w[3]) && $w[3] )
					{
						$realw1 = array();
						foreach( $w[1] as $w1 )
						{
							if( ! in_array(substr($w1, 0, 1), array('\'', '"')) )
							{
								$w1 = "'" . mysql_real_escape_string($w1) . "'";
							}
							$realw1[] = $w1;
						}
						$w[1] = $realw1;
					}
					$w[1] = '(' . join(',', $w[1]) . ')';
					}
				else {
					if( ! (isset($w[2]) && $w[2]) ){ // not parse if ask for it
						// non digit
						if( preg_match('/[^\d]/', $w[1]) ){
							$w[1] = "'" . mysql_real_escape_string($w[1]) . "'";
							}
						}
					elseif($w[2] == 'complex'){
						if( preg_match('/\./', $w[1]) && (substr($w[1], 0, 1) != '(') ){
							$w[1] = '{PRFX}' . $w[1];
							}
						}
					}
				// add {PRFX} if table name specified
				if( preg_match('/\./', $k) && (substr($k, 0, 1) != '(') ){
					$k = '{PRFX}' . $k;
					}
				$parts[] = join( '', array($k, ' ' . $w[0] . ' ', $w[1]) );
				}
			$return = join( ' AND ', $parts );
			}
		else {
			$parts = array();
			$joinBy = ' OR ';
			for( $ii = 0; $ii < count($whereArray); $ii++ ){
				$w = $whereArray[$ii];
				if( is_array($w) ){
					$parts[] = '(' . $this->buildWhere($w) . ')';
					}
				else {
					if( $ii > 0 ){
						$parts[] = ' ' . $w . ' '; // OR or AND
						}
					$joinBy = '';
					}
				}
			$return = join( $joinBy, $parts );
			}
		return $return;
		}

	function buildWhere_( $whereArray, $joinBy = 'AND' ){
		$return = array();

		reset( $whereArray );
		foreach( $whereArray as $k => $w ){
			// non digit, real one
			if( preg_match('/[^\d]/', $k) ){
				$joinBy = 'AND';
			// check right part
				if( is_array($w[1]) ){
					if( ! $w[1] ){
						if( isset($w[2]) && $w[2] ){
							$w[1] = array('NULL');
							}
						else
							continue;
						}
					$w[1] = '(' . join(',', $w[1]) . ')';
					}
				else {
					if( ! (isset($w[2]) && $w[2]) ){ // not parse if ask for it
						// non digit
						if( preg_match('/[^\d]/', $w[1]) ){
							$w[1] = "'" . mysql_real_escape_string($w[1]) . "'";
							}
						}
					elseif($w[2] == 'complex'){
						if( preg_match('/\./', $w[1]) && (substr($w[1], 0, 1) != '(') ){
							$w[1] = '{PRFX}' . $w[1];
							}
						}
					}
				// add {PRFX} if table name specified
				if( preg_match('/\./', $k) && (substr($k, 0, 1) != '(') ){
					$k = '{PRFX}' . $k;
					}
//				if( $w[0] == 'LIKE' )
//					$k = 'LOWER(' . $k . ')';
				$return[] = join( '', array($k, ' ' . $w[0] . ' ', $w[1]) );
				}
			else { // array of alternatives
				$joinBy = 'OR';
				$return[] = '(' . $this->buildWhere($w, 'AND') . ')';
				}
			}
		$return = join( ' ' . $joinBy . ' ', $return );
		return $return;
		}

	function delete( $tblName, $whereArray ){
		$where = $this->buildWhere( $whereArray );

		$sql =<<<EOT
DELETE FROM {PRFX}$tblName 
WHERE $where
EOT;
		$result = $this->runQuery( $sql );
		return $result;
		}
	
	function prepareUpdateStatement( $array, $forcedTypes = array() ){
		reset( $array );

		if( ! isset($forcedTypes['meta_value']) )
			$forcedTypes['meta_value'] = 'string';
			
		$pairs = array();
		foreach( $array as $pName => $pValue ){
			if( $pName == 'id' )
				continue;
			if( is_array($pValue) ){
				$pValue = $pValue ? serialize( $pValue ) : '';
				}
			if ( strlen($pValue) == 0 || preg_match("/[^\d]/", $pValue) || (isset($forcedTypes[$pName]) && ($forcedTypes[$pName] == 'string'))  )
			{
				if( ! (isset($forcedTypes[$pName]) && ($forcedTypes[$pName] == 'expression')) )
				{
					$pValue = "'" . mysql_real_escape_string($pValue) . "'";
				}
			}
			$pairs[] = $pName . ' = ' . $pValue;
			}

		$sql = join( ', ', $pairs );
		return $sql;
		}

	function getColumnsInTable( $tblName )
	{
		$sql = "DESCRIBE {PRFX}$tblName";
		$result = $this->runQuery( $sql );

		while( $l = $result->fetch() )
		{
			$return[ $l['Field'] ] = $l;
		}
		return $return;
	}

	function dumpTable( 
		$tblName,
		$parsePrefix = false,
		$clearBefore = true
		)
	{
		$return = '';
		if( substr($tblName,0,1) == '(' )
		{
			$prfx = '';
			$tblName = str_replace( '(', '', $tblName );
			$tblName = str_replace( ')', '', $tblName );
		}
		else
		{
			$prfx = $parsePrefix ? $this->_prefix : '{PRFX}';
		}

		$fieldsDesc = array();
		$fieldsOrder = array();
		$sql = "DESCRIBE $prfx" . "$tblName";
		$result = $this->runQuery( $sql );

		$priKey = '';
		while( $l = $result->fetch() ){
			if( strlen($l['Default']) )
				$l['Default'] = "DEFAULT '" . $l['Default'] . "'";
			if( $l['Null'] != 'NO' )
				$l['Null'] = 'NOT NULL';
			else
				$l['Null'] = '';
			if( $l['Key'] == 'PRI' )
				$priKey =  $l['Field'];

			$fieldsDesc[] = "`" . $l['Field'] . "` " . $l['Type'] . ' ' . $l['Null'] . ' ' . $l['Default'] . ' ' . $l['Extra'];
			$fieldsOrder[] = $l['Field'];
			}
		if( $priKey )
			$fieldsDesc[] = "PRIMARY KEY  (`" . $priKey . "`)";
		$propsAndValues = '(' . join( ", ", $fieldsDesc ) . ")";

		if( $clearBefore && ($tblName != 'conf') ){
			$return .= "DROP TABLE IF EXISTS $prfx" . "$tblName;\n";
			}
		$return .= "CREATE TABLE IF NOT EXISTS $prfx" . "$tblName $propsAndValues;\n";

		$sql = "SELECT * FROM $prfx" . "$tblName";
		$result = $this->runQuery( $sql );
		while( $l = $result->fetch() ){
			if( $tblName == 'conf' ){
				$skipConf = array('installationId', 'licenseCode');
				if( in_array($l['name'], $skipConf) )
					continue;
				}
		
			if( $clearBefore )
				$propsAndValues = $this->prepareInsertStatement( $l, array(), $fieldsOrder );
			else
				$propsAndValues = $this->prepareInsertStatement( $l );

			if( (! $clearBefore) && $priKey ){
				$priKeyValue = $l[$priKey];
				$return .= "DELETE FROM $prfx" . "$tblName WHERE $priKey = $priKeyValue;\n";
				}
			$return .= "INSERT INTO $prfx" . "$tblName $propsAndValues;\n";
			}
		return $return;
	}

	function tableExists( $tbl )
	{
		$return = FALSE;
		$tables = $this->getTablesInDatabase();
		if( in_array($tbl, $tables) )
		{
			$return = TRUE;
		}
		return $return;
	}

	function getTablesInDatabase(){
		$sql = 'SHOW tables';
		$return = array();
		$result = $this->runQuery( $sql );

		if( $result ){
			while( $e = $result->fetch() ){
				foreach( $e as $k => $v ){
					if( substr($v, 0, strlen($this->_prefix)) != $this->_prefix )
						continue;
					$v = substr( $v, strlen($this->_prefix) );
					$return[] = $v;
					}
				}
			}
		return $return;
		}
	}

class dbWrapper extends ntsMysqlWrapper {
	function __construct()
	{
		require( dirname(__FILE__) . '/../init_db.php' );
		$tbPrefix = $db['dbprefix'] . 'v6_';
		parent::ntsMysqlWrapper( 
			$db['hostname'],
			$db['username'],
			$db['password'],
			$db['database'],
			$tbPrefix
			);
		$this->init();
		$this->_debug = false;
		$this->_enableCache = false;
	}

	// Singleton stuff
	static function &getInstance(){
		return ntsLib::singletonFunction( 'dbWrapper' );
		}
	}
?>