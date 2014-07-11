<?php foreach( $status_actions as $sa ) : ?>
	<?php 
	list( $this_title, $this_icon ) = Hc_lib::parse_icon( $sa[1] );
	$link = ntsLink::makeLink('admin/manage/appointments/update', $sa[0] . '-confirm', array('_id' => $object->getId()) )
	?>
	<li>
		<a href="<?php echo $link; ?>" title="<?php echo $this_title; ?>">
			<?php echo $this_icon; ?> <?php echo $this_title; ?>
		</a>
	</li>
<?php endforeach; ?>