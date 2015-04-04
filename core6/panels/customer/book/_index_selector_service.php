<?php
$this_per_row = 2;
$this_col_class = 'col-lg-6 col-md-6 col-sm-12 col-xs-12';

$am =& ntsAccountingManager::getInstance();
$customer_id = ntsLib::getCurrentUserId();
$customer_balance = $am->get_balance( 'customer', $customer_id );
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

						$balance_cover = array();
						$can_book_this = 1;
						$package_only = $obj->getProp('package_only');
						if( $customer_balance ){
							$test_app = ntsObjectFactory::get('appointment');
							$test_app->setByArray( $this_a );
							$test_app->setProp('service_id', $id);
							$balance_cover = $am->balance_cover( $customer_balance, $test_app );
						}

						if( $package_only ){
							$can_book_this = 0;
							if( $balance_cover ){
								$can_book_this = 1;
							}
						}
						?>
						<li class="<?php echo $this_col_class; ?>"<?php echo $col_style; ?>>
							<div class="alert alert-default-o">
								<ul class="list-unstyled">
									<li>
										<ul class="list-unstyled">
											<li>
											<?php if( $can_book_this ) : ?>
												<a title="<?php echo ntsView::objectTitle($obj); ?>" href="<?php echo ntsLink::makeLink('-current-', '', array('service' => $id)); ?>">
													<?php echo ntsView::objectTitle( $obj, TRUE ); ?>
												</a>
											<?php else : ?>
												<?php echo ntsView::objectTitle( $obj, TRUE ); ?>
											<?php endif; ?>
											</li>

											<?php if( $balance_cover ) : ?>
												<li>
													<span class="label label-success"><?php echo M('Can Be Paid By Balance'); ?></span>
												</li>
											<?php elseif( $package_only ) : ?>
												<li>
													<a title="<?php echo M('Purchase'); ?>" href="<?php echo ntsLink::makeLink('customer/packs'); ?>">
														<span class="label label-warning"><?php echo M('Available In Package Only'); ?></span>
													</a>
												</li>
											<?php endif; ?>
										</ul>
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