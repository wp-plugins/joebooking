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

<?php
$accordion_parent = 'nts-' . ntsLib::generateRand();
$accordion_login = 'nts-' . ntsLib::generateRand();
$accordion_register = 'nts-' . ntsLib::generateRand();
?>

<p>
<div class="panel-group" id="<?php echo $accordion_parent; ?>">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a class="accordion-toggle" data-toggle="collapse" data-parent="#<?php echo $accordion_parent; ?>" href="#<?php echo $accordion_login; ?>">
				<?php echo M('Already Registered?'); ?>
				</a>
			</h4>
		</div>

		<div class="panel-collapse collapse" id="<?php echo $accordion_login; ?>">
			<div class="panel-body">
				<?php echo $NTS_VIEW['form_login']->display(); ?>

				<?php
				$accordion_parent2 = 'nts-' . ntsLib::generateRand();
				$accordion_forgot = 'nts-' . ntsLib::generateRand();
				?>
				<div id="<?php echo $accordion_parent2; ?>">
					<?php
					echo ntsForm::wrapInput(
						'',
						'<a href="#' . $accordion_forgot . '" class="accordion-toggle" data-toggle="collapse" data-parent="#' . $accordion_parent2 . '">' . M('Forgot Your Password') . '?</a>'
						);
					?>
					<div id="<?php echo $accordion_forgot; ?>" class="collapse">
						<?php echo $NTS_VIEW['form_forgot']->display(); ?>
					</div>
				</div>

			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a class="accordion-toggle" data-toggle="collapse" data-parent="#<?php echo $accordion_parent; ?>" href="#<?php echo $accordion_register; ?>">
				<?php echo M('First Time?'); ?>
				</a>
			</h4>
		</div>

		<div class="panel-collapse collapse<?php if( ! $NTS_VIEW['form_register']->valid ){echo ' in';} ?>" id="<?php echo $accordion_register; ?>">
			<div class="panel-body">
				<?php echo $NTS_VIEW['form_register']->display(); ?>
			</div>
		</div>	
	</div>
</div>
