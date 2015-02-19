<?php if( $grand_balance_count OR $grand_total_amount OR $grand_prepay_amount OR $grand_paid_amount ) : ?>
	<hr>
	<ul class="list-inline list-separated">

	<?php if( $grand_total_amount != $grand_prepay_amount ): ?>

		<?php if( $grand_total_amount ) : ?>
			<li>
				<?php echo M('Total'); ?>
			</li>
			<li>
				<span class="btn btn-default">
					<strong><?php echo ntsCurrency::formatPrice($grand_total_amount); ?></strong>
				</span>
			</li>
		<?php endif; ?>

		<?php if( $grand_paid_amount ) : ?>
			<li>
				<?php echo M('Paid'); ?>
			</li>
			<li>
				<span class="btn btn-default">
					<strong><?php echo ntsCurrency::formatPrice($grand_paid_amount); ?></strong>
				</span>
			</li>
		<?php endif; ?>

		<?php if( $grand_due_amount && ($grand_due_amount != $grand_total_amount) && ($grand_due_amount > 0) ) : ?>
			<li>
				<?php echo M('Total Due'); ?>
			</li>
			<li>
				<span class="btn btn-default">
					<strong><?php echo ntsCurrency::formatPrice($grand_due_amount); ?></strong>
				</span>
			</li>
		<?php endif; ?>
	<?php endif; ?>

	<?php if( $grand_prepay_amount OR $grand_balance_count ) : ?>
		<?php if( $grand_total_amount != $grand_prepay_amount ): ?>
			<?php if( $grand_prepay_amount ) : ?>
				<li>
					<?php echo M('Pay Now'); ?>
				</li>
				<li>
					<span class="btn btn-default">
						<strong><?php echo ntsCurrency::formatPrice($grand_prepay_amount); ?></strong>
					</span>
				</li>
			<?php endif; ?>
		<?php endif; ?>

		<?php
		$btn_label = array();
		if( $grand_balance_count )
		{
			$btn_label[] = M('Pay By Balance');
		}
		if( $grand_prepay_amount )
		{
			$btn_label[] = M('Pay Online') . ' ' . '<strong>' . ntsCurrency::formatPrice($grand_prepay_amount) . '</strong>';
		}
		$btn_label = join( ' &amp; ', $btn_label );
		?>
		<?php if( $grand_total_amount != $grand_prepay_amount ): ?>
			<li>
				<?php echo M('Click Here To'); ?> 
			</li>
		<?php endif; ?>
		<li>
			<a class="btn btn-success btn-lg" href="<?php echo ntsLink::makeLink('-current-', 'pay'); ?>">
				<?php echo $btn_label; ?>
			</a>
		</li>
		<?php if( $has_offline ) : ?>
			<li>
				<a class="btn btn-success-o" href="<?php echo ntsLink::makeLink('-current-', 'payoffline'); ?>">
					<?php echo $has_offline; ?>
				</a>
			</li>
		<?php endif; ?>

	<?php elseif( $has_offline && ($grand_total_amount > $grand_paid_amount) ) : ?>

		<li>
			<a class="btn btn-success btn-lg" href="<?php echo ntsLink::makeLink('-current-', 'payoffline'); ?>">
				<?php echo $has_offline; ?>
			</a>
		</li>
	<?php endif; ?>
	</ul>
<?php endif; ?>