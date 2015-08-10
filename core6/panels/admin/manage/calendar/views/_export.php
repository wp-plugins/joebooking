<?php
$ff =& ntsFormFactory::getInstance();
$choose_fields = 1;
?>

<ul class="list-inline">
<li>
<?php if( ! $choose_fields ) : ?>
	<a class="btn btn-default btn-sm" href="<?php echo ntsLink::makeLink('-current-', 'export'); ?>">
		<i class="fa fa-download"></i> <span class="hidden-xs"><?php echo M('Download'); ?></span>
	</a>
<?php else : ?>
	<div class="dropdown">
		<a class="dropdown-toggle btn btn-default btn-sm" data-toggle="dropdown" href="#">
			<i class="fa fa-download"></i> <span class="hidden-xs"><?php echo M('Download'); ?></span>
		</a>
		<ul class="dropdown-menu dropdown-menu-right">
			<li class="text-left">
				<span>
				<?php
				$form_file = dirname(__FILE__) . '/_export_form';

			/* default unset */
				$ci = ntsLib::getCurrentUser();
				$default_unset = $ci->getPreference( 'unset_download_fields' );
				$form_values = array();

				foreach( $default_unset as $du ){
					$form_values['field_' . $du] = 0;
				}

				$form =& $ff->makeForm( $form_file, $form_values );
				$form->display();
				?>
				</span>
			</li>
		</ul>
	</div>
<?php endif; ?>
</li>

<li>
	<a target="_blank" class="btn btn-default btn-sm" href="<?php echo ntsLink::makeLink('-current-', '', array('view-mode' => 'print')); ?>">
		<i class="fa fa-print"></i> <span class="hidden-xs"><?php echo M('Print'); ?></span>
	</a>
</li>
</ul>