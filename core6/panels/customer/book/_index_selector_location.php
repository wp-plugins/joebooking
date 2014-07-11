<?php if( $requested['location'] OR (count($locations) <= 1) ) : ?>

	<?php foreach( $locations as $obj ) : ?>
		<?php
		$description = $obj->getProp('description');
		?>
		<div class="alert alert-default-o">
			<?php if( $requested['location'] ) : ?>
				<a class="close text-danger" title="<?php echo M('Reset'); ?>" href="<?php echo ntsLink::makeLink('-current-', '', array('location' => '-reset-')); ?>">
					<i class="fa fa-times text-danger"></i>
				</a>
			<?php endif; ?>
			<ul class="list-unstyled collapse-panel">
				<li>
					<?php if( strlen($description) ) : ?>
						<a href="#" data-toggle="collapse-next" class="display-block">
					<?php endif; ?>
					<?php echo ntsView::objectTitle( $obj, TRUE ); ?>
					<?php if( strlen($description) ) : ?>
						</a>
					<?php endif; ?>
				</li>
				<?php if( strlen($description) ) : ?>
					<li class="collapse">
						<?php echo $description; ?>
					</li>
				<?php endif; ?>
			</ul>
		</div>
	<?php endforeach; ?>

<?php else : ?>

	<div class="collapse-panel panel panel-group panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a href="#" data-toggle="collapse-next" class="display-block">
					<?php echo M('Location'); ?> <span class="caret"></span>
				</a>
			</h4>
		</div>

		<div class="panel-collapse collapse<?php echo $this_collapse; ?>">
			<div class="panel-body">
				<ul class="list-unstyled <?php echo $row_class; ?>">
					<?php $count = 0; ?>
					<?php foreach( $locations as $id => $obj ) : ?>
						<?php
						$count++;
						$description = $obj->getProp('description');
						?>
						<li class="<?php echo $col_class; ?>"<?php echo $col_style; ?>>
							<div class="alert alert-default-o">
								<ul class="list-unstyled">
									<li>
										<a title="<?php echo ntsView::objectTitle($obj); ?>" href="<?php echo ntsLink::makeLink('-current-', '', array('location' => $id)); ?>">
											<?php echo ntsView::objectTitle( $obj, TRUE ); ?>
										</a>
									</li>
									<?php if( $description ) : ?>
										<li>
											<?php echo $description; ?>
										</li>
									<?php endif; ?>
								</ul>
							</div>
						</li>
						<?php if( ! ($count % $per_row) ) : ?>
							<li class="clearfix"></li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>

<?php endif; ?>