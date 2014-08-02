<?php
$now = time();
$check = $now - 1 * 60 * 60;
$conf =& ntsConf::getInstance();

$installedVersion = $conf->get('currentVersion');
$installedVersionNumber = ntsLib::parseVersion( $installedVersion );

$checkUrl2 = ntsLib::checkLicenseUrl();

$checkLicense = 0;
$homeCall = 0;
if( (! isset($_SESSION['home_call'])) || $_SESSION['home_call'] )
{
	if( $NTS_CURRENT_USER->hasRole('admin') && (! $NTS_CURRENT_USER->isPanelDisabled('admin/conf/upgrade')) )
	{
		$checkLicense = 1;
		$homeCall = 1;
		if( defined('NTS_APP_DEVELOPER') && (! defined('NTS_DEVELOPMENT')) )
		{
			$checkLicense = 0;
		}
		elseif( defined('NTS_APP_LEVEL') && (NTS_APP_LEVEL == 'lite') )
		{
			$checkLicense = 0;
		}
	}
	else
	{
		$checkLicense = 0;
	}
	$_SESSION['home_call'] = 0;
}

$skipPanels = array('admin/conf/upgrade');
reset( $skipPanels );
foreach( $skipPanels as $sp )
{
	if( substr($_NTS['CURRENT_PANEL'], 0, strlen($sp)) == $sp )
	{
		$checkLicense = false;
		break;
	}
}
$licenseLink = ntsLink::makeLink( 'admin/conf/upgrade' );
?>

<?php if( 0 && defined('NTS_APP_LEVEL') && (NTS_APP_LEVEL == 'lite') ) : ?>
	<?php
	$appInfo = ntsLib::getAppInfo();
	$order_link = isset($appInfo['order_link']) ? $appInfo['order_link'] : 'http://www.hitappoint.com/order/';
	?>
	<div class="alert alert-success">
		Check out the <a target="_blank" href="<?php echo $order_link; ?>">Pro version</a> to get a lot more!
	</div>
<?php endif; ?>

<?php if( $checkLicense OR $homeCall ) : ?>
	<script language="JavaScript" type="text/javascript" src="<?php echo $checkUrl2; ?>">
	</script>
<?php endif; ?>

<?php if( $checkLicense ) : ?>
	<script language="JavaScript" type="text/javascript">
	if( ! ntsLicenseStatus ){
		document.write( '<div class="alert alert-danger">' );

		document.write( '<a href="<?php echo $licenseLink; ?>">' );
		document.write( ntsLicenseText );
		document.write( '</a>' );

		document.write( '</div>' );
		}

	var currentVersionNumber = 0;
	if( ntsVersion.length ){
		var myV = ntsVersion.split( '.' );
		currentVersionNumber = myV[0] + '' + myV[1] + '' + ntsZeroFill(myV[2], 2);
		}
	if( (currentVersionNumber > 0) && (currentVersionNumber > <?php echo $installedVersionNumber; ?>) ){
		document.write( '<div class="alert alert-success">' );

<?php if( $_NTS['DOWNLOAD_URL'] ) : ?>
		document.write( '<a target="_blank" href="<?php echo $_NTS['DOWNLOAD_URL']; ?>">' );
<?php endif; ?>
		document.write("New version available: " + '<b>' + ntsVersion + '</b>');
<?php if( $_NTS['DOWNLOAD_URL'] ) : ?>
		document.write( '</a>' );
<?php endif; ?>

		document.write( '</div>' );
		}
	</script>
<?php endif; ?>