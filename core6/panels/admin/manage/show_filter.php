<?php
global $NTS_CURRENT_USER, $_NTS;
$app_info = ntsLib::getAppInfo();

$locs = ntsLib::getVar( 'admin::locs' );
$ress = ntsLib::getVar( 'admin::ress' );
$sers = ntsLib::getVar( 'admin::sers' );

$locs2 = ntsLib::getVar( 'admin::locs2' );
$ress2 = ntsLib::getVar( 'admin::ress2' );
$sers2 = ntsLib::getVar( 'admin::sers2' );

$locs_all = ntsLib::getVar( 'admin::locs_all' );
$ress_all = ntsLib::getVar( 'admin::ress_all' );
$sers_all = ntsLib::getVar( 'admin::sers_all' );

$filter = ntsLib::getVar( 'admin/manage:filter' );
$tm2 = ntsLib::getVar( 'admin::tm2' );

if( count($filter) || (count($locs_all) > 1) || (count($ress_all) > 1) || (count($sers_all) > 1) )
{
	$showFilter = TRUE;
}
else
{
	$showFilter = FALSE;
}

if( isset($app_info['disabled_features']['flex_service']) )
{
	$showFilter = FALSE;
}

$current_filter = array();
for( $fi = 0; $fi < count($filter); $fi++ )
{
	$thisFilter = $filter;
	unset( $thisFilter[$fi] );
	$thisFilter = array_values( $thisFilter );

	$fp = $filter[$fi];
	$fclass = substr( $fp, 0, 1 );
	$fid = substr( $fp, 1 );

	$classes = array(
		's'	=> array( 'service', 'Service' ),
		'r'	=> array( 'resource', 'Bookable Resource' ),
		'l'	=> array( 'location', 'Location' ),
		'c'	=> array( 'user', 'Customer' ),
		);
	if( ! isset($classes[$fclass]) )
		continue;
	$className = $classes[$fclass][0];
	$title = M( $classes[$fclass][1] );

	switch( $className )
	{
		case 'user':
			$obj = new ntsUser();
			$obj->setId( $fid );
			$objectView = '' . M('Customer') . '' . ': ' . ntsView::objectTitle($obj) . '</b>';
			break;
		default:
			$obj = ntsObjectFactory::get( $className );
			$obj->setId( $fid );
//			$objectView = $title . ': ' . '<b>' . ntsView::objectTitle($obj) . '</b>';
			$objectView = ntsView::objectTitle($obj);
			break;
	}
	$clear_link = ntsLink::makeLink('-current-', '', array('nts-filter' => join('-', $thisFilter)));
	$current_filter[ $fclass ] = array( $objectView, $clear_link );
}
?>

<?php if( $showFilter ) : ?>
<!-- SELECTORS -->
	<div>
		<ul class="list-inline" style="margin: 0 0;">

		<?php if( isset($current_filter['l']) ) : ?>
			<li>
				<div class="btn-group">
					<span class="btn btn-default btn-label" title="<?php echo $current_filter['l'][0]; ?>">
						<i class="fa fa-home"></i> <?php echo $current_filter['l'][0]; ?>
					</span>
					<?php if(count($locs_all) > 1) : ?>
						<a class="btn btn-default" href="<?php echo $current_filter['l'][1]; ?>">
							<span class="close2 text-danger">&times;</span>
						</a>
					<?php endif; ?>
				</div>
			</li>
		<?php elseif( (count($locs2) == 1) && (count($locs_all) > 1) ) : ?>
			<?php
			$obj = ntsObjectFactory::get( 'location' );
			$obj->setId( $locs2[0] );
			?>
			<li>
				<div class="btn-group">
					<span class="btn btn-default btn-label" title="<?php echo ntsView::objectTitle($obj); ?>">
						<?php echo ntsView::objectTitle($obj, TRUE); ?>
					</span>
				</div>
			</li>
		<?php elseif( count($locs2) > 1 ) : ?>
			<li>
				<a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#" data-target="#ntsFilterLocation">
					- <?php echo M('Location'); ?> - <span class="caret"></span>
				</a>
			</li>
		<?php endif; ?>

		<?php if( isset($current_filter['r']) ) : ?>
			<li>
				<div class="btn-group">
					<span class="btn btn-default btn-label" title="<?php echo $current_filter['r'][0]; ?>">
						<i class="fa fa-hand-o-up"></i> <?php echo $current_filter['r'][0]; ?>
					</span>
					<a class="btn btn-default" href="<?php echo $current_filter['r'][1]; ?>">
						<span class="close2 text-danger">&times;</span>
					</a>
				</div>
			</li>
		<?php elseif( (count($ress2) == 1) && (count($ress_all) > 1) ) : ?>
			<?php
			$obj = ntsObjectFactory::get( 'resource' );
			$obj->setId( $ress2[0] );
			?>
			<li>
				<div class="btn-group">
					<span class="btn btn-default btn-label" title="<?php echo ntsView::objectTitle($obj); ?>">
						<?php echo ntsView::objectTitle($obj, TRUE); ?>
					</span>
				</div>
			</li>
		<?php elseif( count($ress2) > 1 ) : ?>
			<li>
				<a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#" data-target="#ntsFilterResource">
					- <?php echo M('Bookable Resource'); ?> - <span class="caret"></span>
				</a>
			</li>
		<?php endif; ?>

		<?php if( isset($current_filter['s']) ) : ?>
			<li>
				<div class="btn-group">
					<span class="btn btn-default btn-label" title="<?php echo $current_filter['s'][0]; ?>">
						<i class="fa fa-tags"></i> <?php echo $current_filter['s'][0]; ?>
					</span>
					<a class="btn btn-default" href="<?php echo $current_filter['s'][1]; ?>">
						<span class="close2 text-danger">&times;</span>
					</a>
				</div>
			</li>
		<?php elseif( (count($sers2) == 1) && (count($sers_all) > 1) ) : ?>
			<?php
			$obj = ntsObjectFactory::get( 'service' );
			$obj->setId( $sers2[0] );
			?>
			<li>
				<div class="btn-group">
					<span class="btn btn-default btn-label" title="<?php echo ntsView::objectTitle($obj); ?>">
						<?php echo ntsView::objectTitle($obj, TRUE); ?>
					</span>
				</div>
			</li>
		<?php endif; ?>

		<?php if( count($sers2) > 1 ) : ?>
			<li>
				<a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#" data-target="#ntsFilterService">
					- <?php echo M('Service'); ?> - <span class="caret"></span>
				</a>
			</li>
		<?php endif; ?>
		</ul>
	</div>
<!-- END OF SELECTORS -->

<!-- DROPDOWNS -->
	<div>
	<?php if( count($locs2) > 1 ) : ?>
		<div class="dropdown" id="ntsFilterLocation">
			<ul class="dropdown-menu dropdown-menu-hori">
<?php		foreach( $locs2 as $objId ) : ?>
<?php
				$obj = ntsObjectFactory::get( 'location' );
				$obj->setId( $objId );
				$thisFilter = array_merge( $filter, array('l' . $obj->getId()) );
				$thisFilter = array_unique($thisFilter);
?>
				<li>
					<a title="<?php echo ntsView::objectTitle($obj); ?>" href="<?php echo ntsLink::makeLink('-current-', '', array('nts-filter' => join('-', $thisFilter) )); ?>"><i class="fa fa-home"></i> <?php echo ntsView::objectTitle($obj); ?></a>
				</li>
<?php		endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	<?php if( count($ress2) > 1 ) : ?>
		<div class="dropdown" id="ntsFilterResource">
			<ul class="dropdown-menu dropdown-menu-hori">
	<?php		foreach( $ress2 as $objId ) : ?>
	<?php
					$obj = ntsObjectFactory::get( 'resource' );
					$obj->setId( $objId );
					$thisFilter = array_merge( $filter, array('r' . $obj->getId()) );
					$thisFilter = array_unique($thisFilter);
	?>
					<li>
						<a title="<?php echo ntsView::objectTitle($obj); ?>" href="<?php echo ntsLink::makeLink('-current-', '', array('nts-filter' => join('-', $thisFilter) )); ?>"><i class="fa fa-hand-o-up"></i> <?php echo ntsView::objectTitle($obj); ?></a>
					</li>
	<?php		endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	<?php if( count($sers2) > 1 ) : ?>
		<div class="dropdown" id="ntsFilterService">
			<ul class="dropdown-menu dropdown-menu-hori">
	<?php		foreach( $sers2 as $objId ) : ?>
	<?php
					$obj = ntsObjectFactory::get( 'service' );
					$obj->setId( $objId );
					$thisFilter = array_merge( $filter, array('s' . $obj->getId()) );
					$thisFilter = array_unique($thisFilter);
	?>
					<li>
						<a title="<?php echo ntsView::objectTitle($obj); ?>" href="<?php echo ntsLink::makeLink('-current-', '', array('nts-filter' => join('-', $thisFilter) )); ?>"><?php echo ntsView::objectTitle($obj, TRUE); ?></a>
					</li>
	<?php		endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	</div>
<!-- END OF DROPDOWNS -->

<?php endif; ?>