<?php
class ntsService extends ntsObject {
	function ntsService(){
		parent::ntsObject( 'service' );
		}

	function getPrepayAmount()
	{
		$return = 0;
		$price = $this->getProp('price');
		if( (! strlen($price)) OR (! $price) )
		{
			return $return;
		}

		$prepay = $this->getPrepay();

		if( substr($prepay, -1) == '%' )
		{
			$percent = (int) substr($prepay, 0, -1);
			$return = ($price * $percent) / 100;
		}
		else
		{
			$return = $prepay;
		}
		return $return;
	}

	function getPrepay(){
		$return = $this->getProp('prepay');
		return $return;
		}

	/* returns true if approval required */
	function checkApproval( $customerId, $amount )
	{
		$return = TRUE;
		if( $amount )
		{
			$return = FALSE;
		}
		else
		{
			// customer groups
			$myGroupsIds = array();
			$prepay = $this->getPrepayAmount();
			if( $prepay > 0 )
			{
				/* check if we have online payments */
				$pgm =& ntsPaymentGatewaysManager::getInstance();
				$has_online = $pgm->hasOnline();
				$return = $has_online ? TRUE : FALSE;
			}
			else
			{
				if( $customerId )
				{
					$customer = new ntsUser();
					$customer->setId( $customerId );
					$restrictions = $customer->getProp('_restriction');

					if( $restrictions )
						$myGroupsIds[] = -1;
					else
						$myGroupsIds[] = 0;
				}
				else
				{
					$myGroupsIds[] = -1;
				}

				reset( $myGroupsIds );
				foreach( $myGroupsIds as $groupId )
				{
					$permission = $this->getPermissionsForGroup( $groupId );
					if( $permission == 'auto_confirm' )
					{
						$return = FALSE;
						break;
					}
				}
			}
		}
		return $return;
	}

	function getPackages( $forCustomer = false ){
		$return = array();
		$ntsdb =& dbWrapper::getInstance();
		$where = array(
			array(
				array(
					'service_id' => array('=', $this->getId()),
					),
				array(
					'service_id ' => array('=', 0),
					),
				)
			);
		$result = $ntsdb->select( 'id', 'packs', $where, 'ORDER BY qty ASC' );
		if( $result ){
			while( $e = $result->fetch() ){
				$pack = ntsObjectFactory::get( 'pack' );
				$pack->setId( $e['id'] );
				if( (! $forCustomer) || ($pack->getProp('price')))
					$return[] = $pack;
				}
			}
		return $return;
		}

/* possible values - 'not_allowed, 'not_shown', 'allowed', 'auto_confirm' */
	function getPermissions(){
		$return = array(); 

		$defaultPermissions = $this->getDefaultProp( '_permissions' );
		$rawPermissions = $this->getProp( '_permissions' );

		$return1 = array();
		reset( $defaultPermissions );
		foreach( $defaultPermissions as $ps ){
			list( $pk, $pv ) = explode( ':', $ps );
			$return1[ $pk ] = $pv;
			}
		
		$return2 = array();
		reset( $rawPermissions );
		foreach( $rawPermissions as $ps ){
			list( $pk, $pv ) = explode( ':', $ps );
			$return2[ $pk ] = $pv;
			}
		$return = array_merge( $return1, $return2 );
		return $return;
		}

	function getPermissionsForGroup( $groupId ){
		$permissions = $this->getPermissions();
		$key = 'group' . $groupId;
		if( isset($permissions[$key]) )
			$return = $permissions[$key];
		else {
			echo "<br>Permissions for group id $groupId not defined!<br>";
			$return = 'not_allowed';
			}
		return $return;
		}
	}
?>