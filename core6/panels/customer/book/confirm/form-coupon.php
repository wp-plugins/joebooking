<ul class="list-inline">
	<li>
		<?php
		echo $this->makeInput (
		/* type */
			'text',
		/* attributes */
			array(
				'id'		=> 'coupon',
				'attr'		=> array(
					'size'	=> 12,
					),
				)
			);
		?>
	</li>
	<li>
		<a href="<?php echo ntsLink::makeLink('-current-'); ?>" id="nts-apply-coupon" class="btn btn-default">
		<?php echo M('Apply'); ?>
		</a>
	</li>
</ul>