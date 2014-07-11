<?php
$t = $NTS_VIEW['t'];
$generic_child_file = dirname(__FILE__) . '/../../views/_object_child.php';

$common_errors = array();
if( count($locs) <= 1 )
	$common_errors[] = 'location';
if( count($ress) <= 1 )
	$common_errors[] = 'resource';
$common_errors[] = 'time';

/* check if we have custom fields */
$om =& objectMapper::getInstance();
$custom_forms = array();

foreach( $apps as $a )
{
	$form_id = $om->isFormForService( $a['service_id'] );
	if( $form_id )
	{
		$custom_forms[ $form_id ] = $a['service_id'];
	}
}

$custom_fields = array();
if( $custom_forms )
{
	$class = 'appointment';
	reset( $custom_forms );
	foreach( $custom_forms as $form_id => $sid )
	{
		$other_details = array(
			'service_id'	=> $sid,
			);
		$this_fields = $om->getFields( $class, 'internal', $other_details );
		$custom_fields[ $form_id ] = $this_fields;
	}
}
$dl_class = $custom_fields ? 'dl-horizontal' : '';
?>
<div class="page-header">
	<h2><?php echo M('Confirm'); ?></h2>
</div>

<ul class="list-unstyled">
	<li>
		<?php
		$obj = new ntsUser;;
		$obj->setId( $cid );
		$obj_class = $obj->getClassName();

		for( $ii = 1; $ii <= count($apps); $ii++ )
		{
			if( $status[$ii] === 1 )
			{
				$customer_available = 1;
				break;
			}
			else
			{
				if( is_array($status[$ii]) && isset($status[$ii]['customer']) )
				{
					$customer_available = array(
						'customer'	=> $status[$ii]['customer']
						);
					break;
				}
				else
				{
					$customer_available = 0;
				}
			}
		}
		$customer_available = array(
			$cid	=> $customer_available,
			);
		?>

		<dl class="<?php echo $dl_class; ?>">
			<dt>
				<?php echo M('Customer'); ?>
			</dt>
			<dd>
				<?php
				echo $this->render_file(
					dirname(__FILE__) . '/../../views/_object.php',
					array(
						'obj_class'	=> 'customer',
						'obj'		=> $obj,
						'available'	=> $customer_available,
						'this_id'	=> $cid,
						'all_ids'	=> $locs,
						)
					);
				?>
			</dd>
		</dl>
	</li>

	<li>
		<dl class="<?php echo $dl_class; ?>">
		<dt>
			<?php echo (count($apps) > 1) ? M('Appointments') : M('Appointment'); ?>
		</dt>

		<dd>
		<ul class="list-unstyled">
		<?php for( $ii = 1; $ii <= count($apps); $ii++ ) : ?>
			<?php
			$a = $apps[$ii-1];
			$t->setTimestamp( $a['starts_at'] );
			$my_errors = array();

			if( $status[$ii] === 1 )
				$class = 'success';
			else
			{
				if( is_array($status[$ii]) )
					$my_errors = $status[$ii];
				if( $status[$ii] )
				{
					$class = 'danger';
				}
				else
					$class = 'archive';
			}
			$date_view = $t->formatDateFull();
			$time_view = $t->formatTime();
			$t->modify( '+ ' . $a['duration'] . ' seconds' );
			$time_view .= ' - ' . $t->formatTime();
			if( $a['lead_out'] )
			{
				$t->modify( '+ ' . $a['lead_out'] . ' seconds' );
				$time_view .= ' [' . $t->formatTime() . ']';
			}

			$display_common_errors = array();
			reset( $common_errors );
			foreach( $common_errors as $ce )
			{
				if( isset($my_errors[$ce]) )
				{
					$display_common_errors[] = $my_errors[$ce];
				}
			}

			$collapsible = ( $display_common_errors OR (count($locs) > 1) OR (count($ress) > 1) OR (count($sers) > 1) ) ? TRUE : FALSE;
			?>
			<li class="collapse-panel panel panel-group panel-<?php echo $class; ?>">
				<div class="panel-heading">
					<h4 class="panel-title">
						<?php if( $collapsible ) : ?>
							<a href="#" data-toggle="collapse-next">
						<?php endif; ?>
							<i class="fa fa-fw fa-calendar"></i><?php echo $date_view; ?>
							<i class="fa fa-fw fa-clock-o"></i><?php echo $time_view; ?>
						<?php if( $collapsible ) : ?>
							</a>
						<?php endif; ?>
					<a class="close text-danger" href="<?php echo ntsLink::makeLink('-current-', 'remove', array('ai' => $ii)); ?>" title="<?php echo M('Remove'); ?>">
						<i class="fa fa-times text-danger"></i>
					</a>
					</h4>
				</div>

				<div class="panel-collapse collapse<?php if( $my_errors ){echo ' in';}; ?>">
					<div class="panel-body">
						<ul class="list-unstyled">
							<?php if( $display_common_errors ) : ?>
								<?php foreach( $display_common_errors as $ce ) : ?>
									<li class="text-danger">
										<i class="fa fa-fw fa-exclamation-circle text-danger"></i><?php echo $ce; ?>
									</li>
								<?php endforeach; ?>
								<li><hr></li>
							<?php endif;  ?>

							<?php if( count($locs) > 1 ) : ?>
								<li>
								<?php
								$obj = ntsObjectFactory::get('location');
								$obj->setId( $a['location_id'] );
								$obj_class = $obj->getClassName();

								$my_child_file = dirname(__FILE__) . '/../../views/_' . $obj_class . '_child.php';
								$child_file = file_exists($my_child_file) ? $my_child_file : $generic_child_file;

								echo $this->render_file(
									$child_file,
									array(
										'link'			=> 0,
										'obj'			=> $obj,
										'no_reset'		=> 1,
										'errors'		=> $my_errors,
										'a'				=> $a,
										)
									);
								?>
								</li>
							<?php endif; ?>

							<?php if( count($ress) > 1 ) : ?>
								<li>
								<?php
								$obj = ntsObjectFactory::get('resource');
								$obj->setId( $a['resource_id'] );
								$obj_class = $obj->getClassName();

								$my_child_file = dirname(__FILE__) . '/../../views/_' . $obj_class . '_child.php';
								$child_file = file_exists($my_child_file) ? $my_child_file : $generic_child_file;

								echo $this->render_file(
									$child_file,
									array(
										'link'			=> 0,
										'obj'			=> $obj,
										'no_reset'		=> 1,
										'errors'		=> $my_errors,
										'a'				=> $a,
										)
									);
								?>
								</li>
							<?php endif; ?>

							<?php if( count($sers) > 1 ) : ?>
								<li>
								<?php
								$obj = ntsObjectFactory::get('service');
								$obj->setId( $a['service_id'] );
								$obj_class = $obj->getClassName();

								$my_child_file = dirname(__FILE__) . '/../../views/_' . $obj_class . '_child.php';
								$child_file = file_exists($my_child_file) ? $my_child_file : $generic_child_file;

								echo $this->render_file(
									$child_file,
									array(
										'link'			=> 0,
										'obj'			=> $obj,
										'no_reset'		=> 1,
										'errors'		=> $my_errors,
										'a'				=> $a,
										)
									);
								?>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</div>
			</li>
		<?php endfor; ?>

		<?php
		$default_params = array();
		if( $apps )
		{
			$last_app = $apps[count($apps)-1];
			if( count($locs) > 1 )
			{
				$default_params['location_id'] = $last_app['location_id'];
			}
			if( count($ress) > 1 )
			{
				$default_params['resource_id'] = $last_app['resource_id'];
			}
			$default_params['service_id'] = $last_app['location_id'];
			$t->setTimestamp( $last_app['starts_at'] );
			$default_params['cal'] = $t->formatDate_Db();
		}
		$more_link = ntsLink::makeLink(
			'-current-/..',
			'',
			$default_params
			)
		?>
		<li>
			<a href="<?php echo $more_link; ?>" class="btn btn-default btn-sm">
				<i class="fa fa-plus"></i> <?php echo M('More'); ?>
			</a>
		</li>

		</ul>
		</dd>
		</dl>
	</li>

	<li class="divider"></li>
</ul>

<?php $form->display(); ?>