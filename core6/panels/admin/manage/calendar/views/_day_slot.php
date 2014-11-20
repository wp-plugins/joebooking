<?php
$need_link = ( ($targetLink != '#') OR $menu ) ? TRUE : FALSE;
if( $need_link )
	$slotClass .= ' dropdown';
else
	$slotClass .= ' squeeze-in';
?>

<div class="alert alert-condensed text-small <?php echo $slotClass; ?>" style="margin: 0 1px;" title="<?php echo $linkLabel; ?>">
<?php if( $need_link ) : ?>
	<a href="<?php echo $targetLink; ?>" class="<?php echo $aClass; ?> squeeze-in display-block no-decoration"<?php echo $moreAttr; ?>>
<?php endif; ?>

<?php if( $slotPullRight ) : ?>
	<div style="float: right;">
		<?php echo $slotPullRight; ?>
	</div>
<?php endif; ?>

	<?php if( $time_view_needed ) : ?>
		<div class="text-left squeeze-in"><?php echo $timeViewStart; ?></div>
	<?php endif; ?>
	<?php if( $calendarField ) : ?>
		<div class="text-center squeeze-in"><?php echo $slotInfo; ?></div>
	<?php endif; ?>
	<?php if( $time_view_needed ) : ?>
		<div class="text-right squeeze-in"><?php echo $timeViewEnd; ?></div>
	<?php endif; ?>

<?php if( $need_link ) : ?>
	</a>
<?php endif; ?>
	<?php if( $menu ) : ?>
		<?php echo Hc_html::dropdown_menu($menu); ?>
	<?php endif; ?>
</div>