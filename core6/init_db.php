<?php
$app = ntsLib::getAppProduct();
$db['hostname'] = isset($GLOBALS['NTS_CONFIG'][$app]['DB_HOST']) ? $GLOBALS['NTS_CONFIG'][$app]['DB_HOST'] : NTS_DB_HOST;
$db['username'] = isset($GLOBALS['NTS_CONFIG'][$app]['DB_USER']) ? $GLOBALS['NTS_CONFIG'][$app]['DB_USER'] : NTS_DB_USER;
$db['password'] = isset($GLOBALS['NTS_CONFIG'][$app]['DB_PASS']) ? $GLOBALS['NTS_CONFIG'][$app]['DB_PASS'] : NTS_DB_PASS;
$db['database'] = isset($GLOBALS['NTS_CONFIG'][$app]['DB_NAME']) ? $GLOBALS['NTS_CONFIG'][$app]['DB_NAME'] : NTS_DB_NAME;
$db['dbprefix'] = isset($GLOBALS['NTS_CONFIG'][$app]['DB_TABLES_PREFIX']) ? $GLOBALS['NTS_CONFIG'][$app]['DB_TABLES_PREFIX'] : NTS_DB_TABLES_PREFIX;
?>