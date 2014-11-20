<?php
$session = new ntsSession;
$prepay = $session->userdata('prepay');

$grand_total_amount = 0;
$grand_prepay_amount = 0;
$grand_paid_amount = 0;
$grand_balance_count = 0;
$grand_due_amount = 0;

$pm =& ntsPaymentManager::getInstance();
foreach( $objects as $object )
{
	$paid_amount = $object->getPaidAmount();
	$price = $object->getCost();
	$grand_total_amount += $price;
	$grand_due_amount += ($price - $paid_amount);

	$app = $object->getByArray();
	$default_prepay = $pm->getPrepayAmount( $app );

	$prepay_amount = isset($prepay[$object->getId()]) ? $prepay[$object->getId()] : $default_prepay;
	if( is_array($prepay_amount) ) // asset
	{
		if( ($price - $paid_amount) > 0 )
		{
			$grand_balance_count++;
		}
		continue;
	}

	$prepay_amount = $prepay_amount - $paid_amount;
	if( $prepay_amount < 0 )
		$prepay_amount = 0;

	if( $has_online )
	{
		$grand_prepay_amount += $prepay_amount;
	}
	$grand_paid_amount += $paid_amount;
}
reset( $objects );
?>

<div class="page-header">
	<h2>
	<?php if( $grand_prepay_amount OR $grand_balance_count ) : ?>
		<?php echo M('Payment Required'); ?>
	<?php elseif( $grand_paid_amount ) : ?>
		<?php if( $grand_paid_amount >= $grand_total_amount ) : ?>
			<?php echo M('Fully Paid'); ?>
		<?php else : ?>
			<?php echo M('Partially Paid'); ?>
		<?php endif; ?>
	<?php else : ?>
		<?php echo M('Status'); ?>
	<?php endif; ?>
	</h2>
</div>