<?php
$t = $NTS_VIEW['t'];
$t->setTimestamp( $obj );
$select_link = ntsLink::makeLink( 
	'-current-',
	'', 
	array(
		'starts_at' => $obj,
		)
	);
$reset_link = ntsLink::makeLink( 
	'-current-',
	'', 
	array(
		'starts_at' => '-reset-',
		)
	);
require( dirname(__FILE__) . '/_object_dropdown.php' );
?>
<?php if( ! $link ) : ?>
	<a class="close text-danger" href="<?php echo $reset_link; ?>" title="<?php echo M('Reset'); ?>">
		<i class="fa fa-times text-danger"></i>
	</a>
<?php endif; ?>

<?php if( $link ) : ?>
	<?php if( $dropdown ) : ?>
		<span class="dropdown">
			<a href="#" data-toggle="dropdown" class="dropdown-toggle text-center display-block" title="<?php echo $t->formatTime(); ?>">
				<?php echo $t->formatTime(); ?>
			</a>
			<?php echo Hc_html::dropdown_menu($dropdown); ?>
		</span>
	<?php else : ?>
		<a href="<?php echo $select_link; ?>" title="<?php echo $t->formatTime(); ?>" class="text-center display-block nts-no-ajax">
			<?php echo $t->formatTime(); ?>
		</a>
	<?php endif; ?>

<?php else : ?>

	<i class="fa fa-fw fa-calendar"></i><?php echo $t->formatDateFull(); ?> <i class="fa fa-fw fa-clock-o"></i><?php echo $t->formatTime(); ?>

	<?php if( $errors ) : ?>
		<ul class="list-unstyled">
		<?php foreach( $errors as $err_class => $err_text ) : ?>
			<?php if( $err_class == $obj_class ) : ?>
				<i class="fa-fw fa fa-exclamation-circle text-danger"></i><?php echo $err_text; ?>
			<?php endif; ?>
		<?php endforeach; ?>
		</ul>
	<?php endif; ?>

<?php endif; ?>