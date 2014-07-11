<?php
//_print_r( $coupon_promotions );
?>
<div class="dropdown">
	<?php if( $coupon ) : ?>
		<div class="btn-group">
			<span class="btn btn-default" title="<?php echo $coupon; ?>">
				<?php echo $coupon; ?>
			</span>
			<a class="btn btn-default" href="<?php echo ntsLink::makeLink('-current-', 'coupon', array('coupon' => '') ); ?>">
				<span class="text-danger close2"><strong>&times;</strong></span>
			</a>
		</div>
	<?php else : ?>
		<a class="dropdown-toggle" data-toggle="dropdown" href="#" title="<?php echo M('Coupon Code'); ?>">
			<?php echo M('Coupon Code'); ?>?
		</a>
	<?php endif; ?>

	<ul class="dropdown-menu dropdown-menu-hori">
	<?php foreach( $coupon_promotions as $pro ) : ?>
		<?php
		$this_coupons = $pro->getCoupons();
		?>
		<li class="dropdown-header">
			<?php echo ntsView::objectTitle($pro); ?>
		</li>
		<?php foreach( $this_coupons as $cpn ) : ?>
			<?php
			$code = $cpn->getProp('code');
			$objView = $code;
			?>
			<li title="<?php echo $objView; ?>">
				<a href="<?php echo ntsLink::makeLink('-current-', 'coupon', array('coupon' => $code) ); ?>">
					<span class="label label-available">&nbsp;</span> <?php echo $objView; ?>
				</a>
			</li>
		<?php endforeach; ?>
	<?php endforeach; ?>
	</ul>
</div>