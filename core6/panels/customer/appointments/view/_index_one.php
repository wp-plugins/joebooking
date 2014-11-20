<?php
$conf =& ntsConf::getInstance();
$auto_resource = $conf->get('autoResource');
$auto_location = $conf->get('autoLocation');

$completedStatus = $a->getProp('completed');
$approvedStatus = $a->getProp('approved');

$service = ntsObjectFactory::get( 'service' );
$service->setId( $a->getProp('service_id') );

$displayColumns = array();
if( ! (NTS_SINGLE_LOCATION OR $auto_location) )
	$displayColumns[] = 'location';
if( ! (NTS_SINGLE_RESOURCE OR $auto_resource) )
	$displayColumns[] = 'resource';

/* due payment */
$cost = $a->getCost();
$payment_options = array();
$default_payment_option = '';

if( $cost )
{
	$due = $a->getDue();
	$paid = $a->getPaidAmount();

	$balance_cover = array();
	if( $customer_balance )
	{
		$balance_cover = $am->balance_cover( $customer_balance, $a );
	}
	$displayColumns[] = 'payment';

	if( $group_ref )
	{
		require( dirname(__FILE__) . '/_payment_group.php' );
	}
	else
	{
		require( dirname(__FILE__) . '/_payment_browse.php' );
	}
}

$actions = array();
if( ! $completedStatus )
{
	$minCancel = $service->getProp('min_cancel');
	if( ($now + $minCancel) > $a->getProp('starts_at') )
	{
		// can't cancel
	}
	else
	{
		if( $canCancel )
		{
			$actions[] = array(
				ntsLink::makeLink('-current-/../edit/cancel', '', array('_id' => $a->getId(), NTS_PARAM_RETURN => 'all') ),
				'<i class="fa fa-times text-danger"></i> ' . M('Cancel'),
				);
		}
		if( $canReschedule )
		{
			$t->setTimestamp( $a->getProp('starts_at') );
			$oldDate = $t->formatDate_Db();

			$actions[] = array(
				ntsLink::makeLink('customer/book', '', array('reschedule' => $a->getId(), 'cal' => $oldDate) ),
				'<i class="fa fa-exchange text-warning"></i> ' . M('Reschedule'),
				);
		}
	}
}

if( $group_ref )
{
	$actions = array();
}
?>

<?php
$t->setTimestamp( $a->getProp('starts_at') );

$dateView = $t->formatDateFull();
$fullDateView = '<i class="fa fa-fw fa-calendar"></i>' . $dateView;

$timeView = $t->formatTime();
$fullTimeView = '<i class="fa fa-fw fa-clock-o"></i>' . $timeView;

$status_class = $a->statusClass();
$status_text = $a->statusText();

$collapse_in = $group_ref ? ' in' : '';
?>
<li class="collapse-panel panel panel-default panel-<?php echo $status_class; ?>">
	<div class="panel-heading" title="<?php echo $status_text; ?>">
		<span class="pull-right">
			<?php echo $a->statusLabel(); ?>
		</span>

		<?php if( in_array('payment', $displayColumns) && $cost ) : ?>
			<span class="pull-right" style="margin-right: 0.5em; ">
				<?php echo $a->paymentStatus(); ?>
			</span>
		<?php endif; ?>

		<h4 class="panel-title">
			<a href="#" data-toggle="collapse-next">
				<?php echo $fullDateView; ?> <?php echo $fullTimeView; ?>
			</a>
		</h4>
	</div>

	<div class="panel-collapse collapse<?php echo $collapse_in; ?>">
		<div class="panel-body">
			<ul class="list-unstyled list-separated">
				<?php if( in_array('location', $displayColumns) ) : ?>
					<?php
					$location = new ntsObject('location');
					$location->setId( $a->getProp('location_id') );
					?>
					<li>
						<?php echo ntsView::objectTitle($location, TRUE); ?>
					</li>
				<?php endif; ?>

				<?php if( in_array('resource', $displayColumns) ) : ?>
					<?php
					$resource = ntsObjectFactory::get( 'resource' );
					$resource->setId( $a->getProp('resource_id') );
					?>
					<li>
						<?php echo ntsView::objectTitle($resource, TRUE); ?>
					</li>
				<?php endif; ?>

				<li>
					<?php echo ntsView::objectTitle( $service, TRUE ); ?>
				</li>
			</ul>
		</div>

		<?php if( $cost OR $actions ) : ?>
			<div class="panel-footer">
				<ul class="list-inline">
					<?php if( $cost ) : ?>
						<li>
							<?php echo M('Price'); ?> <strong><?php echo ntsCurrency::formatPrice( $cost ); ?></strong>
						</li>

						<?php if( $due <= 0 ) : ?>
							<li>
								<span class="btn btn-success-o">
									<?php echo M('Paid'); ?>
								</span>
							</li>
						<?php else : ?>
							<?php if( $paid ) : ?>
								<li>
									<?php echo M('Paid'); ?> <strong><?php echo ntsCurrency::formatPrice( $paid ); ?></strong>
								</li>
							<?php endif; ?>

							<?php if( $payment_options ) : ?>
								<?php if( count($payment_options) > 1 OR ($default_payment_option && count($payment_options)) ) : ?>
									<li class="dropdown">
										<a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#">
											<?php if( $default_payment_option ) : ?>
												<?php echo $default_payment_option; ?>
											<?php else : ?>
												<?php echo M('Due'); ?> <strong><?php echo ntsCurrency::formatPrice( $due ); ?></strong>
											<?php endif; ?>
											<span class="caret"></span>
										</a>
										<ul class="dropdown-menu">
											<?php foreach( $payment_options as $po ) : ?>
												<?php if( is_array($po) ) : ?>
													<li>
														<a href="<?php echo $po[1]; ?>" class="" title="<?php echo $po[0]; ?>">
															<?php echo $po[0]; ?>
														</a>
													</li>
												<?php else : ?>
													<li class="dropdown-header">
														<?php echo $po; ?>
													</li>
												<?php endif; ?>
										<?php endforeach; ?>
										</ul>
									</li>
								<?php else : ?>
									<li>
									<?php foreach( $payment_options as $po ) : ?>
										<?php if( $po[1] ) : ?>
											<a href="<?php echo $po[1]; ?>" class="btn btn-default" title="<?php echo $po[0]; ?>">
												<?php echo $po[0]; ?>
											</a>
										<?php else : ?>
											<span class="btn btn-default" title="<?php echo $po[0]; ?>">
												<?php echo $po[0]; ?>
											</span>
										<?php endif; ?>
									<?php endforeach; ?>
									</li>
								<?php endif; ?>
							<?php else : ?>
								<li>
									<span class="btn btn-danger-o">
										<?php echo M('Due'); ?> <strong><?php echo ntsCurrency::formatPrice( $due ); ?></strong>
									</span>
								</li>
							<?php endif; ?>
						<?php endif; ?>

						<?php if( $actions ) : ?>
							<li class="divider">&nbsp;</li>
						<?php endif; ?>
					<?php endif; ?>

					<?php foreach( $actions as $aa ) : ?>
						<?php
						list( $action_title, $action_icon ) = Hc_lib::parse_icon($aa[1]);
						?>
						<li>
							<a href="<?php echo $aa[0]; ?>" class="btn btn-default" title="<?php echo $action_title; ?>">
								<?php echo $action_icon; ?><?php echo $action_title; ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>
	</div>
</li>
