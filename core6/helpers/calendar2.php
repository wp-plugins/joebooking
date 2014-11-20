<?php
/*  should have these vars defined 

$showWeekNo
$linkedDates
$okDates
$cssDates
$linkedDates
$params // optional

$calendarMode = recurring | custom-dates | default
if default
	$selectedDate
if recurring
	$recurringDates
	$recurFrom
	$recurTo
if custom-dates
	$customDates
*/

if( ! isset($showWeekNo) )
	$showWeekNo = FALSE;
if( ! isset($calendarMode) )
	$calendarMode = 'default';
if( ! isset($params) )
	$params = array();

$t = $NTS_VIEW['t'];
include_once( NTS_LIB_DIR . '/lib/datetime/ntsCalendar.php' );

$ntsConf =& ntsConf::getInstance();
$weekStartsOn = $ntsConf->get('weekStartsOn');
$text_Monthnames = array( M('Jan'), M('Feb'), M('Mar'), M('Apr'), M('May'), M('Jun'), M('Jul'), M('Aug'), M('Sep'), M('Oct'), M('Nov'), M('Dec') );
$text_Weekdays = array( M('Sun'), M('Mon'), M('Tue'), M('Wed'), M('Thu'), M('Fri'), M('Sat') );
$calendar = new ntsCalendar();

$highlightDays = array();
switch( $calendarMode )
{
	case 'recurring':
		$highlightDays = $recurringDates ? $recurringDates : array( $recurFrom, $recurTo );
		break;
	case 'custom-dates':
		$highlightDays = $customDates;
		break;
	default:
		if( isset($highlightDay) )
			$highlightDays = array( $highlightDay );
		else
			$highlightDays = array( $selectedDate );
}

if( ! (isset($calYear) && isset($calMonth) && isset($calDay)) )
{
	list( $calYear, $calMonth, $calDay ) = ntsTime::splitDate( $selectedDate );
}

if( ! isset($k) )
	$k = 0;

$showMonths = isset($calendarMonths) ? $calendarMonths : 1;
$t->setDateTime( $calYear, $calMonth - $showMonths, 1, 0, 0, 0 );
$previousMo = $t->formatDate_Db();

$t->setDateTime( $calYear, $calMonth + 1, 1, 0, 0, 0 );
$nextMo = $t->formatDate_Db();
?>

<div class="hc_cal hc_cal_fluid">

<?php
$monthMatrix = $calendar->getMonthMatrix( $calYear, $calMonth );
$currentCalendar = array();
$myParams = $params;
$myParams['cal'] = $previousMo;
$prevMoLink = isset($linkDates[$previousMo]) ? $linkDates[$previousMo] : ntsLink::makeLink('-current-', '', $myParams);
?>
	<div class="row">
		<?php if( ! (isset($skipPrevLink) && $skipPrevLink) ) : ?>
			<a class="pull-left nts-cal-link" href="<?php echo $prevMoLink; ?>">
				<span class="btn btn-default">&laquo;</span>
			</a>
		<?php endif; ?>

		<?php
		$myParams = $params;
		$myParams['cal'] = $nextMo;
		$nextMoLink = isset($linkDates[$nextMo]) ? $linkDates[$nextMo] : ntsLink::makeLink('-current-', '', $myParams);
		?>
		<?php if( ! (isset($skipNextLink) && $skipNextLink) ) : ?>
			<a class="pull-right nts-cal-link" href="<?php echo $nextMoLink; ?>">
				<span class="btn btn-default">&raquo;</span>
			</a>
		<?php endif; ?>

		<span class="btn display-block text-center">
			<strong>
			<?php echo $text_Monthnames[ $calMonth - 1 ]; ?> <?php echo $calYear; ?>
			</strong>
		</span>
	</div>

	<div class="row">

		<?php if( $showWeekNo ) : ?>
			<div class="col-sm-1">
				<div class="text-smaller text-muted" style="white-space: nowrap; overflow: hidden;" title="<?php echo M('Week'); ?>">
					<span class="btn btn-sm btn-tight">
						<?php echo M('Week'); ?>
					</span>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="hc_cal hc_cal_fluid">
				<div class="row">
		<?php endif; ?>

		<?php for( $i = 0; $i <= 6; $i++ ) : ?>
			<?php
			$dayIndex = $weekStartsOn + $i;
			$dayIndex = $dayIndex % 7;
			?>
			<div class="col-sm-1">
				<div style="white-space: nowrap; text-align: center;" title="<?php echo $text_Weekdays[$dayIndex]; ?>"><small><?php echo $text_Weekdays[$dayIndex]; ?></small></div>
			</div>
		<?php endfor; ?>

		<?php if( $showWeekNo ) : ?>
				</div>
				</div>
			</div>
		<?php endif; ?>
	</div>

	<?php foreach( $monthMatrix as $week => $days ) : ?>
	<div class="row">
		<?php if( $showWeekNo ) : ?>
			<div class="col-sm-1">
				<div class="text-smaller text-muted" style="white-space: nowrap; overflow: hidden;" title="<?php echo M('Week'); ?>">
					<span class="btn btn-sm btn-tight">
						<?php echo $week; ?>
					</span>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="hc_cal hc_cal_fluid" style="margin: 0 0; padding: 0 0;">
				<div class="row">
		<?php endif; ?>

		<?php foreach( $days as $day ) : ?>
		<?php if( $day ) : ?>
			<?php
			$thisDate = ntsTime::formatDateParam( $calYear, $calMonth, $day );

 			$linked = in_array($thisDate, $linkedDates) ? true : false;
 			$ok = in_array($thisDate, $okDates) ? true : false;

			$class = isset($cssDates[$thisDate]) ?  $cssDates[$thisDate] : array();
//			$class[] = 'alert alert-tight';
			$class[] = 'btn btn-sm btn-tight display-block';
			$label = isset($labelDates[$thisDate]) ? $labelDates[$thisDate] : '';
			$class = join( ' ', $class );
			$myParams = $params;
			$myParams['cal'] = $thisDate;
			$day_class = array('col-sm-1');
			if( in_array($thisDate, $highlightDays) )
				$day_class[] = 'today';
			$day_class = join( ' ', $day_class );
			?>
			<div class="<?php echo $day_class; ?>">
				<?php if( $linked ) : ?>
					<?php
					if( isset($linkDates[$thisDate]) )
					{
						$targetLink = $linkDates[$thisDate];
					}
					else
					{
						$targetPanel = isset($calendarReturnTo) ? $calendarReturnTo : '-current-';
						$targetLink = ntsLink::makeLink($targetPanel, '', $myParams);
					}
					?>
					<a class="nts-cal-link" title="<?php echo $label; ?>" href="<?php echo $targetLink; ?>">
						<span class="<?php echo $class; ?>" >
							<?php echo $day; ?>
						</span>
					</a>
				<?php else : ?>
					<span class="<?php echo $class; ?>" title="<?php echo $label; ?>">
						<?php echo $day; ?>
					</span>
				<?php endif; ?>
			</div>
		<?php else : ?>
			<div class="col-sm-1">
			&nbsp;
			</div>
		<?php endif; ?>
		<?php endforeach; ?>

		<?php if( $showWeekNo ) : ?>
				</div>
				</div>
			</div>
		<?php endif; ?>

	</div>
	<?php endforeach; ?>

</div>
