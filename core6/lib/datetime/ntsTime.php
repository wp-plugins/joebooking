<?php
global $NTS_TIME_WEEKDAYS;
$NTS_TIME_WEEKDAYS = array( M('Sunday'), M('Monday'), M('Tuesday'), M('Wednesday'), M('Thursday'), M('Friday'), M('Saturday'),  );

global $NTS_TIME_WEEKDAYS_SHORT;
$NTS_TIME_WEEKDAYS_SHORT = array( M('Sun'), M('Mon'), M('Tue'), M('Wed'), M('Thu'), M('Fri'), M('Sat') );

global $NTS_TIME_MONTH_NAMES;
$NTS_TIME_MONTH_NAMES = array( 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' );

global $NTS_TIME_MONTH_NAMES_REPLACE;
$NTS_TIME_MONTH_NAMES_REPLACE = array();
reset( $NTS_TIME_MONTH_NAMES );
foreach( $NTS_TIME_MONTH_NAMES as $mn ){
	$NTS_TIME_MONTH_NAMES_REPLACE[] = M($mn);
	}

/* new object oriented style */
class ntsTime extends DateTime {
	var $timeFormat = 'H:i';
	var $dateFormat = 'd/m/Y';
	var $weekdays = array();
	var $weekdaysShort = array();
	var $monthNames = array();
	var $timezone = '';
	var $date_icon = '';
	var $time_icon = '';

	function __construct( $time = 0, $tz = '' )
	{
//static $initCount;
//$initCount++;
//echo "<h2>init $initCount</h2>";
		if( strlen($time) == 0 )
			$ts = 0;
		if( ! $time )
			$time = time();
		if( is_array($time) )
			$time = $time[0];

		parent::__construct();
		$this->setTimestamp( $time );

		if( ! $tz ){
			$tz = NTS_COMPANY_TIMEZONE;
			}
		$this->setTimezone( $tz );

		$this->timeFormat = NTS_TIME_FORMAT;
		$this->dateFormat = NTS_DATE_FORMAT;
		
		$this->date_icon = '<i class="fa fa-calendar"></i>';
		$this->time_icon = '<i class="fa fa-clock-o"></i>';
	}

	function setNow(){
		$this->setTimestamp( time() );
		}

	function formatDateRange( $date1, $date2 )
	{
		$return = array();
		$skip = array();

		$this->setDateDb( $date1 );

		if( $date1 == $date2 )
		{
			$return = $this->formatDateFull();
			return $return;
		}

		$year1 = $this->getYear();
		$month1 = $this->getMonth();
		$day1 = $this->getDay();
		$this->setStartMonth();
		$start_month = $this->formatDate_Db();

		$this->setDateDb( $date2 );
		$year2 = $this->getYear();
		$month2 = $this->getMonth();
	// check if it is whole month
		$this->setEndMonth();
		$end_month = $this->formatDate_Db();

		if( $year2 == $year1 )
			$skip['year'] = TRUE;

		if( ($year2 == $year1) && ($month2 == $month1) )
			$skip['month'] = TRUE;

		$save_format = $this->dateFormat;
		if( ($start_month == $date1) && ($end_month == $date2) )
		{
			$return[] = $this->getMonthName() . ' ' . $this->getYear();
		}
		elseif( $skip )
		{
			$date_format = $this->dateFormat;
			$date_format_short = $date_format;

			$tags = array('m', 'n', 'M');
			foreach( $tags as $t )
			{
				$pos_m_original = strpos($date_format_short, $t);
				if( $pos_m_original !== FALSE )
					break;
			}

			if( isset($skip['year']) )
			{
				$pos_y = strpos($date_format_short, 'Y');
				if( $pos_y == 0 )
				{
					$date_format_short = substr_replace( $date_format_short, '', $pos_y, 2 );
				}
				else
				{
					$date_format_short = substr_replace( $date_format_short, '', $pos_y - 1, 2 );
				}
			}
			if( isset($skip['month']) )
			{
				$tags = array('m', 'n', 'M');
				foreach( $tags as $t )
				{
					$pos_m = strpos($date_format_short, $t);
					if( $pos_m !== FALSE )
						break;
				}

				if( $pos_m_original == 0 ) // month going first, do not replace
				{
//					$date_format_short = substr_replace( $date_format_short, '', $pos_m, 2 );
				}
				else
				{
					if( $pos_m == 0 ) // month going first, do not replace
					{
						$date_format_short = substr_replace( $date_format_short, '', $pos_m, 2 );
					}
					else
					{
						$date_format_short = substr_replace( $date_format_short, '', $pos_m - 1, 2 );
					}
				}
			}

			if( $pos_y == 0 ) // skip year in the second part
			{
				$date_format1 = $date_format;
				$date_format2 = $date_format_short;
			}
			else
			{
				$date_format1 = $date_format_short;
				$date_format2 = $date_format;
			}

			$this->setDateDb( $date1 );
			$this->dateFormat = $date_format1;
			$return[] = $this->formatDate();
			$this->setDateDb( $date2 );
			$this->dateFormat = $date_format2;
			$return[] = $this->formatDate();

			$this->dateFormat = $save_format;
		}
		else
		{
			$this->setDateDb( $date1 );
			$return[] = $this->formatDate();
			$this->setDateDb( $date2 );
			$return[] = $this->formatDate();
		}
		$return = join( ' - ', $return );
		return $return;
	}

	static function expandPeriodString( $what, $multiply = 1 ){
		$string = '';
		switch( $what ){
			case 'd':
				$string = '+' . 1 * $multiply . ' days';
				break;
			case '2d':
				$string = '+' . 2 * $multiply . ' days';
				break;
			case 'w':
				$string = '+' . 1 * $multiply . ' weeks';
				break;
			case '2w':
				$string = '+' . 2 * $multiply . ' weeks';
				break;
			case '3w':
				$string = '+' . 3 * $multiply . ' weeks';
				break;
			case '6w':
				$string = '+' . 6 * $multiply . ' weeks';
				break;
			case 'm':
				$string = '+' . 1 * $multiply . ' months';
				break;
			}
		return $string;
		}

	function setTimezone( $tz )
	{
		if( is_array($tz) )
			$tz = $tz[0];

//		if( preg_match('/^-?[\d\.]$/', $tz) ){
//			$currentTz = ($tz >= 0) ? '+' . $tz : $tz;
//			$tz = "Etc/GMT$currentTz";
//			echo "<br><br>Setting timezone as Etc/GMT$currentTz<br><br>";
//			}

		if( strlen($tz) )
		{
			$this->timezone = $tz;
			$tz = new DateTimeZone($tz);
			parent::setTimezone( $tz );
		}
	}

	function getLastDayOfMonth(){
		$thisYear = $this->getYear(); 
		$thisMonth = $this->getMonth();

		$this->setDateTime( $thisYear, ($thisMonth + 1), 0, 0, 0, 0 );
		$return = $this->format( 'j' );
		return $return;
		}

	function getTimestamp(){
		if( function_exists('date_timestamp_get') ){
			return parent::getTimestamp();
			}
		else {
			$return = $this->format('U');
			return $return;
			}
		}

	function setTimestamp( $ts )
	{
		if( strpos($ts, '-') !== FALSE )
		{
			$tss = explode( '-', $ts );
			$ts = $tss[0];
		}
		if( ! strlen($ts) )
			$ts = 0;
		if( function_exists('date_timestamp_set') )
		{
			return parent::setTimestamp( $ts );
		}
		else
		{
			$strTime = '@' . $ts;
			parent::__construct( $strTime );
			$this->setTimezone( $this->timezone );
			return;
		}
	}

	static function splitDate( $string ){
		$year = substr( $string, 0, 4 );
		$month = substr( $string, 4, 2 );
		$day = substr( $string, 6, 4 );
		$return = array( $year, $month, $day );
		return $return;
		}

	function timestampFromDbDate( $date ){
		list( $year, $month, $day ) = ntsTime::splitDate( $date );
		$this->setDateTime( $year, $month, $day, 0, 0, 0 );
		$return = $this->getTimestamp();
		return $return;
		}

	function getParts(){
		$return = array( $this->format('Y'), $this->format('m'), $this->format('d'), $this->format('H'), $this->format('i') );
		return $return;
		}

	function getYear(){
		$return = $this->format('Y');
		return $return;
		}

	function getMonth(){
		$return = $this->format('m');
		return $return;
		}

	function getMonthName(){
		global $NTS_TIME_MONTH_NAMES;
		$thisMonth = (int) $this->getMonth();
		$return = $NTS_TIME_MONTH_NAMES[ $thisMonth - 1 ];
		return $return;
		}

	function getDay(){
		$return = $this->format('d');
		return $return;
		}

	function getTimeOfDay(){
		$ts = $this->getTimestamp();
		$dayStart = $this->getStartDay();
		$return = $ts - $dayStart;
		return $return;
		}

	function formatTimeOfDay( $ts ){
		$this->setDateDb('20130315');
		$this->modify( '+' . $ts . ' seconds' );
		return $this->formatTime();
		}

	function getStartDay(){
		$thisYear = $this->getYear(); 
		$thisMonth = $this->getMonth();
		$thisDay = $this->getDay();

		$this->setDateTime( $thisYear, $thisMonth, $thisDay, 0, 0, 0 );
		$return = $this->getTimestamp();
		return $return;
		}

	function setStartDay(){
		$thisYear = $this->getYear(); 
		$thisMonth = $this->getMonth();
		$thisDay = $this->getDay();

		$this->setDateTime( $thisYear, $thisMonth, $thisDay, 0, 0, 0 );
		$return = $this->getTimestamp();
		return $return;
		}

	function setNextDay(){
		$this->setStartDay();
		$this->modify( '+1 day' );
		}

	function getEndDay(){
		$thisYear = $this->getYear(); 
		$thisMonth = $this->getMonth();
		$thisDay = $this->getDay();

		$this->setDateTime( $thisYear, $thisMonth, ($thisDay + 1), 0, 0, 0 );
		$return = $this->getTimestamp();
		return $return;
		}

	function setStartWeek(){
		$conf =& ntsConf::getInstance();
		$weekStartsOn = $conf->get('weekStartsOn');

		$this->setStartDay();
		$weekDay = $this->getWeekday();

		while( $weekDay != $weekStartsOn ){
			$this->modify( '-1 day' );
			$weekDay = $this->getWeekday();
			}
		}

	function setEndWeek()
	{
		$conf =& ntsConf::getInstance();
		$weekStartsOn = $conf->get('weekStartsOn');

		$this->setStartDay();
		$this->modify( '+1 day' );
		$weekDay = $this->getWeekday();

		while( $weekDay != $weekStartsOn )
		{
			$this->modify( '+1 day' );
			$weekDay = $this->getWeekday();
		}
		$this->modify( '-1 day' );
	}

	function setStartMonth(){
		$thisYear = $this->getYear(); 
		$thisMonth = $this->getMonth();
		$this->setDateTime( $thisYear, $thisMonth, 1, 0, 0, 0 );
		}

	function getStartMonth()
	{
		$thisYear = $this->getYear(); 
		$thisMonth = $this->getMonth();
		$this->setDateTime( $thisYear, $thisMonth, 1, 0, 0, 0 );

		$return = $this->getTimestamp();
		return $return;
	}

	function setEndMonth(){
		$thisYear = $this->getYear(); 
		$thisMonth = $this->getMonth();
		$this->setDateTime( $thisYear, ($thisMonth + 1), 1, 0, 0, -1 );
		}

	function getEndMonth()
	{
		$thisYear = $this->getYear(); 
		$thisMonth = $this->getMonth();
		$this->setDateTime( $thisYear, ($thisMonth + 1), 1, 0, 0, -1 );

		$return = $this->getTimestamp();
		return $return;
	}

	function setStartYear(){
		$thisYear = $this->getYear(); 
		$this->setDateTime( $thisYear, 1, 1, 0, 0, 0 );
		}

	function timezoneShift(){
		$return = 60 * 60 * $this->timezone;
		return $return;
		}

	function setDateTime( $year, $month, $day, $hour, $minute, $second ){
		$this->setDate( $year, $month, $day );
		$this->setTime( $hour, $minute, $second );
		}

	function setDateDb( $date )
	{
		list( $year, $month, $day ) = ntsTime::splitDate( $date );
		$this->setDateTime( $year, $month, $day, 0, 0, 0 );
	}

	function setMonthDb( $date )
	{
		$date = $date . '01';
		$this->setDateDb( $date );
	}

	function formatTime( $duration = 0, $displayTimezone = 0, $html = FALSE )
	{
		$return = $this->format( $this->timeFormat );
		if( $duration )
		{
			$start_date = $this->formatDate_Db();
			$this->modify( '+' . $duration . ' seconds' );
			$end_date = $this->formatDate_Db();

			$return .= ' - ';
			if( $end_date != $start_date )
			{
				$return .= $this->formatDateFull() . ' ';
			}
			$return .= $this->format( $this->timeFormat );
		}

		if( $displayTimezone )
		{
			$return .= ' [' . ntsTime::timezoneTitle($this->timezone) . ']';
		}

		if( $html )
		{
			$return = $this->time_icon . $return;
		}
		return $return;
	}

	function formatDate( $ts = 0, $html = FALSE )
	{
		global $NTS_TIME_MONTH_NAMES, $NTS_TIME_MONTH_NAMES_REPLACE;
		if( $ts )
		{
			$this->setTimestamp( $ts );
		}
		$return = $this->format( $this->dateFormat );
	// replace months 
		$return = str_replace( $NTS_TIME_MONTH_NAMES, $NTS_TIME_MONTH_NAMES_REPLACE, $return );

		if( $html )
		{
			$return = $this->date_icon . $return;
		}
		return $return;
	}

	function formatDateFull( $ts = 0, $html = FALSE )
	{
		$return = $this->formatWeekdayShort($ts) . ', ' . $this->formatDate($ts);
		if( $html )
		{
			$return = $this->date_icon . $return;
		}
		return $return;
	}

	static function formatDateParam( $year, $month, $day ){
		$return = sprintf("%04d%02d%02d", $year, $month, $day);
		return $return;
		}

	function formatDate_Db( $ts = 0 )
	{
		if( $ts )
		{
			$this->setTimestamp( $ts );
		}
		$dateFormat = 'Ymd';
		$return = $this->format( $dateFormat );
		return $return;
	}

	function formatMonth_Db( $ts = 0 )
	{
		if( $ts )
		{
			$this->setTimestamp( $ts );
		}
		$dateFormat = 'Ym';
		$return = $this->format( $dateFormat );
		return $return;
	}

	function formatTime_Db(){
		$dateFormat = 'Hi';
		$return = $this->format( $dateFormat );
		return $return;
		}

	function getWeekday(){
		$return = $this->format('w');
		return $return;
		}

	function formatWeekday(){
		global $NTS_TIME_WEEKDAYS;
		$return = $NTS_TIME_WEEKDAYS[ $this->format('w') ];
		return $return;
		}

	function formatFull( $ts = 0 )
	{
		$return = $this->formatDateFull($ts) . ' ' . $this->formatTime(0, 0, $ts);
		return $return;
	}

	function formatWeekdayShort( $ts = 0 )
	{
		if( $ts )
		{
			$this->setTimestamp( $ts );
		}
		return ntsTime::weekdayLabelShort( $this->format('w') );
	}

	static function weekdayLabelShort( $wdi )
	{
		global $NTS_TIME_WEEKDAYS_SHORT;
		$return = $NTS_TIME_WEEKDAYS_SHORT[ $wdi ];
		return $return;
	}

	static function timezoneTitle( $tz, $showOffset = FALSE ){
		if( is_array($tz) )
			$tz = $tz[0];
		$tzobj = new DateTimeZone( $tz );
		$dtobj = new DateTime();
		$dtobj->setTimezone( $tzobj );


		if( $showOffset ){
			$offset = $tzobj->getOffset($dtobj);
			$offsetString = 'GMT';
			$offsetString .= ($offset >= 0) ? '+' : '';
			$offsetString = $offsetString . ( $offset/(60 * 60) );
			$return = $tz . ' (' . $offsetString . ')';
			}
		else {
			$return = $tz;
			}

		return $return;
		}

	static function getTimezones(){
		$skipStarts = array('Brazil/', 'Canada/', 'Chile/', 'Etc/', 'Mexico/', 'US/');
		$skipStarts = array();
		$return = array();

		if( defined('DateTimeZone::ALL_WITH_BC') )
			$timezones = timezone_identifiers_list( DateTimeZone::ALL_WITH_BC );
		else
			$timezones = timezone_identifiers_list();

		reset( $timezones );
		foreach( $timezones as $tz ){
			if( strpos($tz, "/") === false )
				continue;
			$skipIt = false;
			reset( $skipStarts );
			foreach( $skipStarts as $skip ){
				if( substr($tz, 0, strlen($skip)) == $skip ){
					$skipIt = true;
					break;
					}
				}
			if( $skipIt )
				continue;

			$tzTitle = ntsTime::timezoneTitle( $tz );
			$return[] = array( $tz, $tzTitle );
			}
		return $return;
		}

	static function formatPeriodShort( $ts, $limit = 'day' ) // or hour
	{
		if( ! $limit )
			$limit = 'day';
		switch( $limit )
		{
			case 'day':
				$day = (int) ($ts/(24 * 60 * 60));
				$hour = (int) ( ($ts - (24 * 60 * 60)*$day)/(60 * 60));
				break;
			case 'hour':
				$day = 0;
				$hour = (int) ( ($ts - (24 * 60 * 60)*$day)/(60 * 60));
				break;
			case 'minute':
				$day = 0;
				$hour = 0;
				break;
		}

		$minute = (int) ( $ts - (24 * 60 * 60)*$day - (60 * 60)*$hour ) / 60;

		$formatArray = array();
		if( $day > 0 )
		{
			$formatArray[] = $day;
		}
		$formatArray[] = sprintf( "%02d", $hour );
		$formatArray[] = sprintf( "%02d", $minute );

		$verbose = join( ':', $formatArray );
		return $verbose;
	}

	static function formatPeriod( $ts ){
		$conf =& ntsConf::getInstance();
		$limitMeasure = $conf->get('limitTimeMeasure');

		switch( $limitMeasure ){
			case 'minute':
				$day = 0;
				$hour = 0;
				$minute = (int) ( $ts ) / 60;
				break;
			case 'hour':
				$day = 0;
				$hour = (int) ( ($ts)/(60 * 60));
				$minute = (int) ( $ts - (60 * 60)*$hour ) / 60;
				break;
			default:
				$day = (int) ($ts/(24 * 60 * 60));
				$hour = (int) ( ($ts - (24 * 60 * 60)*$day)/(60 * 60));
				$minute = (int) ( $ts - (24 * 60 * 60)*$day - (60 * 60)*$hour ) / 60;
				break;
			}

		$formatArray = array();
		if( $day > 0 ){
			if( $day > 1 )
				$formatArray[] = $day . ' ' . M('Days');
			else
				$formatArray[] = $day . ' ' . M('Day');
			}
		if( $hour > 0 ){
			if( $hour > 1 )
				$formatArray[] = $hour . ' ' . M('Hours');
			else
				$formatArray[] = $hour . ' ' . M('Hour');
			}
		if( $minute > 0 ){
			if( $minute > 1 )
				$formatArray[] = $minute . ' ' . M('Minutes');
			else
				$formatArray[] = $minute . ' ' . M('Minute');
			}

		$verbose = join( ' ', $formatArray );
		return $verbose;
		}

	function getWeekOfMonth()
	{
		$return = 0;
		$keepDate = $this->formatDate_Db();
		$thisMonth = $this->getMonth();
		$testMonth = $thisMonth;
		while( $testMonth == $thisMonth )
		{
			$return++;
			$this->modify( '-1 week' );
			$testMonth = $this->getMonth();
		}
		$this->setDateDb( $keepDate );
		return $return;
	}

	function formatWeekOfMonth()
	{
		$week = $this->getWeekOfMonth();
		$text = array(
			1	=> M('1st'),
			2	=> M('2nd'),
			3	=> M('3rd'),
			4	=> M('4th'),
			5	=> M('5th'),
			);
		return $text[$week];
	}

	function getMonthMatrix( $endDate = '' )
	{
		$matrix = array();
		$currentMonthDay = 0;
		$startDate = $this->formatDate_Db();

		if( $endDate )
			$this->setDateDb( $endDate );
		else
			$this->setEndMonth( $endDate );
		$this->setEndWeek();
		$endDate = $this->formatDate_Db();

		$this->setDateDb( $startDate );
		$this->setStartWeek();
		$rexDate = $this->formatDate_Db();
		

		while( $rexDate <= $endDate )
		{
			$week = array();
			for( $weekDay = 0; $weekDay <= 6; $weekDay++ )
			{
				$week[] = $rexDate;
				$this->modify('+1 day');
				$rexDate = $this->formatDate_Db();
			}
			$matrix[] = $week;
		}
		return $matrix;
	}

}
?>