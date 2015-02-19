<div class="row">
<div class="col-sm-2">
	<div class="list-group">
		<a class="list-group-item" href="<?php echo ntsLink::makeLink('-current-', 'make' ); ?>"><?php echo M('Download'); ?></a>
		<a class="list-group-item" href="<?php echo ntsLink::makeLink('-current-/upload', '' ); ?>"><?php echo M('Restore'); ?></a>
	</div>
</div>

<div class="col-sm-10">
<?php
$conf =& ntsConf::getInstance();
$params = array(
	'remindOfBackup',
	);
$default = array();
reset( $params );
foreach( $params as $p ){
	$default[ $p ] = $conf->get( $p );
	}
$ff =& ntsFormFactory::getInstance();
$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile, $default );
$form->display();
?>
</div>

</div>

<?php
$ri = ntsLib::remoteIntegration();
?>

<?php if( $ri == 'wordpress' ) : ?>
	<p class="text-small">
		<a href="<?php echo ntsLink::makeLink('-current-', 'makeown' ); ?>">Download backup file to import into standalone version</a>
	<p>
<?php endif; ?>