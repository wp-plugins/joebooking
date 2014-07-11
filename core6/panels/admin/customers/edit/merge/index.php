<?php
$select_link = ntsLink::makeLink(
	'admin/customers/browse',
	'',
	array(
		NTS_PARAM_RETURN	=> 'merge',
		'skip'	=> $object->getId(),
		)
	);
?>
<p>
<?php echo M('When you merge this account to another one, this one is deleted and all appointments and other customer related data are transferred to the other one'); ?>.
</p>

<?php if( ! $merge_to ) : ?>
	<p>
	<a class="btn btn-default" href="<?php echo $select_link; ?>">
		<?php echo M('Customer'); ?>: <?php echo M('Select'); ?>
	</a>
	</p>
<?php else : ?>
	<?php
	$merge_to_id = $merge_to->getId();

	// some info for the target customer
	$locs = ntsLib::getVar( 'admin::locs' );
	$ress = ntsLib::getVar( 'admin::ress' );
	$sers = ntsLib::getVar( 'admin::sers' );
	$tm2 = ntsLib::getVar( 'admin::tm2' );
	$where = array(
		'customer_id'	=> array( '=', $merge_to_id ),
		'completed'		=> array( '>=', 0 ),
		'location_id'	=> array( 'IN', $locs ),
		'resource_id'	=> array( 'IN', $ress ),
		'service_id'	=> array( 'IN', $sers ),
		);
	$merge_to_apps_count = $tm2->countAppointments( $where );

	$pm =& ntsPaymentManager::getInstance();
	$invoices = $pm->getInvoicesOfCustomer( $merge_to_id );
	$merge_to_invoices_count = count( $invoices );
	?>

	<dl class="dl-horizontal">
		<dt>
			<?php echo M('Merge To'); ?>
		</dt>
		<dd>
			<ul class="list-unstyled">
				<li>
					<ul class="list-inline list-separated">
						<li>
							<a target="_blank" href="<?php echo ntsLink::makeLink('admin/customers/edit/edit', '', array('_id' => $merge_to->getId())); ?>">
								<?php echo ntsView::objectTitle($merge_to, TRUE); ?>
							</a>
						</li>
						<li>
							<a class="btn btn-default btn-sm" href="<?php echo $select_link; ?>">
								<?php echo M('Change'); ?>
							</a>
						</li>
					</ul>
				</li>

				<li>
					<ul class="list-inline list-separated">
					<?php
					$email = $merge_to->getProp('email');
					?>
						<?php if( $email ) : ?>
							<li>
								<?php echo $email; ?>
							</li>
						<?php endif; ?>
					</ul>
				</li>

				<li>
					<ul class="list-inline list-separated">
						<li>
							<?php echo M('Appointments'); ?>: <strong><?php echo $merge_to_apps_count; ?></strong>
						</li>
						<?php if( $merge_to_invoices_count ) : ?>
							<li>
								<?php echo M('Invoices'); ?>: <strong><?php echo $merge_to_invoices_count; ?></strong>
							</li>
						<?php endif; ?>
					</ul>
				</li>
			</ul>
		</dd>

		<dd>
			<p>
				<span class="alert alert-danger">
					<?php echo M('Please note that this operation can not be reverted!'); ?>
				</span>
			</p>
			<p>
				<a class="btn btn-success hc-confirm" href="<?php echo ntsLink::makeLink('-current-', 'merge', array('merge_to' => $merge_to_id)); ?>">
					<?php echo M('Merge'); ?>: 
					<?php echo ntsView::objectTitle($object, TRUE); ?> [id:<?php echo $object->getId(); ?>] 
					<i class="fa fa-fw fa-arrow-right"></i> 
					<?php echo ntsView::objectTitle($merge_to, TRUE); ?> [id:<?php echo $merge_to->getId(); ?>] 
				</a>
			</p>
		</dd>
	</dl>
<?php endif; ?>