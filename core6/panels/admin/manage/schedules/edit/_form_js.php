<script language="JavaScript">
/* check the options of start */
var CURRENT_DURATION<?php echo $this->formId; ?> = <?php echo $thisDuration; ?>;

<?php if ( $slotType == 'range' ) : ?>
var STARTS_AT<?php echo $this->formId; ?> = document.getElementById('<?php echo $this->formId; ?>starts_at');
var STARTS_AT_OPTIONS<?php echo $this->formId; ?> = new Array( STARTS_AT<?php echo $this->formId; ?>.options.length );
for( ii = 0; ii < STARTS_AT<?php echo $this->formId; ?>.options.length; ii++ )
{
	STARTS_AT_OPTIONS<?php echo $this->formId; ?>[ii] = new Array( STARTS_AT<?php echo $this->formId; ?>.options[ii].value, STARTS_AT<?php echo $this->formId; ?>.options[ii].text );
}
<?php endif; ?>

<?php if ( $slotType == 'fixed' ) : ?>
var STARTS_FIXED<?php echo $this->formId; ?> = document.getElementById('<?php echo $this->formId; ?>starts_at');
var STARTS_FIXED_OPTIONS<?php echo $this->formId; ?> = new Array( STARTS_FIXED<?php echo $this->formId; ?>.options.length );
for( ii = 0; ii < STARTS_FIXED<?php echo $this->formId; ?>.options.length; ii++ )
{
	STARTS_FIXED_OPTIONS<?php echo $this->formId; ?>[ii] = new Array( STARTS_FIXED<?php echo $this->formId; ?>.options[ii].value, STARTS_FIXED<?php echo $this->formId; ?>.options[ii].text );
}
<?php endif; ?>

<?php if ( $slotType == 'range' ) : ?>
var ENDS_AT<?php echo $this->formId; ?> = document.getElementById('<?php echo $this->formId; ?>ends_at');
var INTERVAL<?php echo $this->formId; ?> = document.getElementById('<?php echo $this->formId; ?>selectable_every');

var ENDS_AT_OPTIONS<?php echo $this->formId; ?> = new Array( ENDS_AT<?php echo $this->formId; ?>.options.length );
for( ii = 0; ii < ENDS_AT<?php echo $this->formId; ?>.options.length; ii++ )
{
	ENDS_AT_OPTIONS<?php echo $this->formId; ?>[ii] = new Array( ENDS_AT<?php echo $this->formId; ?>.options[ii].value, ENDS_AT<?php echo $this->formId; ?>.options[ii].text );
}
var INTERVAL_OPTIONS<?php echo $this->formId; ?> = new Array( INTERVAL<?php echo $this->formId; ?>.options.length );
for( ii = 0; ii < INTERVAL<?php echo $this->formId; ?>.options.length; ii++ )
{
	INTERVAL_OPTIONS<?php echo $this->formId; ?>[ii] = new Array( INTERVAL<?php echo $this->formId; ?>.options[ii].value, INTERVAL<?php echo $this->formId; ?>.options[ii].text );
}
<?php endif; ?>

function setEndOption<?php echo $this->formId; ?>(){
	var currentDuration = CURRENT_DURATION<?php echo $this->formId; ?>;

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
	var currentDuration = CURRENT_DURATION<?php echo $this->formId; ?>;

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
	var currentDuration = CURRENT_DURATION<?php echo $this->formId; ?>;

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

<?php if ( $slotType == 'range' ) : ?>
//	setEndOption<?php echo $this->formId; ?>();
//	setIntervalOption<?php echo $this->formId; ?>();
<?php else : ?>
//	setFixedStartOption<?php echo $this->formId; ?>();
<?php endif; ?>

<?php if ( $slotType == 'range' ) : ?>
	jQuery('#<?php echo $this->formId; ?>starts_at').live("change", function() {
		setEndOption<?php echo $this->formId; ?>();
		setIntervalOption<?php echo $this->formId; ?>();
		});

	jQuery('#<?php echo $this->formId; ?>ends_at').live("change", function() {
		setStartOption<?php echo $this->formId; ?>();
		setIntervalOption<?php echo $this->formId; ?>();
		});
<?php endif; ?>
</script>