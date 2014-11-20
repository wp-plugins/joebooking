<?php
$now = time();
$t = $NTS_VIEW['t'];

$pgm =& ntsPaymentGatewaysManager::getInstance();
$pm =& ntsPaymentManager::getInstance();

$has_online = $pgm->hasOnline();
$has_offline = $pgm->hasOffline();

$conf =& ntsConf::getInstance();
$canCancel = $conf->get('customerCanCancel');
$canReschedule = $conf->get('customerCanReschedule');
$canReschedule = 0;

$aam =& ntsAccountingAssetManager::getInstance();
$am =& ntsAccountingManager::getInstance();

$grand_total_amount = 0;
$grand_prepay_amount = 0;
$grand_paid_amount = 0;
$grand_balance_count = 0;
$grand_due_amount = 0;

if( $group_ref )
{
	require( dirname(__FILE__) . '/_header_group.php' );
}
else
{
	require( dirname(__FILE__) . '/_header_browse.php' );
}
?>

<?php if( ! $objects ) : ?>
	<p>
	<?php echo M('None'); return; ?>
	</p>
<?php endif; ?>

<ul class="list-unstyled">
<?php foreach( $objects as $a ) : ?>
	<?php
	$t->setTimestamp( $a->getProp('starts_at') );
	$dateView = $t->formatDateFull();
	$dateView = $t->getMonthName() . ' ' . $t->getYear();
	?>
	<?php if( ! isset($datesShown[$dateView]) ) : ?>
		<li>
			<h3><?php echo $dateView; ?></h3>
		</li>
		<?php $datesShown[$dateView] = 1; ?>
	<?php endif; ?>

	<?php 
	require( dirname(__FILE__) . '/_index_one.php' );
	?>
<?php endforeach; ?>
</ul>

<?php
if( $group_ref )
{
	require( dirname(__FILE__) . '/_footer_group.php' );
}
else
{
//	require( dirname(__FILE__) . '/_header_browse.php' );
}
?>