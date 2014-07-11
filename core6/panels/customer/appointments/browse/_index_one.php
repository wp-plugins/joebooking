<?php
$completedStatus = $a->getProp('completed');
$approvedStatus = $a->getProp('approved');

$service = ntsObjectFactory::get( 'service' );
$service->setId( $a->getProp('service_id') );

/* due payment */
$cost = $a->getCost();
if( $cost )
{
	$due = $a->getDue();
	$paid = $a->getPaidAmount();

	$balance_cover = array();
	if( $customer_balance )
	{
		$balance_cover = $am->balance_cover( $customer_balance, $a );
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
else
{
	if( $customerAcknowledge && (! $a->getProp('_ack')) )
	{
		$actions[] = array(
			ntsLink::makeLink('-current-/../edit/ack', '', array('_id' => $a->getId(), NTS_PARAM_RETURN => 'all') ),
			M('Acknowledge Completion'),
			);
	}
}
?>

<?php
$dateView = $t->formatDateFull();
$fullDateView = '<i class="fa fa-fw fa-calendar"></i>' . $dateView;

$timeView = $t->formatTime();
$fullTimeView = '<i class="fa fa-fw fa-clock-o"></i>' . $timeView;

$status_class = $a->statusClass();
$status_text = $a->statusText();
?>

<li class="collapse-panel panel panel-default panel-<?php echo $status_class; ?>">
	<div class="panel-heading" title="<?php echo $status_text; ?>">
		<?php if( in_array('payment', $displayColumns) && $cost ) : ?>
			<span class="pull-right">
				<?php echo $a->paymentStatus(); ?>
			</span>
		<?php endif; ?>

		<h4 class="panel-title">
			<a href="#" data-toggle="collapse-next">
				<?php echo $fullDateView; ?> <?php echo $fullTimeView; ?>
			</a>
		</h4>
	</div>
	<div class="panel-collapse collapse">
		<div class="panel-body">
			<ul class="list-unstyled list-separated">
				<li>
					<?php echo $a->statusLabel(); ?>
				</li>

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

							<?php
							$payment_options = array();
							if( $balance_cover )
							{
								$payment_options[] = M('Use Balance');

								foreach( $balance_cover as $asset_id => $asset_value )
								{
									$asset_expires = 0;
									if( strpos($asset_id, '-') !== FALSE )
									{
										list( $asset_short_id, $asset_expires ) = explode( '-', $asset_id );
									}

									$this_view = $aam->format_asset( $asset_id, $asset_value, FALSE, FALSE );
									
									$this_view .= ' [' . M('Available') . ': ';
									$this_view .= $aam->format_asset( $asset_id, $customer_balance[$asset_id], FALSE, FALSE );
									if( $asset_expires )
									{
										$t->setTimestamp( $asset_expires );
										$this_view .= ', ' . M('Expires') . ': ' . $t->formatDateFull();
									}
									$this_view .= ']';

									$balance_link = ntsLink::makeLink(
										'-current-/../edit/paybalance',
										'',
										array(
											'_id'			=> $a->getId(),
											'asset_id'		=> $asset_id,
											'asset_value'	=> $asset_value
											)
										);

									$payment_options[] = array(
										$this_view,
										$balance_link
										);
								}
							}
							if( $has_online )
							{
								if( $balance_cover )
								{
									$payment_options[] = M('Pay Online');
									$payment_options[] = array(
										ntsCurrency::formatPrice( $due ),
										'#'
										);
								}
								else
								{
									$payment_options[] = array(
										M('Pay Online') . ' ' . ntsCurrency::formatPrice( $due ),
										'#'
										);
								}
							}
							?>

							<?php if( $payment_options ) : ?>
								<?php if( count($payment_options) > 1 ) : ?>
									<li class="dropdown">
										<a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#">
											<?php echo M('Pay Now'); ?> <span class="caret"></span>
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
										<a href="<?php echo $po[1]; ?>" class="btn btn-default" title="<?php echo $po[0]; ?>">
											<?php echo $po[0]; ?>
										</a>
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
