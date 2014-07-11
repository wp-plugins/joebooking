<?php if( $rid ) : ?>
	<?php
	echo $this->render_file(
		dirname(__FILE__) . '/_object.php',
		array(
			'obj_class'	=> 'resource',
			'available'	=> $available['resource'],
			'this_id'	=> $rid,
			'all_ids'	=> $ress,
			'a'			=> $a,
			)
		);
	?>
<?php else : ?>
	<?php
	if( isset($current_resource) )
	{
		$current_id = $current_resource->getId();
		$selector_label = ntsView::objectTitle( $current_resource, TRUE );
		$selector_class = $current_selector_class;
	}
	else
	{
		$selector_label = M('Bookable Resource');
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
				$this_available = $available['resource'];
				if( isset($current_resource) )
				{
					$ress = array_diff( $ress, array($current_resource->getId()) );
					$ress = array_values( $ress );
				}
				echo $this->render_file(
					dirname(__FILE__) . '/_object.php',
					array(
						'obj_class'	=> 'resource',
						'available'	=> $this_available,
						'this_id'	=> $rid,
						'all_ids'	=> $ress,
						'a'			=> $a,
						)
					);
				?>
			</div>
		</div>
	</div>
<?php endif; ?>