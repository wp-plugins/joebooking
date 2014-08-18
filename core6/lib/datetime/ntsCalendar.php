<?php
class ntsCalendar {
	function getMonthMatrix( $year, $month, $extended = false ){
		$ntsConf =& ntsConf::getInstance();
		$weekStartsOn = $ntsConf->get('weekStartsOn');

		$matrix = array();
		$currentMonthDay = 0;

//		$ts = gmmktime(0, 0, 0, $month, 1, $year);
//		$firstDayOfWeek = gmdate( 'w', $ts );

		$t = new ntsTime;
		$t->setDateTime( $year, $month, 1, 0, 0, 0 );
		$startWeekIndex = $t->getWeekNo();
		$ts = $t->getTimestamp();
//		$t->setStartWeek();
		$firstDayOfWeek = $t->getWeekDay();

	// REFLECT THE WEEK STARTS ON
		$firstDayOfWeek = $firstDayOfWeek - ( $weekStartsOn - 1 );
		if( $firstDayOfWeek <= 0 )
			$firstDayOfWeek = $firstDayOfWeek + 7;

//		$ts = gmmktime(0, 0, 0, $month + 1, 0, $year);
//		$lastDayOfMonth = gmdate( 'j', $ts );

		$t->setDateTime( $year, $month + 1, 0, 0, 0, 0 );
		$lastDayOfMonth = $t->format('j');

		for( $week = 1; $week <= 6; $week++ ){
//			$weekIndex += $week - 1;
			$weekIndex = $startWeekIndex + $week - 1;
			$matrix[ $weekIndex ] = array();
			for( $weekDay = 1; $weekDay <= 7; $weekDay++ ){
				$weekDayIndex = $weekDay - 1;

				if( $currentMonthDay )
					$currentMonthDay++;

				if( $week == 1 && $firstDayOfWeek == $weekDay )
					$currentMonthDay = 1;

				if( $currentMonthDay > $lastDayOfMonth )
					$currentMonthDay = 0;

				$matrix[ $weekIndex ][ $weekDayIndex ] = $currentMonthDay;
				}
			if( (! $currentMonthDay) || ($currentMonthDay == $lastDayOfMonth) )
				break;
			}

		return $matrix;
		}
	}
?>