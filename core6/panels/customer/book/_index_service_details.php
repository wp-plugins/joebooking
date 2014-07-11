<?php
$session = new ntsSession;
$coupon = $session->userdata('coupon');

$pm =& ntsPaymentManager::getInstance();

$description = $obj->getProp('description');
$duration = $obj->getProp('duration');
$lead_out = $obj->getProp('lead_out');
$duration_view = ntsTime::formatPeriodShort($duration);

$price_view = '';
if( ! isset($this_a) )
	$this_a = array();
$this_a['service_id'] = $obj->getId();
$base_amount = $pm->getBasePrice( $this_a );
$total_amount = $pm->getPrice( $this_a, $coupon );

if( $base_amount )
{
	if( $base_amount != $total_amount )
	{
		$price_view = '<span class="text-muted" style="text-decoration: line-through;">' . ntsCurrency::formatPrice($base_amount) . '</span>' . ' ' . ntsCurrency::formatPrice($total_amount);
	}
	else
	{
		$price_view = ntsCurrency::formatPrice($total_amount);
	}
}
?>
<ul class="list-unstyled list-separated">
	<li>
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
	</li>

	<?php if( strlen($description) ) : ?>
		<li>
			<?php echo $description; ?>
		</li>
	<?php endif; ?>
</ul>