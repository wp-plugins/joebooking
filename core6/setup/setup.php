<?php
// take notice
$trackCode = '';
$app = ntsLib::getAppProduct();
$slug = $app;

if( substr($slug, -strlen('_salon_pro')) == '_salon_pro' )
{
	$slug = substr($slug, 0, -strlen('_salon_pro'));
}
if( substr($slug, -strlen('_pro')) == '_pro' )
{
	$slug = substr($slug, 0, -strlen('_pro'));
}

$pageslug = $slug;
if( $slug == 'hitappoint' )
	$pageslug = $pageslug . '6';

$track_setup = '';
switch( $slug )
{
	case 'hitappoint':
		$track_setup = '1:2';
		break;
	case 'joebooking':
		$track_setup = '15:2';
		break;
}
if( $track_setup )
{
	list( $track_site_id, $track_goal_id ) = explode( ':', $track_setup );
}

$trackCode =<<<EOT
<img src="http://www.fiammante.com/piwik/piwik.php?idsite=1&amp;rec=1" style="border:0" alt="" />
EOT;

$trackCode =<<<EOT
<script type="text/javascript">
  var _paq = _paq || [];
  _paq.push(["trackPageView"]);
  _paq.push(["enableLinkTracking"]);

  (function() {
    var u=(("https:" == document.location.protocol) ? "https" : "http") + "://www.fiammante.com/piwik/";
    _paq.push(["setTrackerUrl", u+"piwik.php"]);
    _paq.push(["setSiteId", "$track_site_id"]);
	_paq.push(['trackGoal', $track_goal_id]);
    var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0]; g.type="text/javascript";
    g.defer=true; g.async=true; g.src=u+"piwik.js"; s.parentNode.insertBefore(g,s);
  })();
</script>
EOT;
?>
<html>
<head>
<title><?php echo ucfirst($slug); ?> Installation</title>

<STYLE TYPE="text/css">
LABEL {
	DISPLAY: block;
	PADDING: 0.2em 0.2em;
	MARGIN: 0.2em 0.2em 0.5em 0.2em;
	LINE-HEIGHT: 1em;
	overflow: auto;
	}
LABEL SPAN {
	FONT-WEIGHT: bold;
	DISPLAY: block;
	FLOAT: left;
	WIDTH: 12em;
	}
.success {
	FONT-WEIGHT: bold;
	COLOR: #00BB00;
	}
</STYLE>
</head>
<body>
<h1><?php echo ucfirst($slug); ?> Installation</h1>
<?php
$step = (isset($_REQUEST['step']) ) ? $_REQUEST['step'] : 'start';

//wordpress?
global $table_prefix;
$wordpress = false;
if( isset($table_prefix) && $table_prefix ){
	$tbPrefix = $table_prefix . 'ha_';
	$wordpress = true;
	}
else {
	$tbPrefix = NTS_DB_TABLES_PREFIX;
	$wordpress = false;
	}
?>

<?php if( $step == 'start' ) : ?>
<?php
// check if there's an older 5.x install
require( dirname(__FILE__) . '/../init_db.php' );

$oldPrefix = $tbPrefix . 'v5_';
$oldWrapper = new ntsMysqlWrapper(
	$db['hostname'],
	$db['username'],
	$db['password'],
	$db['database'],
	$oldPrefix
	);

$oldWrapper->init();
$oldTables = $oldWrapper->getTablesInDatabase();
$currentVersion = 0;
if( in_array('conf', $oldTables) )
{
	// conf table exists, search installed version
	$sql = 'SELECT value FROM {PRFX}conf WHERE NAME="currentVersion"';
	$result = $oldWrapper->runQuery($sql);
	if( $i = $result->fetch() )
	{
		$currentVersion = $currentVersion = $i['value'];
	}
}

$upgrade_from4 = 0;

if( ! $currentVersion )
{
	$oldPrefix = substr($tbPrefix, 0, -1) . '45_';

	$oldWrapper = new ntsMysqlWrapper(
		$db['hostname'],
		$db['username'],
		$db['password'],
		$db['database'],
		$oldPrefix
		);

	$oldWrapper->init();
	$oldTables = $oldWrapper->getTablesInDatabase();
	$currentVersion = 0;
	if( in_array('conf', $oldTables) )
	{
		// conf table exists, search installed version
		$sql = 'SELECT value FROM {PRFX}conf WHERE NAME="currentVersion"';
		$result = $oldWrapper->runQuery($sql);
		if( $i = $result->fetch() )
		{
			$currentVersion = $currentVersion = $i['value'];
			$upgrade_from4 = 1;
		}
	}
}
?>
<?php if( $currentVersion ) : ?>
	<h2>Upgrade</h2>
	<p>
	Upgrade from <b><?php echo $currentVersion; ?></b>
<?php
$upgradeLink = '?step=upgrade6';
if( $upgrade_from4 )
{
	$upgradeLink .= '&from4=1';
}

$app = ntsLib::getAppProduct();
$slug = $app;
if( substr($slug, -strlen('_salon_pro')) == '_salon_pro' )
{
	$slug = substr($slug, 0, -strlen('_salon_pro'));
}
if( substr($slug, -strlen('_pro')) == '_pro' )
{
	$slug = substr($slug, 0, -strlen('_pro'));
}

$pageslug = $slug;
if( $slug == 'hitappoint' )
	$pageslug = $pageslug . '6';

if( $wordpress )
	$upgradeLink .= '&page=' . $pageslug;
?>
	<a href="<?php echo $upgradeLink; ?>">Click here to import data from version <?php echo $currentVersion; ?></a>

<?php 	if( ! $wordpress ) : ?>	
	<p>or
	<h2>New Install</h2>
<?php	endif; ?>

<?php elseif( $wordpress ) : ?>
	<?php
	$app = ntsLib::getAppProduct();
	$slug = $app;
	if( substr($slug, -strlen('_pro')) == '_pro' )
	{
		$slug = substr($slug, 0, -strlen('_pro'));
	}

	global $NTS_SETUP_ADMINS;
	$NTS_SETUP_ADMINS = array();

	$roles = array( 'Developer', 'Administrator' );
	reset( $roles );
	foreach( $roles as $role )
	{
		$wp_user_search = new WP_User_Search( '', '', $role);
		$NTS_SETUP_ADMINS = array_merge( $NTS_SETUP_ADMINS, $wp_user_search->get_results() );
	}

	require( dirname(__FILE__) . '/create-database.php' );
	require( dirname(__FILE__) . '/populate.php' );	
	$targetLink = '?page=' . $pageslug;
	?>

	<p>
	Installation ok
	</p>
	<?php if( defined('NTS_DEVELOPMENT') ) : ?>
		TRACKING <?php echo $track_site_id; ?>:<?php echo $track_goal_id; ?>
	<?php else : ?>
		<?php echo $trackCode; ?>
	<?php endif; ?>

	<META http-equiv="refresh" content="5;URL=<?php echo $targetLink; ?>">
	<p>
	Your <a href="<?php echo $targetLink; ?>">online appointment scheduler</a> is ready.
	</p>
<?php endif; ?>

<?php if( ! $wordpress ) : ?>	
<?php	require( dirname(__FILE__) . '/form.php' ); ?>
<?php endif; ?>

<?php elseif( $step == 'upgrade6' ): ?>
<?php	require( dirname(__FILE__) . '/upgrade6.php' ); ?>

		<span class="success">Database tables created, data imported from version 5.x</span>
		<p>
		<?php
		$targetLink = 'index.php';
		if( $wordpress ){
			$app = ntsLib::getAppProduct();
			$slug = $app;
			$targetLink = '?page=' . $pageslug;
			}
		$checkUrl2 = ntsLib::checkLicenseUrl();
		?>
		Your <a href="<?php echo $targetLink; ?>">online appointment scheduler</a> is ready.

<script language="JavaScript" type="text/javascript" src="<?php echo $checkUrl2; ?>"></script>

<?php elseif( $step == 'create' ): ?>
<?php	require( NTS_APP_DIR . '/setup/create-database.php' ); ?>
<?php	require( dirname(__FILE__) . '/populate.php' ); ?>

<?php
$targetLink = 'index.php';
if( $wordpress ){
	$targetLink = '?page=' . $pageslug;
	}

$checkUrl2 = ntsLib::checkLicenseUrl();
?>
		<span class="success">Database tables created, admin account configured, sample data populated</span>
		<p>
		Your <a href="<?php echo $targetLink; ?>">online appointment scheduler</a> is ready.
		</p>

<script language="JavaScript" type="text/javascript" src="<?php echo $checkUrl2; ?>"></script>

<?php if( defined('NTS_DEVELOPMENT') ) : ?>
	TRACKING <?php echo $track_site_id; ?>:<?php echo $track_goal_id; ?>
<?php else : ?>
<?php echo $trackCode; ?>
<?php endif; ?>

<?php endif; ?>

</body>
</html>