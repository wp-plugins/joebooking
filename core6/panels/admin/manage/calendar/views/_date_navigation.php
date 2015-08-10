<?php
$t->setDateDb( $start_date );
$start_view = ('day' == $range) ? $t->formatDateFull() : $t->formatDate();
$month_view = $t->getMonthName() . ' ' . $t->getYear();
$t->modify( '-1 day' );
$prev_date = $t->formatDate_Db();

$t->setDateDb( $end_date );
$end_view = $t->formatDate();
$t->modify( '+1 day' );
$next_date = $t->formatDate_Db();

$prev_date_view = '';
$next_date_view = '';

$nav_title = '';
switch( $range )
{
	case 'day':
	case 'dayloc':
		$nav_title = $start_view;
		if( $end_date > $start_date )
		{
			$nav_title = $t->formatDateRange( $start_date, $end_date );
		}
		$tm2 = ntsLib::getVar( 'admin::tm2' );

		$t->setDateDb( $start_date );
		$t->modify( '-1 day' );
		$temp_start_date = $t->formatDate_Db();
		$prev_active_dates = $tm2->getDatesWithSomething( $temp_start_date, $how_many_days, 1 );
		$prev_date = $prev_active_dates ? $prev_active_dates[0] : '';

		$t->setDateDb( $end_date );
		$t->modify( '+1 day' );
		$temp_end_date = $t->formatDate_Db();
		$next_active_dates = $tm2->getDatesWithSomething( $temp_end_date, $how_many_days );
		$next_date = $next_active_dates ? $next_active_dates[0] : '';

		if( $prev_date )
		{
//			$t->setDateDb( $prev_date );
//			$prev_date_view = $t->formatDateFull();
			$prev_date_view = $t->formatDateRange( $prev_active_dates[0], $prev_active_dates[count($prev_active_dates)-1] );
		}
		if( $next_date )
		{
//			$t->setDateDb( $next_date );
//			$next_date_view = $t->formatDateFull();
			$next_date_view = $t->formatDateRange( $next_active_dates[0], $next_active_dates[count($next_active_dates)-1] );
		}
		break;


	case 'week':
		$nav_title = $start_view . ' - ' . $end_view;
		$nav_title = $t->formatDateRange( $start_date, $end_date );

		$t->setDateDb( $start_date );
		$t->modify( '-1 week' );
		$temp_start_date = $t->formatDate_Db();
		$t->modify( '+6 days' );
		$temp_end_date = $t->formatDate_Db();
		$prev_date_view = $t->formatDateRange( $temp_start_date, $temp_end_date );

		$t->setDateDb( $start_date );
		$t->modify( '+1 week' );
		$temp_start_date = $t->formatDate_Db();
		$t->modify( '+6 days' );
		$temp_end_date = $t->formatDate_Db();
		$next_date_view = $t->formatDateRange( $temp_start_date, $temp_end_date );

		break;

	case 'month':
		$nav_title = $month_view;

		$t->setDateDb( $start_date );
		$t->modify( '-1 month' );
		$prev_date_view = $t->getMonthName() . ' ' . $t->getYear();
		$t->modify( '+2 months' );
		$next_date_view = $t->getMonthName() . ' ' . $t->getYear();

		break;
}
?>

<ul class="list-inline">
	<?php if( $prev_date ) : ?>
		<li>
			<a class="btn btn-default" href="<?php echo ntsLink::makeLink('-current-', '', array('start' => $prev_date)); ?>" title="<?php echo $prev_date_view; ?>">
				&lt;&lt;
			</a>
		</li>
	<?php endif; ?>
	<li class="active">
		<a class="btn btn-success text-center" style="width: 10em; display: block; white-space: nowrap; overflow: hidden;" href="<?php echo ntsLink::makeLink('-current-', '', array('start' => $start_date)); ?>" title="<?php echo $nav_title; ?>">
			<?php echo $nav_title; ?>
		</a>
	</li>
	<?php if( $next_date ) : ?>
		<li>
			<a class="btn btn-default" href="<?php echo ntsLink::makeLink('-current-', '', array('start' => $next_date)); ?>" title="<?php echo $next_date_view; ?>">
				&gt;&gt;
			</a>
		</li>
	<?php endif; ?>

	<li class="divider"></li>
	<li>
		<a target="_blank" class="btn btn-default btn-sm" href="<?php echo ntsLink::makeLink('-current-', '', array('view-mode' => 'print')); ?>">
			<i class="fa fa-print"></i> <span class="hidden-xs"><?php echo M('Print'); ?></span>
		</a>
	</li>
</ul>