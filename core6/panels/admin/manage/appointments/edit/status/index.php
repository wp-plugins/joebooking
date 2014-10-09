<?php foreach( $status_actions as $sa ) : ?>
	<?php 
	list( $this_title, $this_icon ) = Hc_lib::parse_icon( $sa[1] );

	if( in_array($sa[0], array('reject')) )
	{
		$final_action = $sa[0];
		$link_class = 'hc-ajax-loader';
		$parent_class = 'hc-ajax-parent';
		$parent_more = ' data-wrap-ajax-child="li"';
	}
	else
	{
		$final_action = $sa[0] . '-confirm';
		$link_class = '';
		$parent_class = '';
		$parent_more = '';
	}
	$link = ntsLink::makeLink('admin/manage/appointments/update', $final_action, array('_id' => $object->getId()) )
	?>
	<li class="<?php echo $parent_class; ?>"<?php echo $parent_more; ?>>
		<a href="<?php echo $link; ?>" title="<?php echo $this_title; ?>" class="<?php echo $link_class; ?>">
			<?php echo $this_icon; ?> <?php echo $this_title; ?>
		</a>
	</li>
<?php endforeach; ?>