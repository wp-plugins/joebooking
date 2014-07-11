<?php
$conf =& ntsConf::getInstance();
$currentLicense = $conf->get('licenseCode');
$checkUrl2 = ntsLib::checkLicenseUrl();
?>
<?php
echo ntsForm::wrapInput(
	M('License Code'),
	$this->buildInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'licenseCode',
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> M('Required field'),
				),
			)
		)
	);
?>

<?php if( $currentLicense ) : ?>
<script language="JavaScript" type="text/javascript" src="<?php echo $checkUrl2; ?>">
</script>
<script language="JavaScript" type="text/javascript">
var myWrapper = ntsLicenseStatus ? "<span class='text-success'>" : "<span class='text-danger'>";
document.write( myWrapper )
document.write( ntsLicenseText );
document.write( '</span>' )
</script>
<?php endif; ?>

<?php echo $this->makePostParams('-current-', 'update'); ?>

<?php
echo ntsForm::wrapInput(
	'',
	'<input class="btn btn-default" type="submit" value="' . M('Save') . '">'
	);
?>