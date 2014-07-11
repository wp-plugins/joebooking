<?php
$conf =& ntsConf::getInstance();
$ntsdb =& dbWrapper::getInstance();

// save time when run this backup
$now = time();
$conf->set( 'backupLastRun', $now );

$out = '';
$tables = $ntsdb->getTablesInDatabase();
reset( $tables );
foreach( $tables as $t )
{
	// skip users table, let the user integrator manage
	if( $t == 'users' )
		continue;
	$out .= $ntsdb->dumpTable( $t, TRUE ) . "\n";
}

$prfx = $ntsdb->_prefix;
$out .= "DROP TABLE IF EXISTS ${prfx}users;" . "\n";
$out .= "CREATE TABLE IF NOT EXISTS ${prfx}users (`id` int(11)   auto_increment, `username` varchar(128), `email` varchar(128), `password` varchar(64), `first_name` varchar(128) NOT NULL, `last_name` varchar(128) NOT NULL, `created` int(11), `lang` varchar(16) NOT NULL, PRIMARY KEY  (`id`));" . "\n";

// add users
$uif =& ntsUserIntegratorFactory::getInstance();
$integrator =& $uif->getIntegrator();
$users = $integrator->getUsers();
$take = array( 'id', 'username', 'email', 'password', 'first_name', 'last_name', 'created' );

foreach( $users as $u )
{
	if( ! isset($u['password']) )
	{
		$u['password'] = isset($u['user_pass']) ? $u['user_pass'] : '';
	}

	$user_dump = array();
	reset( $take );
	foreach( $take as $t )
	{
		$user_dump[$t] = isset($u[$t]) ? $u[$t] : '';
	}

	$propsAndValues = $ntsdb->prepareInsertStatement( $user_dump );
	$out .= "INSERT INTO $prfx" . "users $propsAndValues;\n";
}

//$uif =& ntsUserIntegratorFactory::getInstance();
//$integrator =& $uif->getIntegrator();
//$out .= $integrator->dumpUsers() . "\n";

$t = new ntsTime;
$fileName = 'AppointmentsBackup-' . $t->formatDate_Db() . '-' . $t->formatTime_Db(). '.sql';
ntsLib::startPushDownloadContent( $fileName );
echo $out;
exit;
?>