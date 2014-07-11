<ul class="list-unstyled">
	<li class="squeeze-in">
	<?php if( $returnTo ) : ?>
		<a class="nts-target-parent2" href="<?php echo $targetLink; ?>">
	<?php else : ?>
		<a class="" href="<?php echo $targetLink; ?>">
	<?php endif; ?>
		<strong><?php echo ntsView::objectTitle($e); ?></strong>
	</a>
	</li>

	<?php
	$email = $e->getProp('email');
	$thisView = $email ? $email : '- ' .  M('No Email') . ' -';
	?>
	<li class="squeeze-in">
	<?php echo $thisView; ?>
	</li>

	<li>
		<ul class="list-inline">
			<li title="<?php echo M('Appointments'); ?>">
				<i class="fa fa-check-square-o"></i> <?php echo $totalCount; ?>
			</li>

		<?php if( $notes ) : ?>
			<li class="dropdown">
				<a class="dropdown-toggle" data-toggle="dropdown" href="#" title="<?php echo M('Notes'); ?>">
					<i class="fa fa-comment-o"></i> <?php echo count($notes); ?>
				</a>
				<ul class="dropdown-menu">
					<?php foreach( $notes as $noteText => $note ) : ?>
					<?php
							list( $noteTime, $noteUserId ) = explode( ':', $note );
							$noteUser = new ntsUser;
							$noteUser->setId( $noteUserId );
							$noteUserView = ntsView::objectTitle( $noteUser );
					?>
					<li>
						<span>
							<?php echo $noteUserView; ?>: <i><?php echo $noteText; ?></i>
						</span>
					</li>
					<?php endforeach; ?>
				</ul>
			</li>
		<?php endif; ?>

		</ul>
	</li>
</ul>