<?php
$session = new ntsSession;
$apps = $session->userdata( 'apps' );
?>
<div class="page-header">
	<div class="row">
		<div class="col-sm-8">
			<h2><?php echo M('New Appointment'); ?></h2>
		</div>

		<?php if( $apps ) : ?>
			<div class="col-sm-4 text-right">
				<?php
				$label = ( count($apps) > 1 ) ? M('Appointments') : M('Appointment');
				?>
				<a class="btn btn-default" href="<?php echo ntsLink::makeLink('customer/book/confirm'); ?>">
					<i class="fa fa-fw fa-shopping-cart"></i> <?php echo count($apps); ?> <?php echo $label; ?>
				</a>
			</div>
		<?php endif; ?>	
	</div>
</div>