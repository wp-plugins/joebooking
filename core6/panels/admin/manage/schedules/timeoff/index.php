<?php
$entries = ntsLib::getVar( 'admin/manage/timeoff:entries' );
$schEdit = ntsLib::getVar( 'admin/manage:schEdit' );
$ress = ntsLib::getVar( 'admin::ress' );

$displayCreateLink = true;
$showNone = $displayCreateLink;
if( ! ( $schEdit && array_intersect($ress, $schEdit) ) )
	$displayCreateLink = false;	

$headers = array(
	'now'		=> M('Current'),
	'future'	=> M('Upcoming'),
	'past'		=> M('Past'),
	);
$classes = array(
	'now'		=> 'success',
	'future'	=> 'warning',
	'past'		=> 'default',
	);

$hide_cols = array();
if( count($ress) <= 1 )
{
	$hide_cols[] = 'resource';
}
?>

<?php require( dirname(__FILE__) . '/submenu.php' ); ?>

<?php if( $total_count <= 0 ) : ?>
	<p>
	<?php echo M('None'); ?>
	</p>
	<?php return; ?>
<?php endif; ?>

<ul class="list-unstyled">
	<?php foreach( $entries as $k => $sub_entries ) : ?>
		<?php if( $sub_entries ) : ?>
			<li>
				<h3>
					<?php echo $headers[$k]; ?>
				</h3>
			</li>
		<?php endif; ?>

		<?php foreach( $sub_entries as $b ) : ?>
			<?php
			$iCanEdit = in_array($b->getProp('resource_id'), $schEdit );
			$editLink = ntsLink::makeLink( 
				'admin/manage/schedules/timeoff/edit',
				'',
				array(
					'_id'	=> $b->getId(),
					)
				);
			?>
			<li class="nts-ajax-parent">
				<div class="panel panel-<?php echo $classes[$k]; ?>">
					<div class="panel-heading">
						<div class="row">
							<div class="col-md-6">
								<?php if( $iCanEdit ) : ?>
									<a href="<?php echo $editLink; ?>" class="nts-ajax-loader nts-ajax-scroll">
								<?php endif; ?>
								<?php echo $b->time_view(); ?>
								<?php if( $iCanEdit ) : ?>
									</a>
								<?php endif; ?>
							</div>
							<?php if( ! in_array('resource', $hide_cols) ) : ?>
								<div class="col-md-6">
									<?php
									$resource = ntsObjectFactory::get('resource');
									$resource->setId( $b->getProp('resource_id') );
									?>
									<?php echo ntsView::objectTitle( $resource, TRUE ); ?>
								</div>
							<?php endif; ?>

							<?php if( $b->getProp('description') ) : ?>
								<div class="col-md-12 text-italic">
									<?php echo $b->getProp('description'); ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
					<div class="panel-body nts-ajax-container">
					</div>
				</div>
			</li>
		<?php endforeach; ?>
	<?php endforeach; ?>
</ul>