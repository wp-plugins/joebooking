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

$nav_title = '';
switch( $range )
{
	case 'day':
	case 'dayloc':
		$nav_title = $start_view;
		break;
	case 'week':
		$nav_title = $start_view . ' - ' . $end_view;
		$nav_title = $t->formatDateRange( $start_date, $end_date );
		break;
	case 'month':
		$nav_title = $month_view;
		break;
}
?>

<ul class="pagination">
	<li>
		<a href="<?php echo ntsLink::makeLink('-current-', '', array('start' => $prev_date)); ?>">
			&lt;&lt;
		</a>
	</li>
	<li class="active">
		<a class="text-center" style="width: 10em; display: block; white-space: nowrap; overflow: hidden;" href="<?php echo ntsLink::makeLink('-current-', '', array('start' => $start_date)); ?>" title="<?php echo $nav_title; ?>">
			<?php echo $nav_title; ?>
		</a>
	</li>
	<li>
		<a href="<?php echo ntsLink::makeLink('-current-', '', array('start' => $next_date)); ?>">
			&gt;&gt;
		</a>
	</li>
</ul>