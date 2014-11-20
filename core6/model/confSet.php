<?php
switch( $name ){
	case 'timeUnit':
		$currentValue = $this->get('timeUnit');

		if( $value != $currentValue ){
			$ntsdb =& dbWrapper::getInstance();
			
		/* UPDATE TIMEBLOCKS AND SERVICES */
			$checkOut = array(
				array( 'timeblocks', 'starts_at', 'down' ),
				array( 'timeblocks', 'ends_at', 'up' ),
				array( 'timeblocks', 'selectable_every', 'down' ),

				array( 'services', 'duration', 'up' ),
				array( 'services', 'lead_in', 'up' ),
				array( 'services', 'lead_out', 'up' ),
				);

			$checkMultiply = 60 * $value;
			reset( $checkOut );
			foreach( $checkOut as $co ){
				$where = array(
					$co[1]											=> array( '<>', 0 ),
					'MOD(' . $co[1] . ', ' . $checkMultiply . ')'	=> array( '<>', 0 ),
					);
				$result = $ntsdb->select( 'DISTINCT ' . $co[1], $co[0], $where );

				$changes = array();
				while( $i = $result->fetch() ){
					$changes[ $i[$co[1]] ] = $i[$co[1]];
					}

				reset( $changes );
				foreach( array_keys($changes) as $oo ){
					if( $oo > $checkMultiply ){
						$remain = $oo % $checkMultiply;
						if( $co[2] == 'down' ){
							$newValue = $oo - $remain;
							}
						else {
							$newValue = $oo + ($checkMultiply - $remain);
							}
						}
					else {
						$newValue = $checkMultiply;
						}
					$changes[ $oo ] = $newValue;
					}

				reset( $changes );
				foreach( $changes as $oo => $no ){
					if( $no == $oo )
						continue;
					$what = array($co[1] => $no);
					$where = array($co[1] => array('=', $oo) );
					$ntsdb->update( $co[0], $what, $where );
					}
				}
			}
		break;

	case 'timeStarts':
		$timeUnit = $this->get('timeUnit');
		$remain = $value % (60*$timeUnit);

		if( $remain ){
			$return = $value - $remain;
			}
		else {
			$return = $value;
			}
		break;

	case 'timeEnds':
		$timeUnit = $this->get('timeUnit');
		$remain = $value % (60*$timeUnit);
		if( $remain )
			$return = $value + (60*$timeUnit - $remain);
		else
			$return = $value;
		break;

	case 'appointmentFlow':
		if( ! $value )
		{
			$return = '';
		}
		else
		{
			$value2 = array();
			reset( $value );
			foreach( $value as $v )
			{
				$value2[] = join( ':', $v );
			}
			$return = join( '|', $value2 );
		}
		break;

	case 'emailDebug':
		$return = ( $value ) ? 1 : 0;
		break;

	case 'priceFormat':
		$return = join( '||', $value );
		break;

	case 'languages':
		if( ! $value )
			$return = 'en';
		else {
			$return = join( '||', $value );
			}
		break;

	case 'paymentGateways':
		if( ! $value )
			$return = '';
		else {
			$return = join( '||', $value );
			}
		break;

	case 'plugins':
		if( ! $value )
			$return = '';
		else {
			$return = join( '||', $value );
			}
		break;
	}
?>