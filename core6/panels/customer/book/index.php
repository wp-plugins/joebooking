<?php
$t = $NTS_VIEW['t'];
$current_user = ntsLib::getCurrentUser();
if( ! isset($locations) )
	$locations = array();
if( ! isset($resources) )
	$resources = array();
if( ! isset($services) )
	$services = array();
?>

<?php require( dirname(__FILE__) . '/_index_subheader.php' ); ?>

<?php if( (! $locations) OR (! $resources) OR (! $services) ) : ?>
	<?php require( dirname(__FILE__) . '/_index_not_available.php' ); ?>
	<?php return; ?>
<?php endif; ?>

<?php require( dirname(__FILE__) . '/_index_selectors.php' ); ?>