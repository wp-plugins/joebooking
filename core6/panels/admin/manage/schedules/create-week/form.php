<?php
global $NTS_TIME_WEEKDAYS;
$app_info = ntsLib::getAppInfo();

$t = $NTS_VIEW['t'];

$locs = ntsLib::getVar( 'admin::locs' );
$ress = ntsLib::getVar( 'admin::ress' );
$sers = ntsLib::getVar( 'admin::sers' );

$ress_archive = ntsLib::getVar( 'admin::ress_archive' );
if( $ress_archive )
{
	$ress = array_diff( $ress, $ress_archive );
	$ress = array_values( $ress );
}
$locs_archive = ntsLib::getVar( 'admin::locs_archive' );
if( $locs_archive )
{
	$locs = array_diff( $locs, $locs_archive );
	$locs = array_values( $locs );
}

$allLocs = ntsObjectFactory::getAllIds( 'location' );
$allRess = ntsObjectFactory::getAllIds( 'resource' );
$allSers = ntsObjectFactory::getAllIds( 'service' );

$cal = $this->getValue('cal');

$minStart = NTS_TIME_STARTS;
$maxEnd = NTS_TIME_ENDS;

$action = $this->getValue('action');
$when = $this->getValue('showWhen');
$currentWhen = $this->getValue('when');

for( $di = 0; $di <= 6; $di++ )
{
	$slotType[$di] = $this->getValue('slot_type_' . $di);
}

$params = array(
	'id'	=> $this->getValue('id'),
	);
echo $this->makePostParams('-current-', '', $params);

$minDuration = -1;
reset( $sers );
foreach( $sers as $objId )
{
	$obj = ntsObjectFactory::get( 'service' );
	$obj->setId( $objId );
	$options[] = array( $objId, ntsView::objectTitle($obj) );
	$thisDuration = $obj->getProp( 'duration' );
	if( ($minDuration == -1) || ($thisDuration < $minDuration) )
	{
		$minDuration = $thisDuration;
	}
	$durations[] = array( $obj->getId(), $thisDuration );
}
?>

<?php
if( count($locs) == 1 )
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
if( count($ress) == 1 )
{
	echo $this->makeInput (
	/* type */
		'hidden',
	/* attributes */
		array(
			'id'	=> 'resource_id',
			'value'	=> $ress[0],
			)
		);
}

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

<?php if( count($allRess) > 1 ) : ?>
	<?php if( count($ress) == 1 ) : ?>
		<?php
		$obj = ntsObjectFactory::get( 'resource' );
		$obj->setId( $ress[0] );
		?>
		<?php
		echo ntsForm::wrapInput(
			M('Bookable Resource'),
			ntsView::objectTitle( $obj, TRUE )
			);
		?>
	<?php else : ?>
		<?php
		$options = array();
		reset( $ress );
		foreach( $ress as $objId )
		{
			$obj = ntsObjectFactory::get( 'resource' );
			$obj->setId( $objId );
			$options[] = array( $objId, ntsView::objectTitle($obj) );
		}
		?>
		<?php
		echo ntsForm::wrapInput(
			M('Bookable Resource'),
			$this->buildInput(
			/* type */
				'select',
			/* attributes */
				array(
					'id'		=> 'resource_id',
					'options'	=> $options,
					)
				)
			);
		?>
	<?php endif; ?>
<?php endif; ?>

<?php if( count($allLocs) > 1 ) : ?>
	<?php if( count($locs) == 1 ) : ?>
		<?php
		$obj = ntsObjectFactory::get( 'location' );
		$obj->setId( $locs[0] );
		?>
		<?php
		echo ntsForm::wrapInput(
			M('Location'),
			ntsView::objectTitle( $obj, TRUE )
			);
		?>
	<?php else : ?>
		<?php
		$options = array();
		reset( $locs );
		foreach( $locs as $objId )
		{
			$obj = ntsObjectFactory::get( 'location' );
			$obj->setId( $objId );
			$options[] = array( $objId, ntsView::objectTitle($obj) );
		}
		?>
		<?php
		echo ntsForm::wrapInput(
			M('Location'),
			$this->buildInput(
			/* type */
				'select',
			/* attributes */
				array(
					'id'		=> 'location_id',
					'options'	=> $options,
					)
				)
			);
		?>
	<?php endif; ?>
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
						'error'		=> M('Required Field'),
						),
					)
				)
			);
		?>
	<?php endif; ?>
<?php endif; ?>

<?php
$min = $minStart;
$max = $maxEnd - $minDuration;
if( $max < $min )
	$max = $maxEnd;
$max = $maxEnd;
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
						'error'		=> M('Required field'),
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

<?php if( in_array('range', $when) ) : ?>
	<div id="<?php echo $this->formId; ?>_when_range"<?php if($currentWhen != 'range'){echo ' style="display: none;"';}; ?>>
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
						'id'	=> 'valid_to',
						),
				/* validators */
					array(
						array(
							'code'		=> 'notEmpty.php', 
							'error'		=> 'Please enter the end date',
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
	</div>
<?php endif; ?>

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

<?php
/* list weekdays */
$t = new ntsTime;
$t->setNow();
$t->setStartWeek();
?>

<div class="row">

<?php for( $ii = 1; $ii <= 7; $ii++ ) : ?>
	<?php
	$di = $t->getWeekday();
	?>
	<?php if( ($ii % 2) ) : ?>
		<div class="clearfix"></div>
	<?php endif; ?>

	<div class="col-md-6">
		<div class="thumbnail" style="padding: 0.5em 1em;">
			<h4>
				<?php echo $t->formatWeekDay(); ?>
			</h4>
			<hr>

			<p>
			<?php
			echo $this->makeInput(
			/* type */
				'radioSet',
			/* attributes */
				array(
					'id'		=> 'slot_type_' . $di,
//					'default'	=> $slotType[$di],
					'options'	=> array(
						array( 'none',	M('None') ),
						array( 'range',	M('Time Range') ),
						array( 'fixed',	M('Fixed Time') ),
						)
					)
				);
			?>
			</p>

			<p>
			<div id="<?php echo $this->formId; ?>_details_range_<?php echo $di; ?>">
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
				echo join( '', 
					array(
						'<p>' . 
						$this->makeInput(
						/* type */
							'date/Time',
						/* attributes */
							array(
								'id'		=> 'starts_at_range_' . $di,
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
									'error'		=> M('Required field'),
									),
								)
							),
						' - ',
						$this->makeInput(
						/* type */
							'date/Time',
						/* attributes */
							array(
								'id'		=> 'ends_at_range_' . $di,
								'conf'	=> array(
									'min'	=> $min,
									'max'	=> $max,
									),
								'default'	=> $maxEnd
								),
						/* validators */
							array(
								array(
									'code'		=> 'notEmpty.php', 
									'error'		=> M('Required field'),
									),
								array(
									'code'		=> 'greaterThan.php', 
									'error'		=> "Slot can't start before end",
									'params'	=> array(
										'compareWithField' => 'starts_at_range_' . $di,
										),
									)
								)
							),
						'</p><p>' . M('Interval') . ': ',
						$this->makeInput(
						/* type */
							'select',
						/* attributes */
							array(
								'id'		=> 'selectable_every_' . $di,
								'options'	=> $selectabe_interval_options,
								)
							),
						' ' . M('Minutes'),
						'</p>'
						)
					);
				?>
			</div>

			<div id="<?php echo $this->formId; ?>_details_fixed_<?php echo $di; ?>">
				<?php
				echo $this->makeInput(
				/* type */
					'date/Time',
				/* attributes */
					array(
						'id'		=> 'starts_at_fixed_' . $di,
						'conf'	=> array(
							'min'	=> $min,
							'max'	=> $max,
							),
						),
				/* validators */
					array(
						array(
							'code'		=> 'notEmpty.php', 
							'error'		=> M('Required field'),
							),
						)
					); 
				?>
			</div>
			</p>

			<p>
			<div id="<?php echo $this->formId; ?>_details_week_<?php echo $di; ?>">
			<?php
			$select_options = array(
				array( 0, M('All') ),
				array( 1, M('Odd') ),
				array( 2, M('Even') ),
				);
			echo M('Weeks') . ': ' . 
				$this->makeInput(
				/* type */
					'select',
				/* attributes */
					array(
						'id'		=> 'week_applied_on_' . $di,
						'options'	=> $select_options,
						)
					);
			?>
			</div>
			</p>

		</div>
	</div>
	<?php
	$t->modify( '+1 day' );
	?>
<?php endfor; ?>

</div>


<?php echo $this->makePostParams('-current-', 'create' ); ?>
<INPUT TYPE="submit" class="btn btn-default btn-success" VALUE="<?php echo M('Add'); ?>">

<?php require( dirname(__FILE__) . '/_form_js.php' ); ?>