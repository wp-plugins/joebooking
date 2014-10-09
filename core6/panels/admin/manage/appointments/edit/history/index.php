<?php
$t = $NTS_VIEW['t'];
?>
<ul class="list-unstyled">
<?php foreach( $logs as $ts => $e ) : ?>
	<?php
	$t->setTimestamp( $ts );
	$user = new ntsUser;
	$user->setId( $e[0]['user_id'] );
	?>
	<li>

		<ul class="list-group">

		<li>
			<h4>
				<?php echo $t->formatDateFull(); ?>, <?php echo $t->formatTime(); ?>
				<small>
				<?php echo ntsView::objectTitle($user, TRUE); ?>
				</small>
			</h4>
		</li>

		<?php foreach( $e as $change ) : ?>
			<li class="list-group-item">
				<ul class="list-inline list-separated">
					<li>
						<?php echo $object->propView( $change['property_name'], $change['new_value'], TRUE ); ?>
					</li>
					<?php if( ! in_array($change['property_name'], array('approved', 'completed', 'id')) ) : ?>
						<li>
							<i class="fa fa-arrow-left text-success"></i>
						</li>
						<li class="text-through text-muted">
							<?php echo $object->propView( $change['property_name'], $change['old_value'], TRUE ); ?>
						</li>
					<?php endif; ?>
				</ul>
			</li>
		<?php endforeach; ?>

		<?php if( isset($change['description']) && strlen($change['description']) ) : ?>
			<li class="list-group-item">
				<i class="fa fa-fw fa-comment-o"></i> 
				<em>
				<?php echo $change['description']; ?>
				</em>
			</li>
		<?php endif; ?>

		</ul>
	</li>
<?php endforeach; ?>
</ul>