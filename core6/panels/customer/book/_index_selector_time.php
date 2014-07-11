<?php
$t = $NTS_VIEW['t'];
?>
<?php if( $requested['time'] ) : ?>
	<?php
	$t->setTimestamp( $requested['time'] );
	?>

	<div class="alert alert-default-o">
		<a class="close text-danger" title="<?php echo M('Reset'); ?>" href="<?php echo ntsLink::makeLink('-current-', '', array('time' => '-reset-')); ?>">
			<i class="fa fa-times text-danger"></i>
		</a>

		<ul class="list-unstyled collapse-panel">
			<li>
				<i class="fa fa-calendar fa-fw"></i> <?php echo $t->formatDateFull(); ?>, <i class="fa fa-clock-o fa-fw"></i> <?php echo $t->formatTime(); ?>
			</li>
		</ul>
	</div>

<?php else : ?>

	<div class="collapse-panel panel panel-group panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a href="#" data-toggle="collapse-next" class="display-block">
					<?php echo M('Time'); ?> <span class="caret"></span>
				</a>
			</h4>
		</div>

		<div class="panel-collapse collapse<?php echo $this_collapse; ?>">
			<div class="panel-body nts-ajax-container" style="display: block;">
				<?php require( dirname(__FILE__) . '/_index_time_view.php' ); ?>
			</div>
		</div>
	</div>

<?php endif; ?>