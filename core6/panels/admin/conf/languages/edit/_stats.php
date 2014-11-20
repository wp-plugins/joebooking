<?php
$count_custom = count( array_intersect($all_strings, array_keys($custom_translate)) );
$count_file = count( array_intersect($all_strings, array_keys($file_translate)) ) - $count_custom;
$count_missing = count( array_diff($all_strings, array_keys($file_translate), array_keys($custom_translate)) );
?>
<ul class="list-unstyled list-inline">
	<li>
		<div class="alert alert-condensed alert-success">
			<?php echo M('Custom'); ?>: <strong><?php echo $count_custom; ?></strong>
		</div>
	</li>
	<li>
		<div class="alert alert-condensed alert-info">
			<?php echo M('System'); ?>: <strong><?php echo $count_file; ?></strong>
		</div>
	</li>
	<?php if( $lang != 'en' ) : ?>
		<li>
			<div class="alert alert-condensed alert-danger">
				<?php echo M('Missing'); ?>: <strong><?php echo $count_missing; ?></strong>
			</div>
		</li>
	<?php endif; ?>
</ul>