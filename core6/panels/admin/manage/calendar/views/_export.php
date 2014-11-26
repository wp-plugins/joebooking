<?php
$ff =& ntsFormFactory::getInstance();
$choose_fields = 1;
?>

<?php if( ! $choose_fields ) : ?>
	<a class="btn btn-default" href="<?php echo ntsLink::makeLink('-current-', 'export'); ?>">
		<i class="fa fa-download"></i> <span class="hidden-xs"><?php echo M('Download'); ?></span>
	</a>
<?php else : ?>
	<div class="dropdown">
		<a class="dropdown-toggle btn btn-default" data-toggle="dropdown" href="#">
			<i class="fa fa-download"></i> <span class="hidden-xs"><?php echo M('Download'); ?></span>
		</a>
		<ul class="dropdown-menu dropdown-menu-right">
			<li class="text-left">
				<span>
				<?php
				$form_file = dirname(__FILE__) . '/_export_form';
				$form =& $ff->makeForm( $form_file );
				$form->display();
				?>
				</span>
			</li>
		</ul>
	</div>
<?php endif; ?>