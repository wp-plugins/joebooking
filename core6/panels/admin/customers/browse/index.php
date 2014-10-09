<?php
$entries = $this->data['entries'];
$grandTotalCount = $NTS_VIEW['grandTotalCount'];

$returnTo = ntsLib::getVar('admin::returnTo');
$returnToParams = ntsLib::getVar('admin::returnToParams');
$upcomingCount = ntsLib::getVar( 'admin/customers::upcomingCount' );
$oldCount = ntsLib::getVar( 'admin/customers::oldCount' );

include_once( NTS_LIB_DIR . '/lib/view/ntsPager.php' );
$pager = new ntsPager( $NTS_VIEW['totalCount'], $NTS_VIEW['showPerPage'], 5 );
$pager->setPage( $NTS_VIEW['currentPage'] );

$pages = $pager->getPages();
reset( $pages );
$pagerParams = array();
if( $NTS_VIEW['search'] )
	$pagerParams['search'] = $NTS_VIEW['search'];
$totalCols = 4;
if( ! NTS_EMAIL_AS_USERNAME )
	$totalCols++;

$filter = ntsLib::getVar( 'admin/customers/browse::filter' );
$filter_options = array(
	'all'		=>	M('All'),
	'new'		=>	M('New'),
	'active'	=>	M('Most Active'),
	'recent'	=>	M('Recent'),
	);

if( $restricted_count )
{
	foreach( $restricted_count as $rk => $rv )
	{
		$filter_options[$rk] = ntsUser::_statusLabel( array($rk) ) . ' ' . '[' . $rv . ']';
	}
}
?>

<div class="row">
	<?php if( count($pages) > 1 ) : ?>
		<div class="col-md-5 col-xs-12 pull-right text-right">
			<ul class="pagination pagination-sm">
				<li class="disabled">
					<a>[<?php echo $NTS_VIEW['showFrom']; ?> - <?php echo $NTS_VIEW['showTo']; ?> of <?php echo $NTS_VIEW['totalCount']; ?>]</a>
				</li>

			<?php if( count($pages) > 1 ) : ?>
				<?php foreach( $pages as $pi ): ?>
					<?php if( $NTS_VIEW['currentPage'] != $pi['number'] ) : ?>
						<?php $pagerParams['p'] = $pi['number']; ?>
						<li>
							<a href="<?php echo ntsLink::makeLink('-current-', '', $pagerParams ); ?>"><?php echo $pi['title']; ?></a>
						</li>
					<?php else : ?>
						<li class="active">
							<a href="<?php echo ntsLink::makeLink('-current-', '', $pagerParams ); ?>"><?php echo $pi['title']; ?></a>
						</li>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</ul>
		</div>
	<?php endif; ?>

	<div class="col-md-7 col-xs-12">
		<ul class="list-inline list-separated">
			<li>
				<a class="btn btn-success nts-no-ajax" href="<?php echo ntsLink::makeLink('-current-/../create'); ?>">
					<i class="fa fa-plus"></i> <?php echo M('Customer'); ?>
				</a>
			</li>

			<?php if( $grandTotalCount > 1 ) : ?>
				<li class="divider hidden-xs">&nbsp;</li>
				<li>
					<div class="btn-group">
						<?php if( $filter == 'all' ) : ?>
							<button class="btn btn-default">
								<?php echo M('Filter'); ?>
							</button>
						<?php else :  ?>
							<span class="btn btn-info">
								<?php echo $filter_options[$filter]; ?>
							</span>
						<?php endif; ?>
						<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
							&nbsp;<span class="caret"></span>
						</button>

						<?php unset( $filter_options[$filter] ); ?>
						<ul class="dropdown-menu">
							<?php foreach( $filter_options as $k => $v ) : ?>
								<li>
									<a href="<?php echo ntsLink::makeLink('-current-', '', array('cfilter' => $k, 'search' => '' )); ?>"><?php echo $v; ?></a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</li>
			<?php endif; ?>

			<?php if( ! $returnTo ) : ?>
				<li>
					<?php if( $grandTotalCount > 0 ) : ?>
						<div class="dropdown">
							<a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-cog"></i> <?php echo M('Actions'); ?> <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li>
									<a href="<?php echo ntsLink::makeLink('admin/customers/import'); ?>"><i class="fa fa-arrow-up"></i> <?php echo M('Import'); ?></a>
								</li>
								<li>
									<a href="<?php echo ntsLink::makeLink('admin/customers/browse', 'export'); ?>"><i class="fa fa-arrow-down"></i> <?php echo M('Export'); ?></a>
								</li>
							</ul>
						</div>
					<?php else : ?>
						<a class="btn btn-default" href="<?php echo ntsLink::makeLink('admin/customers/import'); ?>"><i class="fa fa-arrow-up"></i> <?php echo M('Import'); ?></a>
					<?php endif; ?>
				</li>
			<?php endif; ?>

			<?php if( $grandTotalCount > 1 ) : ?>
				<?php if( ! ($filter != 'all') ) : ?>
					<li>
						<?php $NTS_VIEW['searchForm']->display(); ?>
					</li>
				<?php endif; ?>
			<?php endif; ?>
		</ul>
	</div>
</div>

<?php if( ! count($entries) ) : ?>
	<?php if( $grandTotalCount > 0 ) : ?>
		<p>
		<?php echo M('None'); ?>
		</p>
	<?php endif; ?>
<?php endif; ?>

<?php
if( 0 && $returnTo )
{
	$per_row = 6;
	$span = 'col-md-2 col-sm-4';
}
else
{
	$per_row = 4;
	$span = 'col-md-3 col-sm-6';
}
$row_open = FALSE;
?>

<?php for( $ii = 1; $ii <= count($entries); $ii++ ) : ?>
<?php
		$e = $entries[$ii - 1];
		$objId = $e->getId();
		$restrictions = $e->getProp('_restriction');
		if( $returnTo )
		{
			$params = array(
				NTS_PARAM_RETURN	=> '-reset-',
				'customer_id'		=> $e->getId(),
				'_id'				=> $NTS_VIEW['skip']
				);
			$params = array_merge( $returnToParams, $params );

			$targetLink = ntsLink::makeLink(
				$returnTo,
				'',
				$params
				);
		}
		else
		{
			$targetLink = ntsLink::makeLink( '-current-/../edit/edit', '', array('_id' => $e->getId()) );
		}

		$notes = $e->getProp('_note');

	/* count appointments */
		$totalCount = 0;
		if( isset($upcomingCount[$objId]) )
			$totalCount += $upcomingCount[$objId];
		if( isset($oldCount[$objId]) )
			$totalCount += $oldCount[$objId];
?>

<?php if( 1 == ($ii % $per_row) ) : ?>
	<div class="row">
	<?php $row_open = TRUE; ?>
<?php endif; ?>

	<div class="<?php echo $span; ?>">
		<?php
//			$class = array( 'alert', 'alert-regular', 'squeeze-in' );
			$class = array( 'alert', 'alert-regular' );
			if( $restrictions )
			{
				list( $alert, $cssClass, $message ) = $e->getStatus();
				$class[] = $alert ? 'alert-' . $cssClass : 'alert-info';
				$title = $message;
			}
			else
			{
				$class[] = 'alert-success';
				$title = M('OK');
			}
			$class = join( ' ', $class );
		?>
		<div class="<?php echo $class; ?>" title="<?php echo $title; ?>">
			<?php
//			$child_file = $returnTo ? '_index_child_short.php' : '_index_child_main.php';
			$child_file = '_index_child_main.php';
			require( dirname(__FILE__) . '/' . $child_file );
			?>
		</div>
	</div>

<?php if( ! ($ii % $per_row) ) : ?>
	</div>
	<?php $row_open = FALSE; ?>
<?php endif; ?>

<?php endfor; ?>
<?php if( $row_open ) : ?>
</div>
<?php endif; ?>

<?php if( ntsLib::isAjax() ) : ?>
<script language="JavaScript">
jQuery(document).ready( function(){
	jQuery("#<?php echo $NTS_VIEW['searchForm']->getName(); ?>").live( 'submit', function(event) {
		/* stop form from submitting normally */
		event.preventDefault(); 

		/* get some values from elements on the page: */
		var thisForm = jQuery( this );
		var thisFormData = thisForm.serialize();
		if( thisForm.data('trigger') ){
			thisFormData += '&' + thisForm.data('trigger') + '=1';
			}
		thisFormData += '&<?php echo NTS_PARAM_VIEW_MODE; ?>=ajax';

		var targetUrl = thisForm.attr( 'action' );
//		var resultDiv = thisForm.closest('.nts-ajax-container');
		var resultDiv = thisForm.closest('.nts-ajax-return');

		/* Send the data using post and put the results in a div */
		jQuery.ajax({
			type: "GET",
			url: targetUrl,
			data: thisFormData
			})
			.done( function(msg){
				resultDiv.html( msg );
				});
		return false;
		});
	});
</script>
<?php endif; ?>