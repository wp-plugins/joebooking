<?php
global $NTS_VIEW;
$viewMode = $NTS_VIEW[NTS_PARAM_VIEW_MODE];
if( $viewMode == 'print' ){
	return;
}

$locs = ntsLib::getVar( 'admin::locs' );
$ress = ntsLib::getVar( 'admin::ress' );
$sers = ntsLib::getVar( 'admin::sers' );

$locs2 = ntsLib::getVar( 'admin::locs2' );
$ress2 = ntsLib::getVar( 'admin::ress2' );
$sers2 = ntsLib::getVar( 'admin::sers2' );

$locs_all = ntsLib::getVar( 'admin::locs_all' );
$ress_all = ntsLib::getVar( 'admin::ress_all' );
$sers_all = ntsLib::getVar( 'admin::sers_all' );

/* check out archived resources */
$ress_archive = ntsLib::getVar( 'admin::ress_archive' );
if( $ress_archive )
{
	$ress_all = array_diff( $ress_all, $ress_archive );
	$ress_all = array_values( $ress_all );
}

/* check out archived locations */
$locs_archive = ntsLib::getVar( 'admin::locs_archive' );
if( $locs_archive )
{
	$locs_all = array_diff( $locs_all, $locs_archive );
	$locs_all = array_values( $locs_all );
}
?>
<div>
	<div class="row">
		<div class="col-md-5 col-xs-12 pull-right text-right pull-xs-left text-xs-left">
			<ul class="list-inline list-separated">
				<?php if( $display == 'calendar' ) : ?>
					<li>
						<?php require( dirname(__FILE__) . '/_date_navigation.php' ); ?>
					</li>
				<?php else : ?>
					<li>
						<?php require( dirname(__FILE__) . '/_date_range.php' ); ?>
					</li>
					<li class="divider hidden-xs">&nbsp;</li>
					<li>
						<?php require( dirname(__FILE__) . '/_export.php' ); ?>
					</li>
				<?php endif; ?>
			</ul>
		</div>

		<div class="col-md-7 col-xs-12">
			<ul class="list-inline list-separated">
				<?php if( $can_add ) : ?>
					<li>
						<?php require( dirname(__FILE__) . '/_add_link.php' ); ?>
					</li>
					<li class="divider hidden-xs">&nbsp;</li>
				<?php endif; ?>

				<?php if( (count($locs_all) > 1) OR (count($ress_all) > 1) ) : ?>
					<?php require( dirname(__FILE__) . '/_filter_selectors.php' ); ?>
					<li class="divider hidden-xs">&nbsp;</li>
				<?php endif; ?>
				<?php require( dirname(__FILE__) . '/_display.php' ); ?>
			</ul>
		</div>
	</div>

	<?php if( (count($locs_all) > 1) OR (count($ress_all) > 1) ) : ?>
		<div>
			<?php require( dirname(__FILE__) . '/_filter_dropdowns.php' ); ?>
		</div>
	<?php endif; ?>
</div>
