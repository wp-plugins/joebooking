<?php
$ntsConf =& ntsConf::getInstance();
$customerAcknowledge = $ntsConf->get( 'customerAcknowledge' );
$attachEnableCompany = $ntsConf->get('attachEnableCompany');

$availability_status = $NTS_VIEW['availability_status'];
$conflicts = $NTS_VIEW['conflicts'];

$ress_all = ntsLib::getVar( 'admin::ress_all' );
$locs_all = ntsLib::getVar( 'admin::locs_all' );
$sers_all = ntsLib::getVar( 'admin::sers_all' );

$printView = ($NTS_VIEW[NTS_PARAM_VIEW_MODE] == 'print') ? TRUE : FALSE;

$appEdit = ntsLib::getVar( 'admin/manage:appEdit' );
$object = ntsLib::getVar( 'admin/manage/appointments/edit::OBJECT' );
$objectId = $object->getId();

$locationId = $object->getProp('location_id');
$resourceId = $object->getProp('resource_id');
$serviceId = $object->getProp('service_id');
$startsAt = $object->getProp('starts_at'); 
$createdAt = $object->getProp('created_at'); 
$duration = $object->getProp('duration'); 

$iCanEdit = in_array($resourceId, $appEdit) ? TRUE : FALSE;
if( $printView )
	$iCanEdit = FALSE;

$service = ntsObjectFactory::get( 'service' );
$service->setId( $serviceId ); 
$location = ntsObjectFactory::get( 'location' );
$location->setId( $locationId );
$resource = ntsObjectFactory::get( 'resource' );
$resource->setId( $resourceId );

$NTS_VIEW['t']->setTimestamp( $startsAt );
$dateView = ( $startsAt > 0 ) ? $NTS_VIEW['t']->formatWeekdayShort() . ', ' . $NTS_VIEW['t']->formatDate() : M('Not Scheduled');

if( $startsAt > 0 ){
	$timeView = $NTS_VIEW['t']->formatTime();
	$NTS_VIEW['t']->modify( '+' . $duration . ' seconds' );
	$timeView .= ' - ' . $NTS_VIEW['t']->formatTime();
	}
else {
	$timeView = M('Not Scheduled');
	}

$customerId = $object->getProp('customer_id');
$customer = new ntsUser;
$customer->setId( $customerId );
$customerIds = array();
?>

<div class="row">
	<div class="col-md-7">
		<dl class="dl-horizontal">
			<?php if( is_array($availability_status) ) : ?>
				<dd class="text-danger">
					<ul class="list-unstyled">
					<?php foreach( $conflicts as $c ) : ?>
						<li>
							<i class="fa fa-fw fa-exclamation-circle"></i><?php echo $c; ?>
						</li>
					<?php endforeach; ?>
					</ul>
				</dd>
				<hr>
			<?php elseif(! $availability_status) : ?>
				<dd class="text-muted">
					<ul class="list-unstyled">
						<li>
							<i class="fa fa-fw fa-exclamation-circle"></i><?php echo M('No availability configured'); ?>
						</li>
					</ul>
				</dd>
				<hr>
			<?php endif; ?>

			<?php if( $printView ) : ?>
				<dd>
					<?php echo $object->statusLabel(); ?>
				</dd>
			<?php endif; ?>

			<dt>
				<?php echo M('Customer'); ?>
			</dt>
			<dd>
				<i class="fa fa-fw fa-user"></i>
				<?php if( $printView ) : ?>
					<?php echo ntsView::objectTitle($customer); ?>
				<?php else : ?>
					<a target="_blank" href="<?php echo ntsLink::makeLink('admin/customers/edit/edit', '', array('_id' => $customer->getId())); ?>">
						<?php echo ntsView::objectTitle($customer); ?> 
					</a>
				<?php endif; ?>
			</dd>

			<?php if( (count($locs_all) > 1) && $locationId ) : ?>
				<dt>
					<?php echo M('Location'); ?>
				</dt>
				<dd>
					<?php echo ntsView::objectTitle($location, TRUE); ?> 
				</dd>
			<?php endif; ?>

			<?php if( (count($ress_all) > 1) && $resourceId ) : ?>
				<dt>
					<?php echo M('Bookable Resource'); ?>
				</dt>
				<dd>
					<?php echo ntsView::objectTitle($resource, TRUE); ?> 
				</dd>
			<?php endif; ?>

			<dt>
				<?php echo M('Service'); ?>
			</dt>
			<dd>
				<?php echo ntsView::objectTitle($service, TRUE); ?> 
			</dd>
		</dl>

		<?php
		$om =& objectMapper::getInstance();
		$formId = $om->isFormForService( $serviceId );
		$fields = array();
		if( $formId )
		{
			$form = ntsObjectFactory::get( 'form' );
			$form->setId( $formId );
			$formTitle = $form->getProp('title');

			$serviceId = $object->getProp( 'service_id' );

			$class = 'appointment';
			$otherDetails = array(
				'service_id'	=> $serviceId,
				);
			$fields = $om->getFields( $class, 'internal', $otherDetails );
		}
		?>

		<?php if( $formId && $fields ) : ?>
			<div class="form-horizontal form-condensed">
				<?php
				echo ntsForm::wrapInput(
					'',
					'<h4>' . $formTitle . '</h4>'
					);
				?>
			</div>
			<?php if( ! $printView ) : ?>
				<?php 
				$NTS_VIEW['customForm']->display();
				?>
			<?php else : ?>
				<div class="form-horizontal form-condensed">

				<?php foreach( $fields as $f ) : ?>
					<?php $c = $om->getControl( $class, $f[0], false ); ?>
					<?php
						$value = $object->getProp($f[0]);
						if( $c[1] == 'checkbox' )
							$value = $value ? M('Yes') : M('No');

						echo ntsForm::wrapInput(
							$c[0],
							$value
							);
					?>
				<?php endforeach; ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	
	<div class="col-md-5">
		<?php if( $attachEnableCompany ) : ?>
			<p>
			<div class="nts-auto-load nts-ajax-return" data-src="<?php echo ntsLink::makeLink('admin/manage/appointments/edit/attachments', '', array(NTS_PARAM_VIEW_RICH => 'basic', '_id' => $objectId) ); ?>">
			</div>
		<?php endif; ?>

		<p>
		<div class="nts-auto-load nts-ajax-return" data-src="<?php echo ntsLink::makeLink('admin/manage/appointments/edit/notes', '', array(NTS_PARAM_VIEW_RICH => 'basic', '_id' => $objectId) ); ?>">
		</div>
	</div>
</div>