<?php if( $lid ) : ?>
	<?php
	echo $this->render_file(
		dirname(__FILE__) . '/_object.php',
		array(
			'obj_class'	=> 'location',
			'available'	=> $available['location'],
			'this_id'	=> $lid,
			'all_ids'	=> $locs,
			'a'			=> $a,
			)
		);
	?>
<?php else : ?>
	<?php
	if( isset($current_location) )
	{
		$current_id = $current_location->getId();
		$selector_label = ntsView::objectTitle( $current_location, TRUE );
		$selector_class = $current_selector_class;
	}
	else
	{
		$selector_label = M('Location');
		$selector_class = 'danger-o';
	}
	?>
	<div class="collapse-panel panel panel-group panel-<?php echo $selector_class; ?>">
		<div class="panel-heading">
			<a href="#" data-toggle="collapse-next" class="display-block">
				<?php echo $selector_label; ?> <span class="caret"></span>
			</a>
		</div>

		<div class="panel-collapse collapse<?php if( count($to_select) <= 1){echo ' in';}; ?>">
			<div class="panel-body">
				<?php
				$this_available = $available['location'];
				if( isset($current_location) )
				{
					$locs = array_diff( $locs, array($current_location->getId()) );
					$locs = array_values( $locs );
				}
				echo $this->render_file(
					dirname(__FILE__) . '/_object.php',
					array(
						'obj_class'	=> 'location',
						'available'	=> $this_available,
						'this_id'	=> $lid,
						'all_ids'	=> $locs,
						'a'			=> $a,
						)
					);
				?>
			</div>
		</div>
	</div>
<?php endif; ?>