<ul class="nav nav-pills" style="margin: 0 0;">
	<li title="<?php echo $t->formatPeriod( $stats['duration'] ); ?>">
		<a><i class="fa fa-clock-o"></i> <?php echo $t->formatPeriodShort( $stats['duration'], 'hour' ); ?></a>
	</li>
	<?php if( isset($stats['status_count']['active']) ) : ?>
		<?php foreach( $stats['status_count']['active'] as $status => $count ) : ?>
			<?php
			list( $message, $class ) = ntsAppointment::_statusText( $status, 0 );
			?>
			<li>
				<a title="<?php echo $message; ?>">
					<span class="label label-lg label-<?php echo $class; ?>">
						<?php echo $count; ?>
					</span>
				</a>
			</li>
		<?php endforeach; ?>
	<?php endif; ?>

	<?php if( isset($stats['status_count']['completed']) ) : ?>
		<?php foreach( $stats['status_count']['completed'] as $status => $count ) : ?>
			<?php
			list( $message, $class ) = ntsAppointment::_statusText( 0, $status );
			?>
			<li>
				<a title="<?php echo $message; ?>">
					<span class="label label-lg label-<?php echo $class; ?>">
						<?php echo $count; ?>
					</span>
				</a>
			</li>
		<?php endforeach; ?>
	<?php endif; ?>
</ul>