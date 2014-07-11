<?php
$jsDurationOptions = array();
reset( $durations );
foreach( $durations as $du )
{
	$jsDurationOptions[] = '[' . join(',', $du) . ']';
}
?>

<script language="JavaScript">
jQuery('#<?php echo $this->formId; ?>when').live("change", function() {
<?php foreach( $when as $wh ) : ?>
	jQuery('#<?php echo $this->formId; ?>_when_<?php echo $wh; ?>').toggle();
<?php endforeach; ?>
	});
	
jQuery('#<?php echo $this->formId; ?>slot_type').live("change", function() {
	jQuery('#<?php echo $this->formId; ?>_details_range').toggle();
	jQuery('#<?php echo $this->formId; ?>_details_fixed').toggle();
	});

<?php if( $slotType == 'range' ) : ?>
jQuery('#<?php echo $this->formId; ?>_details_range').show();
jQuery('#<?php echo $this->formId; ?>_details_fixed').hide();
<?php else : ?>
jQuery('#<?php echo $this->formId; ?>_details_range').hide();
jQuery('#<?php echo $this->formId; ?>_details_fixed').show();
<?php endif; ?>

/* check the options of start */
var STARTS_FIXED<?php echo $this->formId; ?> = document.getElementById('<?php echo $this->formId; ?>starts_at_fixed');
var STARTS_AT<?php echo $this->formId; ?> = document.getElementById('<?php echo $this->formId; ?>starts_at_range');
var ENDS_AT<?php echo $this->formId; ?> = document.getElementById('<?php echo $this->formId; ?>ends_at_range');
var INTERVAL<?php echo $this->formId; ?> = document.getElementById('<?php echo $this->formId; ?>selectable_every');

var SERVICE_DURATIONS<?php echo $this->formId; ?> = [<?php echo join(',', $jsDurationOptions); ?>];

var STARTS_FIXED_OPTIONS<?php echo $this->formId; ?> = new Array( STARTS_FIXED<?php echo $this->formId; ?>.options.length );
for( ii = 0; ii < STARTS_FIXED<?php echo $this->formId; ?>.options.length; ii++ ){
	STARTS_FIXED_OPTIONS<?php echo $this->formId; ?>[ii] = new Array( STARTS_FIXED<?php echo $this->formId; ?>.options[ii].value, STARTS_FIXED<?php echo $this->formId; ?>.options[ii].text );
	}

var STARTS_AT_OPTIONS<?php echo $this->formId; ?> = new Array( STARTS_AT<?php echo $this->formId; ?>.options.length );
for( ii = 0; ii < STARTS_AT<?php echo $this->formId; ?>.options.length; ii++ ){
	STARTS_AT_OPTIONS<?php echo $this->formId; ?>[ii] = new Array( STARTS_AT<?php echo $this->formId; ?>.options[ii].value, STARTS_AT<?php echo $this->formId; ?>.options[ii].text );
	}
var ENDS_AT_OPTIONS<?php echo $this->formId; ?> = new Array( ENDS_AT<?php echo $this->formId; ?>.options.length );
for( ii = 0; ii < ENDS_AT<?php echo $this->formId; ?>.options.length; ii++ ){
	ENDS_AT_OPTIONS<?php echo $this->formId; ?>[ii] = new Array( ENDS_AT<?php echo $this->formId; ?>.options[ii].value, ENDS_AT<?php echo $this->formId; ?>.options[ii].text );
	}
var INTERVAL_OPTIONS<?php echo $this->formId; ?> = new Array( INTERVAL<?php echo $this->formId; ?>.options.length );
for( ii = 0; ii < INTERVAL<?php echo $this->formId; ?>.options.length; ii++ ){
	INTERVAL_OPTIONS<?php echo $this->formId; ?>[ii] = new Array( INTERVAL<?php echo $this->formId; ?>.options[ii].value, INTERVAL<?php echo $this->formId; ?>.options[ii].text );
	}

var currentDuration = 0;
var currentServiceId = jQuery('#<?php echo $this->formId; ?>service_id').val();
for( ii = 0; ii < SERVICE_DURATIONS<?php echo $this->formId; ?>.length; ii++ ){
	if( SERVICE_DURATIONS<?php echo $this->formId; ?>[ii][0] == currentServiceId ){
		currentDuration = SERVICE_DURATIONS<?php echo $this->formId; ?>[ii][1];
		}
	}

function setEndOption<?php echo $this->formId; ?>(){
	var currentDuration = 0;
	var currentServiceId = jQuery('#<?php echo $this->formId; ?>service_id').val();
	for( ii = 0; ii < SERVICE_DURATIONS<?php echo $this->formId; ?>.length; ii++ ){
		if( SERVICE_DURATIONS<?php echo $this->formId; ?>[ii][0] == currentServiceId ){
			currentDuration = SERVICE_DURATIONS<?php echo $this->formId; ?>[ii][1];
			}
		}

	var currentValue = ENDS_AT<?php echo $this->formId; ?>.value;
	ENDS_AT<?php echo $this->formId; ?>.options.length = 0;
	var currentStart = STARTS_AT<?php echo $this->formId; ?>.value;
	var checkWith = parseInt(currentStart) + parseInt(currentDuration);

	for( ii = 0; ii < ENDS_AT_OPTIONS<?php echo $this->formId; ?>.length; ii++ ){
		var testOption = ENDS_AT_OPTIONS<?php echo $this->formId; ?>[ii];
		if( testOption[0] >= checkWith ){
			var selectMe = (currentValue == testOption[0]) ? true : false;
			ENDS_AT<?php echo $this->formId; ?>.options.add( new Option(testOption[1], testOption[0], selectMe, selectMe) );
			}
		}
	}

function setStartOption<?php echo $this->formId; ?>(){
	var currentDuration = 0;
	var currentServiceId = jQuery('#<?php echo $this->formId; ?>service_id').val();
	for( ii = 0; ii < SERVICE_DURATIONS<?php echo $this->formId; ?>.length; ii++ ){
		if( SERVICE_DURATIONS<?php echo $this->formId; ?>[ii][0] == currentServiceId ){
			currentDuration = SERVICE_DURATIONS<?php echo $this->formId; ?>[ii][1];
			}
		}

	var currentValue = STARTS_AT<?php echo $this->formId; ?>.value;
	STARTS_AT<?php echo $this->formId; ?>.options.length = 0;
	var currentEnd = ENDS_AT<?php echo $this->formId; ?>.value;
	var checkWith = parseInt(currentEnd) - parseInt(currentDuration);

	for( ii = 0; ii < STARTS_AT_OPTIONS<?php echo $this->formId; ?>.length; ii++ ){
		var testOption = STARTS_AT_OPTIONS<?php echo $this->formId; ?>[ii];
		if( testOption[0] <= checkWith ){
			var selectMe = (currentValue == testOption[0]) ? true : false;
			STARTS_AT<?php echo $this->formId; ?>.options.add( new Option(testOption[1], testOption[0], selectMe, selectMe) );
			}
		}
	}

function setFixedStartOption<?php echo $this->formId; ?>(){
	var currentDuration = 0;
	var currentServiceId = jQuery('#<?php echo $this->formId; ?>service_id').val();
	for( ii = 0; ii < SERVICE_DURATIONS<?php echo $this->formId; ?>.length; ii++ ){
		if( SERVICE_DURATIONS<?php echo $this->formId; ?>[ii][0] == currentServiceId ){
			currentDuration = SERVICE_DURATIONS<?php echo $this->formId; ?>[ii][1];
			}
		}

	var currentValue = STARTS_FIXED<?php echo $this->formId; ?>.value;
	STARTS_FIXED<?php echo $this->formId; ?>.options.length = 0;
	var currentEnd = STARTS_FIXED_OPTIONS<?php echo $this->formId; ?>[STARTS_FIXED_OPTIONS<?php echo $this->formId; ?>.length - 1][0];
	var checkWith = parseInt(currentEnd) - parseInt(currentDuration);

	for( ii = 0; ii < STARTS_FIXED_OPTIONS<?php echo $this->formId; ?>.length; ii++ ){
		var testOption = STARTS_FIXED_OPTIONS<?php echo $this->formId; ?>[ii];
		if( testOption[0] <= checkWith ){
			var selectMe = (currentValue == testOption[0]) ? true : false;
			STARTS_FIXED<?php echo $this->formId; ?>.options.add( new Option(testOption[1], testOption[0], selectMe, selectMe) );
			}
		}
	}

function setIntervalOption<?php echo $this->formId; ?>(){
/* interval options */
	var currentValue = INTERVAL<?php echo $this->formId; ?>.value;
	var maxDuration = ENDS_AT<?php echo $this->formId; ?>.value - STARTS_AT<?php echo $this->formId; ?>.value;
	INTERVAL<?php echo $this->formId; ?>.options.length = 0;

	for( ii = 0; ii < INTERVAL_OPTIONS<?php echo $this->formId; ?>.length; ii++ ){
		var testOption = INTERVAL_OPTIONS<?php echo $this->formId; ?>[ii];
		if( maxDuration >= testOption[0] ){
			var selectMe = (currentValue == testOption[0]) ? true : false;
			INTERVAL<?php echo $this->formId; ?>.options.add( new Option(testOption[1], testOption[0], selectMe, selectMe) );
			}
		}
	}

var currentType = jQuery('#<?php echo $this->formId; ?>slot_type:checked').val();
if( currentType == 'range' ){
	setEndOption<?php echo $this->formId; ?>();
	setIntervalOption<?php echo $this->formId; ?>();
	}
else {
	setFixedStartOption<?php echo $this->formId; ?>();
	}

jQuery('#<?php echo $this->formId; ?>starts_at_range').live("change", function() {
	setEndOption<?php echo $this->formId; ?>();
	setIntervalOption<?php echo $this->formId; ?>();
	});

jQuery('#<?php echo $this->formId; ?>ends_at_range').live("change", function() {
	setStartOption<?php echo $this->formId; ?>();
	setIntervalOption<?php echo $this->formId; ?>();
	});

jQuery('#<?php echo $this->formId; ?>service_id').live("change", function() {
	var currentType = jQuery('#<?php echo $this->formId; ?>slot_type:checked').val();
	if( currentType == 'range' ){
		setEndOption<?php echo $this->formId; ?>();
		setIntervalOption<?php echo $this->formId; ?>();
		}
	else {
		setFixedStartOption<?php echo $this->formId; ?>();
		}
	});
</script>