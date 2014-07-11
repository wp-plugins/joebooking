<?php
$t = $NTS_VIEW['t'];

$locs = ntsLib::getVar( 'admin::locs' );
$ress = ntsLib::getVar( 'admin::ress' );
$sers = ntsLib::getVar( 'admin::sers' );

$use_color_code = TRUE;
$use_color_code = FALSE;
if( ! isset($wide_view) )
{
	$wide_view = FALSE;
}

if( ! isset($condensed_view) )
{
	$condensed_view = FALSE;
}

$location = ntsObjectFactory::get('location');
$location->setId( $app->getProp('location_id') );

$resource = ntsObjectFactory::get('resource');
$resource->setId( $app->getProp('resource_id') );

$service = ntsObjectFactory::get('service');
$service->setId( $app->getProp('service_id') );

$customer = new ntsUser();
$customer->setId( $app->getProp('customer_id') );

/* CLASS */
if( $condensed_view )
	$half_column = 'col-md-6 col-sm-12';
else
	$half_column = 'col-sm-6 col-xs-12';

$link_class = array();
$class = array();

$link_class[] = 'dropdown-toggle';
$class[] = 'alert';
if( $condensed_view )
	$class[] = 'alert-condensed';

$status_class = $app->statusClass();
$class[] = 'alert-' . $status_class;
$link_class[] = 'alert-' . $status_class;

$class = join( ' ', $class );
$link_class = join( ' ', $link_class );

/* TITLES */
$title = array();

/* TITLE - TIME */
if( in_array('time', $titles) )
{
	$t->setTimestamp( $app->getProp('starts_at') );
	$time_title = $t->formatTime( $app->getProp('duration') );

	if( in_array('date', $titles) )
	{
		$time_title = '<i class="fa fa-fw fa-clock-o"></i>' . $time_title;
	}
	elseif( ! $condensed_view )
	{
		$time_title = '<i class="fa fa-fw fa-clock-o"></i>' . $time_title;
	}
	$title['time'] = $time_title;
}

/* TITLE - LOCATION */
if( in_array('location', $titles) )
{
	$title['location'] = ntsView::objectTitle( $location, TRUE );
}

/* TITLE - RESOURCE */
if( in_array('resource', $titles) )
{
	$title['resource'] = ntsView::objectTitle( $resource, TRUE );
}

/* TITLE - SERVICE */
if( in_array('service', $titles) )
{
	$title['service'] = ntsView::objectTitle( $service, TRUE );
}

/* TITLE - CUSTOMER */
if( in_array('customer', $titles) )
{
	$title['customer'] = ntsView::objectTitle( $customer, TRUE );
}

$final_title = array();
$final_title[] = '<ul class="list-unstyled">';

if( $wide_view && ( isset($title['resource']) OR isset($title['location']) OR isset($title['customer']) ) )
{
	$final_title[] = '<li>';
	$final_title[]		= '<ul class="list-unstyled row">';

							list( $this_title_title, $this_title_icon ) = Hc_lib::parse_icon( $title['time'] );
	$final_title[]			= '<li class="' . $half_column . ' squeeze-in text-underline" title="' . $this_title_title . '">';
	$final_title[]				= $title['time'];
	$final_title[]			= '</li>';

							$this_title = '&nbsp;';
							if( isset($title['customer']) )
								$this_title = $title['customer'];
							elseif( isset($title['service']) )
								$this_title = $title['service'];
//							elseif( isset($title['location']) )
//								$this_title = $title['location'];
							else
								$this_title = '';
							list( $this_title_title, $this_title_icon ) = Hc_lib::parse_icon( $this_title );
	$final_title[]			= '<li class="' . $half_column . ' squeeze-in" title="' . $this_title_title . '">';
	$final_title[]				= $this_title;
	$final_title[]			= '</li>';

	$final_title[]		= '</ul>';
	$final_title[] = '</li>';
}
else
{
	if( isset($title['time']) )
	{
		list( $this_title, $this_icon ) = Hc_lib::parse_icon( $title['time'] );
		if( in_array('date', $titles) )
		{
			$date_title = $t->formatDateFull();
			$date_view = '<i class="fa-fw fa fa-calendar"></i>' . $date_title;
			$this_title = $date_title . ' ' . $this_title;
			$date_view = '<i class="fa-fw fa fa-calendar"></i>' . $date_title;
			$title['time'] = $date_view . '<br>' . $title['time'];
		}

		$final_title[] = '<li class="text-underline squeeze-in" title="' . $this_title . '">';
		$final_title[] = $title['time'];
		$final_title[] = '</li>';
	}
}

if( $wide_view && ( isset($title['resource']) && isset($title['location']) ) )
{
	$final_title[] = '<li>';
	$final_title[]		= '<ul class="list-unstyled row">';

							list( $this_title_title, $this_title_icon ) = Hc_lib::parse_icon( $title['location'] );
	$final_title[]			= '<li class="' . $half_column . ' squeeze-in" title="' . $this_title_title . '">';
	$final_title[]				= $title['location'];
	$final_title[]			= '</li>';

							list( $this_title_title, $this_title_icon ) = Hc_lib::parse_icon( $title['resource'] );
	$final_title[]			= '<li class="' . $half_column . ' squeeze-in" title="' . $this_title_title . '">';
	$final_title[]				= $title['resource'];
	$final_title[]			= '</li>';

	$final_title[]		= '</ul>';
	$final_title[]		= '<div class="clearfix"></div>';
	$final_title[] = '</li>';
}
else
{
	if( isset($title['customer']) )
	{
		list( $this_title, $this_icon ) = Hc_lib::parse_icon( $title['customer'] );
		$final_title[] = '<li class="squeeze-in" title="' . $this_title . '">';
		$final_title[] = $this_icon . $this_title;
		$final_title[] = '</li>';
	}

	if( isset($title['location']) )
	{
		list( $this_title_title, $this_title_icon ) = Hc_lib::parse_icon( $title['location'] );
		$final_title[] = '<li class="squeeze-in" title="' . $this_title_title . '">';
		$final_title[] = $title['location'];
		$final_title[] = '</li>';
	}

	if( isset($title['resource']) )
	{
		list( $this_title_title, $this_title_icon ) = Hc_lib::parse_icon( $title['resource'] );
		$final_title[] = '<li class="squeeze-in" title="' . $this_title_title . '">';
		$final_title[] = $title['resource'];
		$final_title[] = '</li>';
	}

	if( isset($title['service']) )
	{
		list( $this_title_title, $this_title_icon ) = Hc_lib::parse_icon( $title['service'] );
		$final_title[] = '<li class="squeeze-in" title="' . $this_title_title . '">';
		$final_title[] = $title['service'];
		$final_title[] = '</li>';
	}
}

$final_title[] = '</ul>';

$show_title = join( '', $final_title );
?>

<?php
/* MENU */
$menu = array();

/* MORE INFORMATION */
$menu[] = array(
	'title'	=> M('Details'),
	);

if( ! isset($title['location']) )
	$menu[] = ntsView::objectTitle( $location, TRUE );
if( ! isset($title['resource']) )
	$menu[] = ntsView::objectTitle( $resource, TRUE );
$menu[] = ntsView::objectTitle( $service, TRUE );

if( (! isset($title['customer'])) && (! in_array('customer', $hide_cols)) )
	$menu[] = ntsView::objectTitle( $customer, TRUE );

/* STATUS ACTIONS */
$menu[] = '-divider-';
$menu[] = array(
	'href'	=> ntsLink::makeLink('admin/manage/appointments/edit/status', '', array('_id' => $app->getId()) ),
	'title'	=> '<i class="fa fa-flag-o"></i> ' . M('Set Status'),
	'title'	=> $app->statusLabel('', 'i') . ' ' . M('Change Status'),
	'class'	=> 'hc-ajax-loader'
	);

/* EDIT */
$menu[] = '-divider-';
$menu[] = array(
	'href'	=> ntsLink::makeLink('admin/manage/appointments/edit/overview', '', array('_id' => $app->getId()) ),
	'title'	=> '<i class="fa fa-edit"></i> ' . M('Edit'),
	'class'	=> 'hc-parent-loader'
	);

/* DELETE */
$menu[] = '-divider-';
$menu[] = array(
	'href'	=> ntsLink::makeLink('admin/manage/appointments/update', 'delete-confirm', array('_id' => $app->getId()) ),
	'title'	=> '<i class="fa fa-times text-danger"></i> ' . M('Delete'),
	'class'	=> 'hc-confirm',
	);

/* add color to border to highlight different staff */
$more_style = '';
if( $use_color_code )
{
	if( (count($ress) > 1) )
	{
		$more_style = '';
		$rid = $app->getProp('resource_id');
		$random_color = Hc_lib::random_html_color( $rid );
		$more_style = 'border-left: ' . $random_color . ' 5px solid;';
	}
}

if( ! $condensed_view )
{
	$form = new ntsForm2;
	$my_checkbox = '';
	$my_checkbox .= $form->start(TRUE);
	$my_checkbox .= $form->input(
		'checkbox',
		array(
			'id'		=> 'app_id',
			'box_value'	=> $app->getId(),
			)
		);
	$my_checkbox .= $form->end();
}
?>

<?php if( ! $condensed_view ) : ?>

	<div class="<?php echo $class; ?>" style="<?php echo $more_style; ?>">
		<div class="pull-right">
			<?php echo $app->statusLabel( $my_checkbox ); ?>
		</div>

		<div class="dropdown">
			<a class="<?php echo $link_class; ?>" href="#" data-toggle="dropdown">
				<?php echo $show_title; ?>
			</a>
			<?php echo Hc_html::dropdown_menu($menu); ?>
		</div>
		<?php echo $app->paymentStatus(); ?>
	</div>

<?php else : ?>

	<a class="<?php echo $class; ?> <?php echo $link_class; ?>" href="#" data-toggle="dropdown" style="<?php echo $more_style; ?>">
		<?php echo $show_title; ?>
	</a>
	<?php echo Hc_html::dropdown_menu($menu); ?>

<?php endif; ?>