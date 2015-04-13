<?php if( $sid ) : ?>
	<?php
	echo $this->render_file(
		dirname(__FILE__) . '/_object.php',
		array(
			'obj_class'	=> 'service',
			'available'	=> $available['service'],
			'this_id'	=> $sid,
			'all_ids'	=> $sers,
			'a'			=> $a,
			)
		);
	?>
<?php else : ?>
	<?php
	if( isset($current_service) ){
		$current_id = $current_service->getId();
		$selector_label = ntsView::objectTitle( $current_service, TRUE );
		$selector_class = $current_selector_class;
	}
	else {
		$selector_label = M('Service');
		$selector_class = 'danger-o';
	}
	?>
	<div class="collapse-panel panel panel-group panel-<?php echo $selector_class; ?>">
		<div class="panel-heading">
			<?php if( isset($current_service) ) : ?>
				<ul class="list-unstyled list-separated">
					<li>
						<a href="#" data-toggle="collapse-next" class="display-block" style="color: inherit;">
							<?php echo $selector_label; ?> <span class="caret"></span>
						</a>
					</li>
					<li>
						<?php
						$obj = $current_service;
						require( dirname(__FILE__) . '/_service_details.php' );
						?>
					</li>
				</ul>
			<?php else : ?>
				<a href="#" data-toggle="collapse-next" class="display-block">
					<?php echo $selector_label; ?> <span class="caret"></span>
				</a>
			<?php endif; ?>
		</div>

		<div class="panel-collapse collapse<?php if( count($to_select) <= 1){echo ' in';}; ?>">
			<div class="panel-body">
				<?php
				$this_available = $available['service'];
				if( isset($current_service) )
				{
					$sers = array_diff( $sers, array($current_service->getId()) );
					$sers = array_values( $sers );
				}
				echo $this->render_file(
					dirname(__FILE__) . '/_object.php',
					array(
						'obj_class'	=> 'service',
						'available'	=> $this_available,
						'this_id'	=> $sid,
						'all_ids'	=> $sers,
						'a'			=> $a,
						)
					);
				?>
			</div>
		</div>
	</div>
<?php endif; ?>