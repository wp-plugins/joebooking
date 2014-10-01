<?php
$t = $NTS_VIEW['t'];
$session = new ntsSession;
$apps = $session->userdata( 'apps' );
?>

<div class="page-header">
	<h2>
	<?php if( $apps && (count($apps) > 1) ) : ?>
		<?php echo M('Confirm Appointments'); ?>
	<?php else : ?>
		<?php echo M('Confirm Appointment'); ?>
	<?php endif; ?>
	</h2>
</div>

<?php require( dirname(__FILE__) . '/../_index_confirm.php' ); ?>

<p>
<div class="panel-group">
	<div class="collapse-panel panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a href="#" data-toggle="collapse-next">
					<?php echo M('Already Registered?'); ?>
				</a>
			</h4>
		</div>

		<div class="panel-collapse collapse">
			<div class="panel-body">
				<?php echo $NTS_VIEW['form_login']->display(); ?>

				<div class="collapse-panel">
					<?php
					echo ntsForm::wrapInput(
						'',
						'<a href="#" data-toggle="collapse-next">' . M('Forgot Your Password') . '?</a>'
						);
					?>
					<div class="collapse">
						<?php echo $NTS_VIEW['form_forgot']->display(); ?>
					</div>
				</div>

			</div>
		</div>
	</div>

	<div class="collapse-panel panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a href="#" data-toggle="collapse-next">
					<?php echo M('First Time?'); ?>
				</a>
			</h4>
		</div>

		<div class="panel-collapse collapse<?php if( ! $NTS_VIEW['form_register']->valid ){echo ' in';} ?>">
			<div class="panel-body">
				<?php echo $NTS_VIEW['form_register']->display(); ?>
			</div>
		</div>	
	</div>
</div>
