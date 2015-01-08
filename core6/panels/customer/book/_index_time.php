<?php
$t = $NTS_VIEW['t'];
$ff =& ntsFormFactory::getInstance();
$current_user = ntsLib::getCurrentUser();
$t->setDateDb( $requested_cal );
?>
<?php if( NTS_ENABLE_TIMEZONES >= 0 ) : ?>
	<p>
	<?php if( NTS_ENABLE_TIMEZONES > 0 ) : ?>
		<div class="dropdown">
			<a class="dropdown-toggle btn btn-default" data-toggle="dropdown" href="#">
				<i class="fa fa-fw fa-globe"></i> 
				<?php echo ntsTime::timezoneTitle($current_user->getTimezone()); ?> <b class="caret"></b>
			</a>
			<ul class="dropdown-menu">
				<li>
					<span>
					<?php
					$formTimezoneParams = array(
						'tz'	=> $current_user->getTimezone(),
						);
					$timezoneForm = NTS_APP_DIR . '/panels/customer/appointments/view/formTimezone';
					$formTimezone =& $ff->makeForm( $timezoneForm, $formTimezoneParams );
					$formTimezone->display();
					?>
					</span>
				</li>
			</ul>
		</div>
	<?php elseif( NTS_ENABLE_TIMEZONES == 0 ) : ?>
		<span class="btn btn-default">
			<i class="fa fa-fw fa-globe"></i> 
			<?php echo ntsTime::timezoneTitle($current_user->getTimezone()); ?>
		</span>
	<?php endif; ?>
	</p>
<?php endif; ?>

<h3>
	<?php echo $t->formatWeekdayShort(); ?>, <?php echo $t->formatDate(); ?>
</h3>

<p>
<?php if( ! $times ) : ?>
	<?php echo M('Not Available'); ?>
<?php else : ?>
	<ul class="list-inline">
	<?php foreach( $times as $ts => $slots ) : ?>
		<?php
		$t->setTimestamp( $ts );
		$link = array(
			'time'	=> $ts,
			);
		?>

		<li style="padding: 0 0;">
			<?php if( $slots ) : ?>
				<a class="nts-row-spaced nts-time-link nts-no-ajax" href="<?php echo ntsLink::makeLink('-current-', '', $link); ?>">
					<span class="btn btn-default btn-success-o btn-sm">
						<?php echo $t->formatTime(); ?>
					</span>
				</a>
			<?php else : ?>
				<a class="nts-row-spaced nts-time-link nts-ajax-loader hc-no-link" href="#">
					<span class="btn btn-archive btn-sm">
						<?php echo $t->formatTime(); ?>
					</span>
				</a>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>