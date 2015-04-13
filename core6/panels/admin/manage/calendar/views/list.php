<?php
$t = $NTS_VIEW['t'];

$ress = ntsLib::getVar( 'admin::ress' );
$ress2 = ntsLib::getVar( 'admin::ress2' );

global $_NTS;
$viewstats = $_NTS['REQ']->getParam('viewstats');
if( $viewstats )
{
	require( dirname(__FILE__) . '/_stats.php' );
	return;
}

$appEdit = ntsLib::getVar( 'admin/manage:appEdit' );
$ress = ntsLib::getVar( 'admin::ress' );
$can_add = ( $appEdit && array_intersect($ress, $appEdit) ) ? TRUE : FALSE;

if( ! isset($show_control) )
	$show_control = TRUE;
?>
<?php if( $show_control ) : ?>
	<?php require( dirname(__FILE__) . '/_control.php' ); ?>
<?php elseif( $can_add && (! $apps) ) : ?>
	<?php require( dirname(__FILE__) . '/_add_link.php' ); ?>
<?php endif; ?>

<?php if( $show_control ) : ?>
	<h2><?php echo $t->formatDateRange($start_date, $end_date); ?></h2>
<?php endif; ?>

<?php if( ! $apps ) : ?>
	<?php if( ! isset($customer_id) ) : ?>
		<p>
		<?php echo M('None'); ?>
		</p>
	<?php endif; ?>
	<?php return; ?>
<?php endif; ?>
<?php require( dirname(__FILE__) . '/_bulk_actions.php' ); ?>
<?php foreach( $apps as $date => $day_apps ) : ?>
	<?php 
	if( ! $day_apps )
		continue;
	$split_by = ( strlen($date) == 6 ) ? 'month' : 'day';
	?>
	<?php if( $split_by == 'month' ) : ?>
		<?php $t->setMonthDb( $date ); ?>
		<h4>
			<?php echo $t->getMonthName(); ?> <small><?php echo $t->getYear(); ?></small>
		</h4>
		<hr>
	<?php else : ?>
		<?php $t->setDateDb( $date ); ?>
		<h4>
			<a href="<?php echo ntsLink::makeLink('-current-', '', array('range' => 'day', 'display' => 'calendar', 'start' => $date)); ?>">
				<?php echo $t->formatWeekdayShort(); ?> <small><?php echo $t->formatDate(); ?></small>
			</a>
		</h4>
		<hr>
	<?php endif; ?>
	<div class="hc-target" data-src="<?php echo ntsLink::makeLink('-current-', 'period', array('period' => $date)); ?>">
		<?php
		$view = array(
			'labels'	=> $labels,
			'apps'		=> $day_apps,
			'date'		=> $date,
			);
		echo $this->render_file(
			dirname(__FILE__) . '/period_list.php',
			$view
			);
		?>
	</div>
<?php endforeach; ?>