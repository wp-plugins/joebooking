<?php
$app_info = ntsLib::getAppInfo();
$date = $this->getValue('date');
$slotType = $this->getValue('selectable_every') ? 'range' : 'fixed';

$allLocs = ntsObjectFactory::getAllIds( 'location' );
$allRess = ntsObjectFactory::getAllIds( 'resource' );
$allSers = ntsObjectFactory::getAllIds( 'service' );

global $NTS_TIME_WEEKDAYS;
$params = array(
	'id'	=> $this->getValue('id'),
	);
echo $this->makePostParams('-current-', '', $params);

$minStart = NTS_TIME_STARTS;
$maxEnd = NTS_TIME_ENDS;

$minDuration = 0;
$durations = array();
$serviceIds = $this->getValue('service_id');
if( (count($serviceIds) == 1) && ($serviceIds[0] == 0) )
{
	$checkServiceIds = $allSers;
}
else
{
	$checkServiceIds = $serviceIds;
}

reset( $checkServiceIds );
foreach( $checkServiceIds as $sid )
{
	$service = ntsObjectFactory::get( 'service' );
	$service->setId( $sid );
	$thisDuration = $service->getProp( 'duration' );
	if( $thisDuration >= 24*60*60 )
	{
	}
	else
	{
		$durations[] = array( $service->getId(), $thisDuration );
		if( (! $minDuration) || ($thisDuration < $minDuration) )
			$minDuration = $thisDuration;
	}
}
$thisDuration = $minDuration;

if( count($allLocs) == 1 )
{
	echo $this->makeInput (
	/* type */
		'hidden',
	/* attributes */
		array(
			'id'	=> 'location_id',
			'value'	=> $allLocs[0],
			)
		);
}
echo $this->makeInput (
/* type */
	'hidden',
/* attributes */
	array(
		'id'		=> 'resource_id',
		)
	);

if( isset($app_info['disabled_features']['flex_service']) )
{
	echo $this->makeInput (
	/* type */
		'hidden',
	/* attributes */
		array(
			'id'	=> 'service_id',
			'value'	=> 0,
			)
		);
}
elseif( count($allSers) == 1 )
{
	echo $this->makeInput (
	/* type */
		'hidden',
	/* attributes */
		array(
			'id'	=> 'service_id',
			'value'	=> $allSers[0],
			)
		);
}
?>

<?php
$min = $minStart;
$max = $maxEnd;

$max_end = $max;
if( $max_end == 24 * 60 * 60 )
{
	// add time after midnight
	$max_end = $max_end + 12 * 60 * 60;
}
?>

<?php if( $slotType == 'range' ) : ?>
	<?php
	$interval_options = array( 3, 5, 6, 9, 10, 12, 15, 18, 20, 21, 24, 25, 27, 30, 40, 45, 50, 60, 75, 90, 2*60, 2.5*60, 3*60, 4*60, 5*60, 6*60, 8*60, 9*60, 12*60, 18*60, 24*60 );
	$selectabe_interval_options = array();
	foreach( $interval_options as $o )
	{
		if( $o % NTS_TIME_UNIT )
			continue;
		if( $o > $maxEnd )
			continue;
		$selectabe_interval_options[] = array( 60 * $o, $o );
	}
	?>
	<?php
	echo ntsForm::wrapInput(
		M('Time'),
		array(
			$this->buildInput(
			/* type */
				'date/Time',
			/* attributes */
				array(
					'id'		=> 'starts_at',
					'conf'	=> array(
						'min'	=> $min,
						'max'	=> $max,
						),
					'default'	=> $minStart
					),
			/* validators */
				array(
					array(
						'code'		=> 'notEmpty.php', 
						'error'		=> M('Required'),
						),
					)
				),
			' - ',
			$this->buildInput(
			/* type */
				'date/Time',
			/* attributes */
				array(
					'id'		=> 'ends_at',
					'conf'	=> array(
						'min'	=> $min,
						'max'	=> $max_end,
						),
					'default'	=> $maxEnd
					),
			/* validators */
				array(
					array(
						'code'		=> 'notEmpty.php', 
						'error'		=> M('Required'),
						),
					array(
						'code'		=> 'greaterThan.php', 
						'error'		=> "Slot can't start before end",
						'params'	=> array(
							'compareWithField' => 'starts_at',
							),
						)
					)
				),
			' ' . M('Interval') . ': ',
			$this->buildInput(
			/* type */
				'select',
			/* attributes */
				array(
					'id'		=> 'selectable_every',
					'options'	=> $selectabe_interval_options,
					)
				),
			' ' . M('Minutes')
			)
		);
	?>
<?php else : ?>
	<?php
	echo ntsForm::wrapInput(
		M('Time'),
		$this->buildInput(
		/* type */
			'date/Time',
		/* attributes */
			array(
				'id'		=> 'starts_at',
				'conf'	=> array(
					'min'	=> $min,
					'max'	=> $max,
					),
				),
		/* validators */
			array(
				array(
					'code'		=> 'notEmpty.php', 
					'error'		=> M('Required'),
					),
				)
			)
		); 
	?>
<?php endif; ?>

<?php if( count($allLocs) > 1 ) : ?>
	<?php
	echo ntsForm::wrapInput(
		M('Locations'),
		$this->buildInput(
		/* type */
			'locations',
		/* attributes */
			array(
				'id'	=> 'location_id',
				),
		/* validators */
			array(
				array(
					'code'		=> 'notEmpty.php', 
					'error'		=> M('Required'),
					),
				)
			)
		);
	?>
<?php endif; ?>

<?php if( count($allSers) > 1 ) : ?>
	<?php if( ! isset($app_info['disabled_features']['flex_service']) ) : ?>
		<?php
		echo ntsForm::wrapInput(
			M('Services'),
			$this->buildInput(
			/* type */
				'services',
			/* attributes */
				array(
					'id'	=> 'service_id',
					),
			/* validators */
				array(
					array(
						'code'		=> 'notEmpty.php', 
						'error'		=> M('Required'),
						),
					)
				)
			);
		?>
	<?php endif; ?>
<?php endif; ?>

<?php
echo ntsForm::wrapInput(
	M('Weekdays'),
	$this->buildInput(
	/* type */
		'date/Weekday',
	/* attributes */
		array(
			'id'	=> 'applied_on',
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> M('Required'),
				),
			)
		)
	);
?>

<?php
$select_options = array(
	array( 0, M('All') ),
	array( 1, M('Odd') ),
	array( 2, M('Even') ),
	);
echo ntsForm::wrapInput(
	M('Weeks'),
	$this->buildInput(
	/* type */
		'select',
	/* attributes */
		array(
			'id'		=> 'week_applied_on',
			'options'	=> $select_options,
			)
		)
	);
?>

<?php
echo ntsForm::wrapInput(
	M('Dates'),
	array(
		$this->buildInput(
		/* type */
			'date/Calendar',
		/* attributes */
			array(
				'id'	=> 'valid_from',
				),
		/* validators */
			array(
				array(
					'code'	=> 'notEmpty.php', 
					'error'	=> 'Please enter the from date',
					),
				)
			),
		' - ',
		$this->buildInput(
		/* type */
			'date/Calendar',
		/* attributes */
			array(
				'id'		=> 'valid_to',
				),
		/* validators */
			array(
				array(
					'code'		=> 'notEmpty.php', 
					'error'		=> 'Please enter the from date',
					),
				array(
					'code'		=> 'greaterEqualThan.php', 
					'error'		=> "This date can't be before the from date",
					'params'	=> array(
						'compareWithField' => 'valid_from',
						),
					),
				)
			)
		)
	);
?>

<?php
echo ntsForm::wrapInput(
	M('Min Advance Booking'),
	$this->buildInput(
	/* type */
		'period/MinHourDayWeek',
	/* attributes */
		array(
			'id'		=> 'min_from_now',
			'default'	=> 3 * 60 * 60,
			'attr'		=> array(
				),
			),
	/* validators */
		array(
			)
		)
	);
?>

<?php
echo ntsForm::wrapInput(
	M('Max Advance Booking'),
	$this->buildInput(
	/* type */
		'period/MinHourDayWeek',
	/* attributes */
		array(
			'id'		=> 'max_from_now',
			'default'	=> 8 * 7 * 24 * 60 * 60,
			'attr'		=> array(
				),
			),
	/* validators */
		array(
			array(
				'code'		=> 'greaterEqualThan.php', 
				'error'		=> M('This should not be smaller than the min advance booking'),
				'params'	=> array(
					'compareWithField'	=> 'min_from_now',
					),
				),
			)
		)
	);
?>

<?php if( ! isset($app_info['disabled_features']['capacity']) ) : ?>
	<?php
	echo ntsForm::wrapInput(
		M('Capacity'),
		array(
			$this->buildInput(
			/* type */
				'text',
			/* attributes */
				array(
					'id'		=> 'capacity',
					'attr'		=> array(
						'size'	=> 3,
						),
					'default'	=> 1,
					),
			/* validators */
				array(
					array(
						'code'		=> 'notEmpty.php', 
						'error'		=> M('Required'),
						),
					array(
						'code'		=> 'integer.php', 
						'error'		=> M('Numbers only'),
						),
					)
				),
				' ' . M('Seats')
			)
		); 
	?>
	<?php
	echo ntsForm::wrapInput(
		M('Max Capacity Per Appointment'),
		array(
			$this->buildInput(
			/* type */
				'text',
			/* attributes */
				array(
					'id'		=> 'max_capacity',
					'attr'		=> array(
						'size'	=> 3,
						),
					'default'	=> 1,
					),
			/* validators */
				array(
					array(
						'code'		=> 'notEmpty.php', 
						'error'		=> M('Required'),
						),
					array(
						'code'		=> 'integer.php', 
						'error'		=> M('Numbers only'),
						),
					array(
						'code'		=> 'lessEqualThan.php', 
						'error'		=> M('This should not be bigger than') . ': ' . M('Capacity'),
						'params'	=> array(
							'compareWithField' => 'capacity',
							),
						),
					)
				),
				' ' . M('Seats')
			)
		); 
	?>
<?php else : ?>
	<?php
	echo $this->makeInput (
	/* type */
		'hidden',
	/* attributes */
		array(
			'id'	=> 'capacity',
			'value'	=> 1,
			)
		);
	?>
<?php endif; ?>

<?php echo $this->makePostParams('-current-', 'update'); ?>
<?php
$deleteLink = ntsLink::makeLink(
	'-current-/delete'
	);
echo ntsForm::wrapInput(
	'',
	array(
		'<ul class="list-inline list-separated">',
			'<li>',
				'<INPUT class="btn btn-default btn-success" TYPE="submit" VALUE="' . M('Update') . '">',
			'</li>',
			'<li>',
				'<a href="' . $deleteLink . '" class="btn btn-danger btn-sm" title="' . M('Delete') . '">' . M('Delete') . '</a>',
			'</li>',
		'</ul>'
		)
	);
?>