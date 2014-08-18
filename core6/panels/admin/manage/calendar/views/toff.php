<?php
$t = $NTS_VIEW['t'];

$collapse_in = $checkbox ? ' in' : '';
$condensed = $checkbox ? '' : ' panel-condensed';

$half_column = 'col-md-6 col-sm-12';

$resource = ntsObjectFactory::get('resource');
$resource->setId( $toff->getProp('resource_id') );

$title = array();
/* prefill titles */
$title['resource'] = ntsView::objectTitle( $resource, TRUE );

$t->setTimestamp( $toff->getProp('starts_at') );
$toff_start_date = $t->formatDate_Db();
$t->setTimestamp( $toff->getProp('ends_at') );
$toff_end_date = $t->formatDate_Db();

$title['time'] = '<i class="fa fa-coffee"></i> ' . $t->formatTime();

$time_label = '';
if( 
	($toff_start_date == $toff_end_date) && 
	($toff_start_date == $date)
	)
{
	$duration = $toff->getProp('ends_at') - $toff->getProp('starts_at');
	$t->setTimestamp( $toff->getProp('starts_at') );
	$time_label = $t->formatTime( $duration );
}
elseif( 
	($toff_start_date < $date) && 
	($toff_end_date > $date)
	)
{
	$time_label = M('All Day');
}
elseif( 
	($toff_start_date < $date) && 
	($toff_end_date == $date)
	)
{
	$t->setTimestamp( $toff->getProp('ends_at') );
	$time_label = ' &laquo; ' . $t->formatTime();
}
elseif( 
	($toff_start_date == $date) && 
	($toff_end_date > $date)
	)
{
	$t->setTimestamp( $toff->getProp('starts_at') );
	$time_label = $t->formatTime() . ' &raquo; ';
}

$title['time'] = '<i class="fa fa-coffee"></i> ' . $time_label;

$final_title = array();

$label = 'time';
list( $this_title, $this_icon ) = Hc_lib::parse_icon( $title[$label] );
$final_title[] = '<span title="' . $this_title . '" class="text-underline squeeze-in">';
$final_title[] = $this_icon . $this_title;
if( $collapse_in )
	$final_title[] = ' <span class="caret"></span>';
$final_title[] = '</span>';

$show_title = join( '', $final_title );
?>
<?php
/* MENU & MORE INFO */
$menu = array();
$more = array(); 

$parent_class = '';
$conflicts = $toff->get_conflicts();
if( $conflicts )
{
	$parent_class = ' panel-danger-o';
	$time_label .= '<span class="label label-danger" style="margin-left: 0.5em;" title="' . M('Conflicts') . '">' . '<i class="fa fa-exclamation-circle"></i>' . '</span>';
	$more[] = '<i class="fa fa-exclamation-circle text-danger"></i> ' . M('Appointments');
	$more[] = '-divider-';
}


/* MORE INFO */
$description = $toff->getProp('description');
if( $description )
	$more[] = $description;

/* EDIT */
//$menu[] = '-divider-';
$menu[] = array(
	'href'	=> ntsLink::makeLink('admin/manage/schedules/timeoff/edit', '', array('_id' => $toff->getId()) ),
	'title'	=> '<i class="fa fa-edit"></i> ' . M('Edit'),
	'class'	=> 'hc-parent-loader'
	);

/* DELETE */
//$menu[] = '-divider-';
$menu[] = array(
	'href'	=> ntsLink::makeLink('admin/manage/schedules/timeoff/edit/delete', 'confirm', array('_id' => $toff->getId()) ),
	'title'	=> '<i class="fa fa-times text-danger"></i> ' . M('Delete'),
	'class'	=> 'hc-confirm',
	);
?>

<div class="collapse-panel panel<?php echo $condensed; ?> panel-default panel-inverse<?php echo $parent_class; ?>">
	<?php if( $collapse_in && $menu ) : ?>
		<div class="panel-heading">
	<?php else : ?>
		<div class="panel-heading squeeze-in">
	<?php endif; ?>
		<?php if( $collapse_in && $menu ) : ?>
			<span class="dropdown">
				<a href="#" data-toggle="dropdown" class="dropdown-toggle">
					<?php echo $show_title; ?>
				</a>
				<?php echo Hc_html::dropdown_menu($menu); ?>
			</span>
		<?php else : ?>
			<a href="#" data-toggle="collapse-next">
				<?php echo $show_title; ?>
			</a>
		<?php endif; ?>
	</div>

	<div class="panel-collapse collapse<?php echo $collapse_in; ?>">
		<?php if( $more ) : ?>
			<div class="panel-body squeeze-in">
				<?php echo Hc_html::dropdown_menu($more, 'list-unstyled list-separated'); ?>
			</div>
		<?php endif; ?>

		<?php if( $menu && (! $collapse_in) ) : ?>
			<div class="panel-footer">
				<div class="btn-group">
					<a class="dropdown-toggle btn btn-default" href="#" data-toggle="dropdown">
						<i class="fa fa-fw fa-cog"></i><?php echo M('Edit'); ?> <span class="caret"></span>
					</a>
					<?php echo Hc_html::dropdown_menu($menu); ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>