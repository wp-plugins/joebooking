<div class="row">
<?php foreach( $apps as $app ) : ?>
	<div class="col-md-3 col-sm-6">
		<ul class="nav nav-stacked">
			<li>
			<?php
			$obj_class = $app->getClassName();
			switch( $obj_class )
			{
				case 'appointment':
					$view = array(
						'app'		=> $app,
						'labels'	=> $labels,
						'checkbox'	=> TRUE,
						);
					echo $this->render_file(
						dirname(__FILE__) . '/app.php',
						$view
						);
					break;

				case 'timeoff':
					$view = array(
						'toff'		=> $app,
						'checkbox'	=> TRUE,
						'date'		=> $date
						);
					echo $this->render_file(
						dirname(__FILE__) . '/toff.php',
						$view
						);
					break;
			}
			?>
			</li>
		</ul>
	</div>
<?php endforeach; ?>
</div>