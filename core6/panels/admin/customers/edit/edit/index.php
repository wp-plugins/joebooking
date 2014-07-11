<?php
$object = ntsLib::getVar( 'admin/customers/edit::OBJECT' );
$objectId = $object->getId();

$ntsConf =& ntsConf::getInstance();
$attachEnableCompany = $ntsConf->get('attachEnableCompany');
?>
<div class="row">
	<div class="col-md-8 col-sm-12">
		<p>
		<?php $NTS_VIEW['form']->display(); ?>
	</div>
	<div class="col-md-4 col-sm-12">
		<?php if( $attachEnableCompany ) : ?>
			<p>
			<div class="nts-auto-load nts-ajax-return" data-src="<?php echo ntsLink::makeLink('admin/customers/edit/attachments', '', array(NTS_PARAM_VIEW_RICH => 'basic', '_id' => $objectId) ); ?>">
			</div>
		<?php endif; ?>

		<p>
		<div class="nts-auto-load nts-ajax-return" data-src="<?php echo ntsLink::makeLink('admin/customers/edit/notes', '', array(NTS_PARAM_VIEW_RICH => 'basic', '_id' => $objectId) ); ?>">
		</div>
	</div>
</div>