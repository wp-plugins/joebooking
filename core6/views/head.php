<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<?php require( dirname(__FILE__) . '/title.php' ); ?>
<title><?php echo ntsView::getTitle(); ?></title>

<?php
$web_dir = defined('NTS_ROOT_WEBDIR') ? NTS_ROOT_WEBDIR : ntsLib::webDirName( ntsLib::getFrontendWebpage() );
if( defined('NTS_DEVELOPMENT') && NTS_DEVELOPMENT )
{
	$happ_web_dir = 'http://localhost';
}
else
{
	$happ_web_dir = $web_dir . '/core6';
}
require( dirname(__FILE__) . '/../assets/files.php' );
?>
<?php foreach( $css_files as $f ) : ?>
	<?php
	$file = is_array($f) ? $f[0] : $f;
	$full_file = (substr($file, 0, strlen('happ/')) == 'happ/') ? $happ_web_dir . '/' . $file : $web_dir . '/' . $file;
	?>
	<?php if( is_array($f) ) : ?>
		<!--[if <?php echo $f[1]; ?>]>
		<link rel="stylesheet" type="text/css" href="<?php echo $full_file; ?>" />
		<![endif]-->
	<?php else : ?>
		<link rel="stylesheet" type="text/css" href="<?php echo $full_file; ?>" />
	<?php endif; ?>
<?php endforeach; ?>

<?php foreach( $js_files as $f ) : ?>
	<?php
	$file = is_array($f) ? $f[0] : $f;
	$full_file = (substr($file, 0, strlen('happ/')) == 'happ/') ? $happ_web_dir . '/' . $file : $web_dir . '/' . $file;
	?>
	<?php if( is_array($f) ) : ?>
		<!--[if <?php echo $f[1]; ?>]>
		<script language="JavaScript" type="text/javascript" src="<?php echo $full_file; ?>"></script>
		<![endif]-->
	<?php else : ?>
		<script language="JavaScript" type="text/javascript" src="<?php echo $full_file; ?>"></script>
	<?php endif; ?>
<?php endforeach; ?>
</head>
<body>