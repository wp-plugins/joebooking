<?php
$this_per_row = 2;
$this_col_class = 'col-lg-6 col-md-6 col-sm-12 col-xs-12';
?>

<?php if( $requested['service'] OR (count($services) <= 1) ) : ?>

	<?php foreach( $services as $obj ) : ?>
		<div class="alert alert-default-o">
			<?php if( $requested['service'] ) : ?>
				<a class="close text-danger" title="<?php echo M('Reset'); ?>" href="<?php echo ntsLink::makeLink('-current-', '', array('service' => '-reset-')); ?>">
					<i class="fa fa-times text-danger"></i>
				</a>
			<?php endif; ?>
			<ul class="list-unstyled collapse-panel">
				<li>
					<a href="#" data-toggle="collapse-next" class="display-block">
					<?php echo ntsView::objectTitle( $obj, TRUE ); ?>
					</a>
				</li>
				<li class="collapse">
					<?php require( dirname(__FILE__) . '/_index_service_details.php' ); ?>
				</li>
			</ul>
		</div>
	<?php endforeach; ?>

<?php else : ?>

	<div class="collapse-panel panel panel-group panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a href="#" data-toggle="collapse-next" class="display-block">
					<?php echo M('Service'); ?> <span class="caret"></span>
				</a>
			</h4>
		</div>

		<div class="panel-collapse collapse<?php echo $this_collapse; ?>">
			<div class="panel-body">
				<ul class="list-unstyled <?php echo $row_class; ?>">
					<?php $count = 0; ?>
					<?php foreach( $services as $id => $obj ) : ?>
						<?php
						$count++;
						?>
						<li class="<?php echo $this_col_class; ?>"<?php echo $col_style; ?>>
							<div class="alert alert-default-o">
								<ul class="list-unstyled">
									<li>
										<a title="<?php echo ntsView::objectTitle($obj); ?>" href="<?php echo ntsLink::makeLink('-current-', '', array('service' => $id)); ?>">
											<?php echo ntsView::objectTitle( $obj, TRUE ); ?>
										</a>
									</li>
									<li>
										<?php require( dirname(__FILE__) . '/_index_service_details.php' ); ?>
									</li>
								</ul>
							</div>
						</li>
						<?php if( ! ($count % $this_per_row) ) : ?>
							<li class="clearfix"></li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>

<?php endif; ?>