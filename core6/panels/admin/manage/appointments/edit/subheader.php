<?php
$object = ntsLib::getVar( 'admin/manage/appointments/edit::OBJECT' );
?>
<?php if( is_array($object) ) : ?>
	<ol class="list-separated">
	<?php foreach( $object as $obj ) : ?>
		<li>
			<ul class="list-inline">
				<li>
					<?php echo $obj->statusLabel(FALSE); ?>
				</li>
				<li>
					<?php echo ntsView::objectTitle($obj); ?>
				</li>
			</ul>
		
		</li>
	<?php endforeach; ?>
	</ol>
	<?php return; ?>
<?php endif; ?>

<?php
$t = $NTS_VIEW['t'];
$t->setTimestamp( $object->getProp('starts_at') );
$dateView = $t->formatWeekdayShort() . ', ' . $t->formatDate();

$duration = $object->getProp('duration');
$timeView = $t->formatTime( $duration );

if( $object->getProp('duration2') ){
	$t->setTimestamp( $startsAt + $object->getProp('duration') + $object->getProp('duration_break')); 
	$moreTimeView = $t->formatTime( $object->getProp('duration2') );
	$timeView .= ' + ' . $moreTimeView;

	$t->setTimestamp( $object->getProp('starts_at') ); 
	}

$lead_out = $object->getProp('lead_out');
if( $lead_out ){
	$timeView .= '<span title="' . M('Clean Up') . '">';
	$timeView .= ' [' . '<i class="fa fa-angle-right"></i> ';
	$t->setTimestamp( $object->getProp('starts_at') );
	$t->modify( '+ ' . ($duration + $lead_out) . ' seconds' );
	$timeView .= $t->formatTime();
	$timeView .= ' ' . M('Clean Up') . ']';
	$timeView .= '</span>';
}



$t->setTimestamp( $object->getProp('starts_at') );

$objectView = ntsView::objectTitle( $object );

$noheader = $_NTS['REQ']->getParam( 'noheader' );
$showHeader = ( (! $noheader) );

$isAjax = ntsLib::isAjax();
if( $isAjax ){
	$showHeader = FALSE;
}
?>
<?php if( $showHeader ) : ?>
	<div class="row">
		<div class="col-md-4 col-xs-12 pull-right">
			<ul class="list-inline pull-right">
				<li>
					ID: <?php echo $object->getId(); ?>
				</li>
				<li>
					<?php echo $object->statusLabel(); ?>
				</li>
			</ul>
		</div>

		<div class="col-md-8 col-xs-12">
			<h2>
				<small style="display: block;"><?php echo $dateView; ?></small>
				<?php echo $timeView; ?>
			</h2>
		</div>
	</div>
<?php endif; ?>