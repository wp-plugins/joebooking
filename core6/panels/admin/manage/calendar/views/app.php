<?php
$menu = array();
$more = array(); 

$t = $NTS_VIEW['t'];

$collapse_in = $checkbox ? ' in' : '';
$condensed = $checkbox ? '' : ' panel-condensed';

$half_column = 'col-md-6 col-sm-12';

$app_seats = $app->getProp('seats');
$duration_break = $app->getProp('duration_break');
$duration2 = $app->getProp('duration2');

$location = ntsObjectFactory::get('location');
$location->setId( $app->getProp('location_id') );

$resource = ntsObjectFactory::get('resource');
$resource->setId( $app->getProp('resource_id') );

$service = ntsObjectFactory::get('service');
$service->setId( $app->getProp('service_id') );

$customer = new ntsUser();
$customer->setId( $app->getProp('customer_id') );

$status_class = $app->statusClass();

$title = array();

$appEdit = ntsLib::getVar( 'admin/manage:appEdit' );
$rid = $app->getProp( 'resource_id' );
$can_edit = in_array($rid, $appEdit) ? 1 : 0;

$edit_link = ntsLink::makeLink('admin/manage/appointments/edit/overview', '', array('_id' => $app->getId()) );

/* prefill titles */
$title['location'] = ntsView::objectTitle( $location, TRUE );
$title['resource'] = ntsView::objectTitle( $resource, TRUE );
$title['service'] = ntsView::objectTitle( $service, TRUE );

$seats = $app->getProp('seats');
if( $seats > 1 ){
	$title['seats'] = HC_Html_Factory::element('span')
		->add_attr( 'title', M('Seats') . ': ' . $seats )
		->add_child( HC_Html::icon('users') . ' ' . $seats )
		->render()
		;
}

if( $seats > 1 ){
	$customer_title = HC_Html::icon('users') . ntsView::objectTitle( $customer, FALSE );
}
else {
	$customer_title = HC_Html::icon('user') . ntsView::objectTitle( $customer, FALSE );
}
$title['customer'] = $customer_title;

$title['customer_link'] = array(
	'title'	=> ntsView::objectTitle( $customer, TRUE ),
	'href'	=> ntsLink::makeLink(
		'admin/customers/edit/edit',
		'',
		array(
			'_id'	=> $customer->getId()
			)
		),
	'target'	=> '_blank',
	'class'		=> 'hc-parent-loader'
	);

$app_starts_at = $app->getProp('starts_at');
$duration =  $app->getProp('duration');

$t->setTimestamp( $app_starts_at );

$day_start = $t->getStartDay();
$day_end = $t->getEndDay();

$t->setTimestamp( $app->getProp('starts_at') );
$title['date'] = $t->formatDateFull(0, TRUE);

$t->setTimestamp( $app_starts_at );
$app_start_date = $t->formatDate_Db();
$app_start_view = $t->formatDateFull() . ' ' . $t->formatTime();

//$t->setTimestamp( $app_starts_at + $duration );
$t->setTimestamp( $app_starts_at );
$t->modify( '+ ' . $duration . ' seconds' );
$app_end_date = $t->formatDate_Db();
$app_end_view = $t->formatDateFull() . ' ' . $t->formatTime();

$add_more = array();

$time_label = '';
$t->setTimestamp( $app_starts_at );
$time_label = $t->formatTime( $duration, FALSE, TRUE );
if( 
	($app_start_date == $app_end_date) && 
	($app_start_date == $date)
	)
{
	$t->setTimestamp( $app_starts_at );
	$time_label = $t->formatTime( $duration );
}
elseif( // starts before, ends after
	($app_start_date < $date) && 
	($app_end_date > $date)
	)
{
	$time_label = M('All Day');
	$add_more[] = '<i class="fa fa-angle-double-left"></i>' . $app_start_view;
	$add_more[] = '<i class="fa fa-angle-double-right"></i>' . $app_end_view;
}
elseif( // starts before, ends today
	($app_start_date < $date) && 
	($app_end_date == $date)
	)
{
//	$t->setTimestamp( $app_starts_at + $duration );
	$t->setTimestamp( $app_starts_at );
	$t->modify( '+ ' . $duration . ' seconds' );
//	$time_label = ' &laquo; ' . $t->formatTime();
	$time_label = ' &raquo; ' . $t->formatTime();
//	$add_more[] = '<i class="fa fa-angle-double-left"></i>' . $app_start_view;
	$add_more[] = $app_start_view . '<i class="fa fa-angle-double-right"></i>';
}
elseif( // starts today, ends after
	($app_start_date == $date) && 
	($app_end_date > $date)
	)
{
	$t->setTimestamp( $app_starts_at );
	$time_label = $t->formatTime() . ' &raquo; ';
	$add_more[] = '<i class="fa fa-angle-double-right"></i>' . $app_end_view;
}
$title['time'] = $time_label;

$time2_label = '';
if( $duration2 ){
	$t->setTimestamp( $app_starts_at + $duration + $duration_break );
	$time2_label = $t->formatTime( $duration2, FALSE, TRUE );
}

$final_title = array();

if( (count($labels['main']) > 0) OR (is_array($labels['main'][0]) ) )
{
	$final_title[] = '<ul class="list-unstyled squeeze-in">';

	$label_count = 0;
	foreach( $labels['main'] as $label )
	{
		if( ! $label )
			continue;

		$more_class = (! $label_count) ? ' class=""' : '';
		if( is_array($label) ){
			$final_title[] = '<li' . $more_class . '>';
			$final_title[]		= '<div class="row">';
			foreach( $label as $l ){
				list( $this_title_title, $this_title_icon ) = Hc_lib::parse_icon( $title[$l] );
				if( in_array($l, array('time')) && (! $collapse_in)){
					$this_title_title_title = $this_title_title;
					if( $time2_label ){
						list( $this_title_title2, $this_title_icon2 ) = Hc_lib::parse_icon( $time2_label );
						$this_title_title .= '<br>' . $this_title_title2;
						$this_title_title_title .= ' + ' . $this_title_title2;
					}
					$final_title[]	= '<div class="' . $half_column . ' squeeze-in" title="' . $this_title_title_title . '" >';
					$final_title[]		= $this_title_title;
				}
				else {
					$final_title[]	= '<div class="' . $half_column . '" title="' . $this_title_title . '">';
					$final_title[]		= $this_title_icon . $this_title_title;
				}
				$final_title[]		= '</div>';
			}

			$final_title[]		= '</div>';
			$final_title[] = '</li>';
		}
		else {
			list( $this_title, $this_icon ) = Hc_lib::parse_icon( $title[$label] );

			$this_title_view = HC_Html_Factory::widget('list')
				->add_attr('class', 'list-inline')
				// ->add_attr('class', 'list-separated')
				;

			if( $collapse_in ){
				// $final_title[] = $app->statusLabel('&nbsp;') . ' ';
				$this_title_view->add_item( $app->statusLabel('&nbsp;') . '&nbsp;' );
			}

			$this_title_title = $this_title;
			if( ($label == 'time') && $time2_label ){
				list( $this_title2, $this_icon2 ) = Hc_lib::parse_icon( $time2_label );
				$this_title .= '<br>' . $this_title2;
				$this_title_title .= ' + ' . $this_title2;
			}

			// $final_title[] = $this_title;
			$this_title_view->add_item( $this_title );

			$final_title[] = '<li title="' . $this_title_title . '"' . $more_class . '>';
			// if( $collapse_in )
				// $final_title[] = ' <span class="caret"></span>';

			$final_title[] = $this_title_view->render();
			$final_title[] = '</li>';
		}
		$label_count++;
	}
	$final_title[] = '</ul>';
}
else
{
	foreach( $labels['main'] as $label )
	{
		list( $this_title, $this_icon ) = Hc_lib::parse_icon( $title[$label] );
		$final_title[] = '<span title="' . $this_title . '" class="squeeze-in alert-' . $status_class . '">';

		if( $collapse_in ){
//			$this_icon = $app->statusLabel('&nbsp;', 'i');
//			$this_icon = $app->statusLabel('&nbsp;');
			$this_icon = '';
			$final_title[] = $this_icon . $this_title;
		}
		else {
			$final_title[] = $this_title;
		}
		if( $collapse_in )
			$final_title[] = ' <span class="caret"></span>';
		$final_title[] = '</span>';
	}
}

$show_title = join( '', $final_title );
?>
<?php
/* MENU & MORE INFO */
$parent_class = '';
$conflicts = array();
$conflicts = $app->get_conflicts( TRUE );
if( $conflicts ){
	$parent_class = ' panel-danger-o';
	foreach( $conflicts as $c )
	{
		$more[] = '<i class="fa fa-exclamation-circle text-danger"></i> ' . $c;
	}
	$more[] = '-divider-';
}

if( $add_more ){
	$more = array_merge( $more, $add_more );
}

$lead_out_view = '';
$lead_out = $app->getProp('lead_out');
if( $lead_out )
{
	$duration = $app->getProp('duration');
	$t->setTimestamp( $app->getProp('starts_at') );
	$t->modify( '+ ' . ($duration + $lead_out) . ' seconds' );
	$lead_out_view = '<i class="fa fa-angle-right"></i>' . $t->formatTime() . ' [' . M('Clean Up') . ']';
	if( ! in_array('time', $labels['dropdown']) ){
		$more[] = $lead_out_view;
	}
}

/* MORE INFO */
if( isset($labels['dropdown']) && $labels['dropdown'] )
{
	foreach( $labels['dropdown'] as $label ){
		if( ($label == 'seats') && ($app_seats <= 1) ){
			continue;
		}
		if( isset($title[$label]) ){
			$more[] = $title[$label];

			if( ($label == 'time') && $time2_label ){
				$more[] = $time2_label;
			}
			if( ($label == 'time') && $lead_out_view ){
				$more[] = $lead_out_view;
			}
		}
	}
}
require( dirname(__FILE__) . '/_app_menu_actions.php' );
?>
<?php
if( $checkbox )
{
	$appEdit = ntsLib::getVar( 'admin/manage:appEdit' );
	$rid = $app->getProp( 'resource_id' );
	if( ! in_array($rid, $appEdit) )
		$checkbox = FALSE;
}

if( $checkbox )
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
$cost = $app->getCost();
?>

<div class="hc-ajax-parent collapse-panel panel<?php echo $condensed; ?> panel-<?php echo $status_class; ?><?php echo $parent_class; ?>">

<?php if( $collapse_in && $menu ) : ?>
	<div class="panel-heading">
<?php else : ?>
	<div class="panel-heading squeeze-in">
<?php endif; ?>

	<?php if( $cost ) : ?>
		<div class="pull-right">
			<?php echo $app->paymentStatus(TRUE); ?>
		</div>
	<?php endif; ?>

	<?php if( 0 ) : ?>
		<span class="dropdown">
			<a href="#" data-toggle="dropdown" class="dropdown-toggle">
				<?php echo $show_title; ?>
			</a>
			<?php echo Hc_html::dropdown_menu($menu); ?>
		</span>
	<?php elseif( $collapse_in && $menu ) : ?>
		<?php echo $show_title; ?>
	<?php else : ?>
		<a href="#" data-toggle="collapse-next" class="alert-<?php echo $status_class; ?>">
			<?php echo $show_title; ?>
		</a>
	<?php endif; ?>
</div>

<div class="hc-ajax-container panel-collapse collapse<?php echo $collapse_in; ?>">
	<?php if( $more ) : ?>
		<div class="panel-body squeeze-in">
			<?php if( $checkbox ) : ?>
				<div class="pull-right">
					<?php echo $my_checkbox; ?>
				</div>
			<?php endif; ?>
			<?php echo Hc_html::dropdown_menu($more, 'list-unstyled list-separated'); ?>
		</div>
	<?php endif; ?>

	<div class="panel-footer">
		<ul class="list-unstyled">
			<li>
				<i class="fa fa-info fa-fw"></i> 
				<span class="text-muted text-smaller">id:<?php echo $app->getId(); ?></span>
			</li>
			<?php if( ! $condensed OR 1 ) : ?>
				<?php
				$t->setTimestamp( $app->getProp('created_at') );
				$created_view = M('Created') . ': ' . $t->formatFull();
				?>
				<li class="squeeze-in" title="<?php echo $created_view; ?>">
					<i class="fa fa-fw"></i> 
					<span class="text-muted text-smaller">
					<?php echo $created_view; ?>
					</span>
				</li>
			<?php endif; ?>
		</ul>

		<?php
		$notes = $app->getProp('_note');
		?>
		<?php if( $notes ) : ?>
			<ul class="list-unstyled">
			<?php foreach( $notes as $n ) : ?>
				<?php
				list( $note_time, $note_user_id ) = explode( ':', $n[1] );
				$note_user = new ntsUser;
				$note_user->setId( $note_user_id );
				$t->setTimestamp( $note_time );
				$note_user_view = ntsView::objectTitle( $note_user, TRUE );
				?>
				<li>
					<i class="fa fa-comment-o fa-fw"></i> <?php echo $note_user_view; ?>
				</li>
				<li class="text-muted text-smaller">
					<?php echo $n[0]; ?>
				</li>
			<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>

	<?php //if( $menu && (! $collapse_in) ) : ?>
	<?php if( $menu ) : ?>
		<div class="panel-footer">
			<?php
			$this_menu = HC_Html_Factory::widget('list')
				->add_attr('class', 'list-inline')
				->add_attr('class', 'list-separated')
				;
			foreach( $menu as $mi ){
				$menu_link = HC_Html_Factory::widget('titled', 'a')
					->add_attr('href', $mi['href'])
					->add_attr('class', array('btn', 'btn-default'))
					->add_attr('class', array('btn-sm'))
					// ->add_child($mi['title'])
					;

				list( $link_title, $link_icon ) = Hc_lib::parse_icon( $mi['title'] );
				$menu_link
					->add_child( $link_icon )
					->add_attr('title', $link_title)
					;

				if( isset($mi['class']) ){
					// if( $mi['class'] != 'hc-ajax-loader'){
						$menu_link->add_attr('class', $mi['class']);
					// }
				}
				$this_menu->add_item( $menu_link );
			}
			echo $this_menu->render();
			?>
		</div>
	<?php endif; ?>
</div>

</div>