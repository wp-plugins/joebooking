<?php
if( ! isset($obj_class) )
	$obj_class = $obj->getClassName();

$select_link = ntsLink::makeLink( 
	'-current-',
	'', 
	array(
		$obj_class . '_id' => $obj->getId(),
		)
	);
$reset_link = ntsLink::makeLink( 
	'-current-',
	'', 
	array(
		$obj_class . '_id' => '0',
		)
	);
require( dirname(__FILE__) . '/_object_dropdown.php' );
?>
<?php if( (! $link) && (! isset($no_reset)) ) : ?>
	<a class="close text-danger" href="<?php echo $reset_link; ?>" title="<?php echo M('Reset'); ?>">
		<i class="fa fa-times text-danger"></i>
	</a>
<?php endif; ?>

<?php if( $link ) : ?>
	<?php if( $dropdown ) : ?>

		<div class="dropdown">
			<div class="squeeze-in">
				<span class="label label-<?php echo $alert_class; ?>">&nbsp;</span> 
				<a href="#" data-toggle="dropdown" class="dropdown-toggle" title="<?php echo ntsView::objectTitle($obj); ?>">
					<?php echo ntsView::objectTitle($obj); ?>
				</a>
				<?php echo Hc_html::dropdown_menu($dropdown); ?>
			</div>
		</div>

	<?php else : ?>

		<div class="squeeze-in">
			<span class="label label-<?php echo $alert_class; ?>">&nbsp;</span> 
			<a href="<?php echo $select_link; ?>" title="<?php echo ntsView::objectTitle($obj); ?>">
				<?php echo ntsView::objectTitle($obj); ?>
			</a>
		</div>

	<?php endif; ?>

<?php else : ?>

	<?php if( $errors && isset($errors[$obj_class]) ) : ?>
		<span class="text-danger">
			<?php echo ntsView::objectTitle($obj, TRUE); ?>
		</span>

		<ul class="list-unstyled">
			<li class="text-danger">
				<i class="fa-fw fa fa-exclamation-circle text-danger"></i><?php echo $errors[$obj_class]; ?>
			</li>
		</ul>

	<?php else : ?>
		<?php if( isset($info_link) && $info_link ) : ?>
			<a href="<?php echo $info_link; ?>">
		<?php endif; ?>
		<?php echo ntsView::objectTitle($obj, TRUE); ?>
		<?php if( isset($info_link) && $info_link ) : ?>
			</a>
		<?php endif; ?>
	<?php endif; ?>

<?php endif; ?>