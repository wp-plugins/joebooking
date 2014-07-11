<?php
$ri = ntsLib::remoteIntegration();
$ntsdb =& dbWrapper::getInstance();
?>
<div class="row">
	<div class="col-md-4 col-xs-12 pull-right">
		<ul class="nav nav-pills pull-right" style="margin: 0 0;">
			<?php require( dirname(__FILE__) . '/user-info-lang.php' ); ?>
			<li>
				<span class="nav-item smaller text-muted">
					<?php echo $t->formatDateFull(); ?> <strong><?php echo $t->formatTime(); ?></strong>
				</span>
			</li>
		</ul>
	</div>

	<div class="col-md-8 col-xs-12">
		<ul class="nav nav-pills" style="margin: 0 0;">
			<?php if( ntsLib::getCurrentUserId() ) : ?>
				<?php if( $NTS_CURRENT_USER->hasRole('admin') ) : ?>
					<?php if( ! $ri ) : ?>
						<?php require( dirname(__FILE__) . '/user-info-admin.php' ); ?>
					<?php elseif( $ri == 'wordpress' ) : ?>
						<?php require( dirname(__FILE__) . '/user-info-admin-wp.php' ); ?>
					<?php endif; ?>
				<?php elseif( $NTS_CURRENT_USER->hasRole('customer') ) : ?>
					<?php require( dirname(__FILE__) . '/user-info-customer.php' ); ?>
				<?php endif; ?>

			<?php else: ?>
				<?php require( dirname(__FILE__) . '/user-info-anon.php' ); ?>
			<?php endif; ?>
		</ul>
	</div>
</div>

<hr>