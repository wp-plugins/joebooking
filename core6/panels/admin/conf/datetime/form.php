<?php
$t = new ntsTime;
?>
<?php
$timezoneOptions = ntsTime::getTimezones();
$t->setNow();
$timeString = $t->formatFull();
echo ntsForm::wrapInput(
	M('Company Timezone'),
	$this->buildInput (
	/* type */
		'select',
	/* attributes */
		array(
			'id'		=> 'companyTimezone',
			'options'	=> $timezoneOptions,
			'help'		=> M('Now') . ': ' . $timeString,
			)
		)
	);
?>
<?php
$dateFormats = array( 'd/m/Y', 'd-m-Y', 'n/j/Y', 'Y/m/d', 'd.m.Y', 'j M Y' );
$dateFormatsOptions = array();
reset( $dateFormats );
foreach( $dateFormats as $f )
{
	$t->dateFormat = $f;
	$dateFormatsOptions[] = array( $f, $t->formatDate() );
}
echo ntsForm::wrapInput(
	M('Date Format'),
	$this->buildInput(
	/* type */
		'select',
	/* attributes */
		array(
			'id'		=> 'dateFormat',
			'options'	=> $dateFormatsOptions,
			)
		)
	);
?>

<?php
$timeFormats = array( 'g:ia', 'H:i', 'g:i A');
$timeFormatsOptions = array();
reset( $timeFormats );
foreach( $timeFormats as $f )
{
	$timeFormatsOptions[] = array( $f, date($f) );
}
echo ntsForm::wrapInput(
	M('Time Format'),
	$this->buildInput(
	/* type */
		'select',
	/* attributes */
		array(
			'id'		=> 'timeFormat',
			'options'	=> $timeFormatsOptions,
			)
		)
	);
?>

<?php
$weekStartsOnOptions = array(
	array( 1, M('Monday') ),
	array( 0, M('Sunday') ),
	);
echo ntsForm::wrapInput(
	M('Week Starts On'),
	$this->buildInput(
	/* type */
		'select',
	/* attributes */
		array(
			'id'		=> 'weekStartsOn',
			'options'	=> $weekStartsOnOptions,
			)
		)
	);
?>

<?php
$timeunitOptions = array(
	array( 3, 3 ),
	array( 5, 5 ),
	array( 10, 10 ),
	array( 15, 15 ),
	array( 30, 30 ),
	array( 60, 60 ),
	);
echo ntsForm::wrapInput(
	M('Time Unit'),
	$this->buildInput(
	/* type */
		'select',
	/* attributes */
		array(
			'id'		=> 'timeUnit',
			'options'	=> $timeunitOptions,
			'after'		=> M('Minutes'),
			)
		)
	);
?>

<?php
echo ntsForm::wrapInput(
	M('Visible Time Of Day'),
	array(
		$this->buildInput(
		/* type */
			'date/Time',
		/* attributes */
			array(
				'id'		=> 'timeStarts',
				)
			),
		' - ',
		$this->buildInput(
		/* type */
			'date/Time',
		/* attributes */
			array(
				'id'		=> 'timeEnds',
				),
		/* validators */
			array(
				array(
					'code'		=> 'notEmpty.php', 
					'error'		=> M('Required Field'),
					),
				array(
					'code'		=> 'greaterThan.php', 
					'error'		=> "This can't be before the working time start",
					'params'	=> array(
						'compareWithField' => 'timeStarts',
						),
					)
				)
			)
		)
	);
?>

<?php
$monthsToShowOptions = array(
	array( 1, 1 ),
	array( 2, 2 ),
	array( 3, 3 ),
	);
echo ntsForm::wrapInput(
	M('Months To Show'),
	$this->buildInput (
	/* type */
		'select',
	/* attributes */
		array(
			'id'		=> 'monthsToShow',
			'options'	=> $monthsToShowOptions,
			)
		)
	);
?>

<?php
$limitOptions = array(
	array( 'minute', M('Minute') ),
	array( 'hour', M('Hour') ),
	array( 'day', M('Day') ),
	);
echo ntsForm::wrapInput(
	M('Max Measure For Duration Display'),
	$this->buildInput (
	/* type */
		'select',
	/* attributes */
		array(
			'id'		=> 'limitTimeMeasure',
			'options'	=> $limitOptions,
			'help'		=> M('For example, if set to Minute, it will show 90 Minutes rather than 1 Hour 30 Minutes.')
			)
		)
	);
?>

<?php
$minFromNowOptions = array(
	array( 'now', M('Now') ),
	array( 'tomorrow', M('Start Of Tomorrow Availability') ),
	);
echo ntsForm::wrapInput(
	M('Apply Min Advance Booking From'),
	$this->buildInput (
	/* type */
		'select',
	/* attributes */
		array(
			'id'		=> 'minFromNowTomorrow',
			'options'	=> $minFromNowOptions,
			'help'		=> M('For tomorrow availability, when applying Min Advance Booking, count from now or from the start of availability time') . '?',
			)
		)
	);
?>


<?php echo $this->makePostParams('-current-', 'update'); ?>
<?php
echo ntsForm::wrapInput(
	'',
	'<input class="btn btn-default" type="submit" value="' . M('Save') . '">'
	);
?>