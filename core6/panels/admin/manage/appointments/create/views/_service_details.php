<?php
$session = new ntsSession;
$coupon = $session->userdata('coupon');

$pm =& ntsPaymentManager::getInstance();

$duration = $obj->getProp('duration');
$duration2 = $obj->getProp('duration2');
$lead_out = $obj->getProp('lead_out');
$duration_view = ntsTime::formatPeriodShort($duration);

if( $duration2 ){
	$duration_view .= ' + ' . ntsTime::formatPeriodShort($duration2);
}

if( $lead_out ){
	$duration_view .= ' + ' . ntsTime::formatPeriodShort($lead_out);
}

$price_view = '';
if( ! isset($a) )
	$a = array();
$a['service_id'] = $obj->getId();
$base_amount = $pm->getBasePrice( $a );
$total_amount = $pm->getPrice( $a, $coupon );

if( $base_amount ){
	if( $base_amount != $total_amount ){
		$price_view = '<span class="text-muted" style="text-decoration: line-through;">' . ntsCurrency::formatPrice($base_amount) . '</span>' . ' ' . ntsCurrency::formatPrice($total_amount);
	}
	else {
		$price_view = ntsCurrency::formatPrice($total_amount);
	}
}
?>
<ul class="list-inline list-separated">
	<li>
		<span class="btn btn-default-o btn-condensed">
			<i class="fa fa-clock-o"></i> <?php echo $duration_view; ?>
		</span>
	</li>
	<?php if( $price_view ) : ?>
		<li>
			<span class="btn btn-default-o btn-condensed">
				<?php echo $price_view; ?>
			</span>
		</li>
	<?php endif; ?>
</ul>
