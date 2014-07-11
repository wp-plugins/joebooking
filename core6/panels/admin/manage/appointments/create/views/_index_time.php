<?php
$t = $NTS_VIEW['t'];
?>
<?php if( $starts_at ) : ?>
	<?php
	echo $this->render_file(
		dirname(__FILE__) . '/_object.php',
		array(
			'obj'		=> $starts_at,
			'obj_class'	=> 'time',
			'available'	=> $available['time'],
			'this_id'	=> $starts_at,
			'all_ids'	=> $all_times,
			'per_row'	=> 0,
			'a'			=> $a,
			)
		);
	?>
<?php else : ?>
	<?php if( isset($current_time) ) : ?>
		<?php
		$t->setTimestamp( $current_time );
		$selector_label = $t->formatFull();
		$selector_label = '<i class="fa fa-fw fa-calendar"></i>' . $t->formatDateFull() . ' <i class="fa fa-fw fa-clock-o"></i>' . $t->formatTime();

		$selector_class = $current_selector_class;
		$collapse_in = ( (isset($selected['cal']) && $selected['cal']) OR (count($to_select) <= 1) ) ? ' in' : '';
		?>
		<div class="collapse-panel panel panel-group panel-<?php echo $selector_class; ?>">
			<div class="panel-heading">
				<a href="#" data-toggle="collapse-next" class="display-block">
					<?php echo $selector_label; ?> <span class="caret"></span>
				</a>

			<?php if( isset($errors) && $errors ) : ?>
				<ul class="list-unstyled">
				<?php foreach( $errors as $err_class => $err_text ) : ?>
					<i class="fa-fw fa fa-exclamation-circle text-danger"></i><?php echo $err_text; ?>
				<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			</div>
			<div class="panel-collapse collapse<?php echo $collapse_in; ?>">
				<div class="panel-body">
	<?php endif; ?>

	<div class="row" style="margin-top: 1em;">
		<div class="col-md-3 col-sm-4">
			<?php
			$cal2show = 1;
			$tm2 = ntsLib::getVar('admin::tm2');

			if( isset($current_time) )
			{
				$saveLids = $tm2->locationIds;
				$saveRids = $tm2->resourceIds;
				$saveSids = $tm2->serviceIds;

				$tm2->setLocation( $final['location_id'] );
				$tm2->setResource( $final['resource_id'] );
				$tm2->setService( $final['service_id'] );
			}

			require( dirname(__FILE__) . '/../../../prepare-calendar.php' );

			if( isset($current_time) )
			{
				$tm2->setLocation( $saveLids );
				$tm2->setResource( $saveRids );
				$tm2->setService( $saveSids );
			}

			$skipPrevLink = FALSE;
			$skipNextLink = FALSE;
			$calendarMonths = $cal2show;
			require( NTS_APP_DIR . '/helpers/calendar2.php' );
			?>
		</div>

		<div class="col-md-9 col-sm-8">
			<?php
			$t->setTimestamp($starts_at);
			?>
			<h4>
				<?php echo $t->formatDateFull( $all_times[0] ); ?>
			</h4>
			<?php
			echo $this->render_file(
				dirname(__FILE__) . '/_object.php',
				array(
					'obj'		=> $starts_at,
					'obj_class'	=> 'time',
					'available'	=> $available['time'],
					'this_id'	=> $starts_at,
					'all_ids'	=> $all_times,
					'per_row'	=> 0,
					'a'			=> $a,
					)
				);
			?>
		</div>
	</div>

	<?php if( isset($current_time) ) : ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
<?php endif; ?>