<ul class="list-unstyled">
<?php foreach( $apps as $app ) : ?>
	<li>
		<?php
		$obj_class = $app->getClassName();
		switch( $obj_class )
		{
			case 'appointment':
				$view = array(
					'app'		=> $app,
					'labels'	=> $labels,
					'checkbox'	=> FALSE,
					'date'		=> $date
					);
				echo $this->render_file(
					dirname(__FILE__) . '/app.php',
					$view
					);
				break;

			case 'timeoff':
				$view = array(
					'toff'		=> $app,
					'checkbox'	=> FALSE,
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
<?php endforeach; ?>
</ul>