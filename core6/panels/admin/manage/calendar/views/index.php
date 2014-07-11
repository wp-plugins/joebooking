<?php
$t = $NTS_VIEW['t'];

global $_NTS;
$viewstats = $_NTS['REQ']->getParam('viewstats');
if( $viewstats )
{
	require( dirname(__FILE__) . '/_stats.php' );
	return;
}

$t->setDateDb( $start_date );
$month_matrix = $t->getMonthMatrix( $end_date );

$appEdit = ntsLib::getVar( 'admin/manage:appEdit' );
$ress = ntsLib::getVar( 'admin::ress' );
$can_add = ( $appEdit && array_intersect($ress, $appEdit) ) ? TRUE : FALSE;
?>
<?php require( dirname(__FILE__) . '/_control.php' ); ?>

<?php
$container_class = 'hc_cal';
switch( $range )
{
	case 'month':
		$per_row = 7;
		$col_class = 'col-sm-1';
		break;

	case 'week':
		$per_row = 4;
		$col_class = 'col-sm-3';
		break;
}
?>

<?php require( dirname(__FILE__) . '/_bulk_actions.php' ); ?>

<?php foreach( $month_matrix as $week ) : ?>
	<div class="<?php echo $container_class; ?>">
		<div class="row">
		<?php $in_row_count = 0; ?>

		<?php foreach( $week as $date ) : ?>
			<?php $in_row_count++; ?>

			<?php if( $in_row_count > $per_row ) : ?>
				</div>
				<div class="row">
				<?php $in_row_count = 0; ?>
			<?php endif; ?>

			<div class="<?php echo $col_class; ?>">
				<?php if( ($date >= $start_date) && ($date <= $end_date) ) : ?>
					<div class="thumbnail">
						<?php $t->setDateDb( $date ); ?>
							<h4>
								<a href="<?php echo ntsLink::makeLink('-current-', '', array('range' => 'day', 'start' => $date)); ?>" style="color: inherit;">
									<?php echo $t->formatWeekdayShort(); ?> <small><?php echo $t->formatDate(); ?></small>
								</a>
							</h4>

						<div class="hc-target" data-src="<?php echo ntsLink::makeLink('-current-', 'period', array('period' => $date)); ?>">
							<?php
							$view = array(
								'labels'	=> $labels,
								'apps'		=> isset($apps[$date]) ? $apps[$date] : array(),
								'date'		=> $date,
								);
							echo $this->render_file(
								dirname(__FILE__) . '/period.php',
								$view
								);
							?>
						</div>
						<?php if( $can_add ) : ?>
							<?php
							$btn_class = $dates[$date][2] ? 'btn-success2' : 'alert-archive';
							$add_params = array(
								'cal'			=> $date,
								'nts-filter'	=> '-reset-',
								);
							if( isset($current_filter['r']) )
							{
								$add_params['resource_id'] = $current_filter['r'];
							}
							if( isset($current_filter['l']) )
							{
								$add_params['location_id'] = $current_filter['l'];
							}
							$add_link = ntsLink::makeLink(
								'admin/manage/appointments/create',
								'',
								$add_params
								);
							?>
							<hr>
							<a href="<?php echo $add_link; ?>" class="btn btn-default btn-sm btn-block <?php echo $btn_class; ?>">
								<i class="fa fa-plus"></i> <?php echo M('Appointment'); ?>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>

		</div>
	</div>
<?php endforeach; ?>