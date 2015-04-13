<?php
class ntsPaymentManager {
	var $promotions = array();
	var $couponCountLeft = array();
	var $gotPromotions = array();
	var $invoice_items = array();
	var $invoice_transactions = array();

	function __construct()
	{
		$om =& objectMapper::getInstance();
		if( $om->isClassRegistered('promotion') )
		{
			$this->promotions = ntsObjectFactory::getAll('promotion');
		}
	}

	function resetPromotions()
	{
		$this->couponCountLeft = array();
		$this->gotPromotions = array();
	}

	function getRKey( $r )
	{
		$return = array(
			'duration'		=> isset($r['duration']) ? $r['duration'] : 0,
			'location_id'	=> isset($r['location_id']) ? $r['location_id'] : 0,
			'resource_id'	=> isset($r['resource_id']) ? $r['resource_id'] : 0,
			'service_id'	=> isset($r['service_id']) ? $r['service_id'] : 0,
			'starts_at'		=> isset($r['starts_at']) ? $r['starts_at'] : 0,
			);
		return $return;
	}

	function getPromotions( $r = array(), $suppliedCoupon = '', $requireCoupon = FALSE )
	{
		$return = array();
		$r = $this->_parseReady( $r );

		$key = $this->getRKey($r);
		$key['suppliedCoupon'] = $suppliedCoupon;
		$key['requireCoupon'] = $requireCoupon;
		$rKey = serialize( $key );

		if( isset($this->gotPromotions[$rKey]) )
		{
			return $this->gotPromotions[$rKey];
		}

		reset( $this->promotions );
		foreach( $this->promotions as $cp )
		{
			$on = TRUE;
			$rule = $cp->getRule();
			if( ! $rule )
				$rule = array();

			foreach( $rule as $key => $options )
			{
				if( ! $on )
				{
					break;
				}

				$on = FALSE;
				switch( $key )
				{
					case 'location':
						if( isset($r['location_id']) && in_array($r['location_id'], $options) )
						{
							$on = TRUE;
						}
						break;

					case 'resource':
						if( isset($r['resource_id']) && in_array($r['resource_id'], $options) )
						{
							$on = TRUE;
						}
						break;

					case 'service':
						if( isset($r['service_id']) && in_array($r['service_id'], $options) )
						{
							$on = TRUE;
						}
						break;

					case 'weekday':
						if( isset($r['starts_at']) )
						{
							$t = new ntsTime;
							$t->setTimestamp( $r['starts_at'] );
							$weekday = $t->getWeekday();
							if( in_array($weekday, $options) )
							{
								$on = TRUE;
							}
						}
						break;

					case 'time':
						if( isset($r['starts_at']) )
						{
							$from = $options[0];
							$to = $options[1];

							$t = new ntsTime;
							$t->setTimestamp( $r['starts_at'] );
							$timeOfDay = $t->getTimeOfDay();
							if( ($timeOfDay >= $from) && ( ($timeOfDay + $r['duration']) <= $to) )
							{
								$on = TRUE;
							}
						}
						break;

					case 'date':
						if( isset($r['starts_at']) )
						{
							$t = new ntsTime;
							$t->setTimestamp( $r['starts_at'] );
							$thisDate = $t->formatDate_Db();
							if( isset($options['from']) )
							{
								if( ($thisDate >= $options['from']) && ($thisDate <= $options['to']) )
								{
									$on = TRUE;
								}
							}
							else
							{
								if( in_array($thisDate, $options) )
								{
									$on = TRUE;
								}
							}
						}
						break;
				}
			}
			if( ! $on )
			{
				continue;
			}

		/* check coupons */
			$codes = $cp->getCouponCodes();

			if( $suppliedCoupon )
			{
				if( $codes && (! in_array($suppliedCoupon, $codes)) )
				{
					continue;
				}
				if( $requireCoupon && (! $codes) )
				{
					continue;
				}
			}
			else
			{
				if( $requireCoupon )
				{
					if( ! $codes )
					{
						continue;
					}
				}
				else
				{
					if( $codes && (! in_array($suppliedCoupon, $codes)) )
					{
						continue;
					}
				}
			}

			if( $codes && $suppliedCoupon && (! $requireCoupon) )
			{
				if( ! isset($this->couponCountLeft[$suppliedCoupon]) )
				{
					$thisCoupon = NULL;
					$coupons = $cp->getCoupons();
					reset( $coupons );
					foreach( $coupons as $cpn )
					{
						if( $suppliedCoupon == $cpn->getProp('code') )
						{
							$thisCoupon = $cpn;
							break;
						}
					}
					if( ! $thisCoupon )
						continue;
						
					$useLimit = $thisCoupon->getProp('use_limit');
					if( $useLimit )
					{
						$this->couponCountLeft[$suppliedCoupon] = $useLimit;
						$alreadyUsed = $thisCoupon->getUseCount();
						$this->couponCountLeft[$suppliedCoupon] = $this->couponCountLeft[$suppliedCoupon] - $alreadyUsed;
						if( $this->couponCountLeft[$suppliedCoupon] < 0 )
							$this->couponCountLeft[$suppliedCoupon] = 0;
					}
					else
					{
						$this->couponCountLeft[$suppliedCoupon] = -1;
					}
				}

				if( $this->couponCountLeft[$suppliedCoupon] > -1 )
				{
					if( $this->couponCountLeft[$suppliedCoupon] <= 0 )
						continue;
					$this->couponCountLeft[$suppliedCoupon]--;
				}
				$cp->setProp('coupon', $suppliedCoupon );
			}

			$return[] = $cp;
		}

	$this->gotPromotions[$rKey] = $return;
	return $this->gotPromotions[$rKey];
	}

	function getBasePrice( $r = array() )
	{
		$r = $this->_parseReady( $r );
		$return = '';
		if( ! isset($r['service_id']) ){
			return $return;
		}

		$service = ntsObjectFactory::get( 'service' );
		$service->setId( $r['service_id'] );
		$return = $service->getProp('price');
		if( ! strlen($return) ){
			return $return;
		}

		$seats = isset($r['seats']) ? $r['seats'] : 1;
		$return = $return * $seats;

		return $return;
	}

	function applyPromotion( $r, $promotion )
	{
		$return = 0;
		$r = $this->_parseReady( $r );
		$base_price = $this->getBasePrice( $r );
		if( ! strlen($base_price) )
		{
			return $return;
		}

		$sign = $promotion->getSign();
		$measure = $promotion->getMeasure();
		$amount = $promotion->getAmount();

		if( $measure == '%' )
		{
			$amount = round( ($amount/100) * $base_price, 2 );
		}

		if( $sign == '-' )
		{
			$return -= $amount;
		}
		else
		{
			$return += $amount;
		}
		return $return;
	}

	function changePrice( $r, $coupon = '' )
	{
		$return = 0;
		$r = $this->_parseReady( $r );
		$base_price = $this->getBasePrice( $r );
		if( ! strlen($base_price) )
		{
			return $return;
		}

	/* promotions */
		$promotions = $this->getPromotions( $r, $coupon );
		reset( $promotions );
		foreach( $promotions as $cp )
		{
			$amount = $this->applyPromotion( $r, $cp );
			$return += $amount;
		}
		return $return;
	}

	function getPrice( $r, $coupon = '' )
	{
		$r = $this->_parseReady( $r );

		$return = $this->getBasePrice( $r );
		if( ! strlen($return) )
		{
			return $return;
		}

	/* promotions */
		$change_price = $this->changePrice( $r, $coupon );
		if( $change_price )
		{
			$return += $change_price;
		}

	/* plugins - none so far */
		$plm =& ntsPluginManager::getInstance();
		$activePlugins = $plm->getActivePlugins();
		$pluginFiles = array();
		reset( $activePlugins );
		foreach( $activePlugins as $plg )
		{
			$f = $plm->getPluginFolder( $plg ) . '/getPrice.php';
			if( file_exists($f) )
				$pluginFiles[] = $f;
		}
		reset( $pluginFiles );
		foreach( $pluginFiles as $f )
		{
			require( $f );
		}
		return $return;
	}

	function _parseReady( $r = array() )
	{
		if( is_object($r) )
		{
			$r = $r->getByArray();
		}

		$return = array();
		$process = array(
			'location'	=> 'location_id',
			'resource'	=> 'resource_id',
			'service'	=> 'service_id',
			'time'		=> 'starts_at',
			);

		foreach( array_keys($r) as $k )
		{
			if( isset($process[$k]) )
			{
				$return[$process[$k]] = $r[$k];
			}
			else
			{
				$return[$k] = $r[$k];
			}
		}
		
		if( (! isset($return['duration'])) && ( isset($return['starts_at']) && isset($return['service_id']) ) )
		{
			$service = ntsObjectFactory::get( 'service', $return['service_id'] );
			$duration = $service->getProp( 'duration' );
			$return['duration'] = $duration;
		}
		ksort( $return );
		return $return;
	}

	function getPrepayAmount( $r = array(), $coupon = '' )
	{
		$r = $this->_parseReady( $r );

		if( isset($r['id']) && $r['id'] )
		{
			$app = ntsObjectFactory::get( 'appointment' );
			$app->setId( $r['id'] );
			$price = $app->getCost();
		}
		else
		{
			$price = isset($r['price']) ? $r['price'] : $this->getPrice( $r, $coupon );
		}
		$service = ntsObjectFactory::get( 'service' );
		$service->setId( $r['service_id'] );
		$prepay = $service->getPrepay();

		$pgm =& ntsPaymentGatewaysManager::getInstance();
		if( ! $pgm->hasOnline() )
		{
			$prepay = '100%';
		}

		$dueNow = 0;
		if( $price )
		{
			if( substr($prepay, -1) == '%' )
			{
				$percent = substr($prepay, 0, -1) / 100;
				$dueNow = $price * $percent;
			}
			else 
			{
				$dueNow = $prepay;
			}
		}
		return $dueNow;
	}

	function getTaxRate( $item = NULL )
	{
		$return = 0;
		$ntsconf =& ntsConf::getInstance();
		$taxRate = $ntsconf->get('taxRate');
		$return = $taxRate; 

		if( $item )
		{
			$className = $item->getClassName();
			switch( $className )
			{
				case 'appointment':
				case 'order':
				case 'pack':
				case 'service':
				default:
					$return = $taxRate; 
					break;
			}
		}
		return $return;
	}

	function getInvoiceItems( $invoice_id )
	{
		if( ! isset($this->invoice_items[$invoice_id]) ){
			$this->preloadInvoiceItems( array($invoice_id) );
		}
		$return = $this->invoice_items[$invoice_id];
		return $return;
	}

	function getInvoiceTransactions( $invoice_id )
	{
		if( ! isset($this->invoice_transactions[$invoice_id]) ){
			$this->preloadInvoiceItems( array($invoice_id) );
		}
		$return = $this->invoice_transactions[$invoice_id];
		return $return;
	}
	
	function preloadInvoiceItems( $invoice_ids )
	{
		foreach( $invoice_ids as $iid ){
			$this->invoice_items[ $iid ] = array();
			$this->invoice_transactions[ $iid ] = array();
		}

		$ntsdb =& dbWrapper::getInstance();

	/* transactions */
		$where = array(
			'invoice_id'	=> array( 'IN', $invoice_ids ),
			);
		$transaction_ids = $ntsdb->get_select(
			'id',
			'transactions',
			$where,
			'ORDER BY created_at ASC'
			);
		if( $transaction_ids )
		{
			ntsObjectFactory::preload( 'transaction', $transaction_ids );

			reset( $transaction_ids );
			foreach( $transaction_ids as $tid )
			{
				$e = ntsObjectFactory::get( 'transaction' );
				$e->setId( $tid );
				$this_iid = $e->getProp( 'invoice_id' );
				$this->invoice_transactions[ $this_iid ][] = $e;
			}
		}

	/* invoice items */
		$where = array(
			'invoice_id'	=> array( 'IN', $invoice_ids ),
			);
		$rows = $ntsdb->get_select(
			array( 'id', 'amount', 'qty', 'title', 'obj_class', 'obj_id', 'taxable', 'invoice_id' ),
			'invoice_items',
			$where,
			'ORDER BY id'
			);

		$also_preload = array();
		reset( $rows );
		foreach( $rows as $r )
		{
			if( $r['obj_class'] && $r['obj_id'] )
			{
				if( ! isset($also_preload[ $r['obj_class'] ]) )
				{
					$also_preload[ $r['obj_class'] ] = array();
				}
				$also_preload[ $r['obj_class'] ][ $r['obj_id'] ] = 1;
			}
		}
		reset( $also_preload );
		foreach( $also_preload as $class => $ids )
		{
			ntsObjectFactory::preload( $class, array_keys($ids) );
		}

		reset( $rows );
		foreach( $rows as $r )
		{
			$item = NULL;
			if( $r['obj_class'] && $r['obj_id'] )
			{
				$item_class = $r['obj_class'];
				$item_id = $r['obj_id'];

				$item = ntsObjectFactory::get( $item_class );
				$item->setId( $item_id );

				switch( $item_class )
				{
					case 'appointment':
					/* check which number of payments */
						$mySeq = 0;

					/*
						$this_invoices = $item->getInvoices();
						$totalCount = count($this_invoices);
						if( $totalCount > 1 )
						{
							for( $ii = 1; $ii <= $totalCount; $ii++ )
							{
								if( $this_invoices[$ii - 1][0] == $invoice_id )
								{
									$mySeq = $ii;
									break;
								}
							}
						}
					*/

						$itemName = M('Appointment') . ' ' . '[' . $item->statusText() . ']';
						$itemDescription = ntsView::objectTitle($item);
						if( $mySeq )
						{
							$itemDescription .= ' [' . M('Payment') . ' ' . $mySeq . '/' . $totalCount . ']';
						}
						break;

					case 'order':
						$itemName = M('Package');
						$itemDescription = $item->getFullTitle();
						break;
				}
			}
			else
			{
				$itemName = $r['title'];
				$itemDescription = '';
			}

			$tax_rate = $r['taxable'] ? $this->getTaxRate() : 0;
			$thisItem = array(
				'id'			=> $r['id'], 
				'name'			=> $itemName, 
				'description'	=> $itemDescription,
				'unitCost' 		=> $r['amount'],
				'unitTaxRate' 	=> $tax_rate,
				'quantity'		=> $r['qty'],
				'object'		=> $item,
				);
				
			if( ! isset($this->invoice_items[$r['invoice_id']]) )
			{
				$this->invoice_items[$r['invoice_id']] = array();
			}
			$this->invoice_items[$r['invoice_id']][] = $thisItem;
		}
	}

	function getInvoicesOfCustomer( $customerId )
	{
		$return = array();
		$ntsdb =& dbWrapper::getInstance();

		$where = array(
			'customer_id'	=> array( '=', $customerId )
			);
		$app_ids = $ntsdb->get_select( 'id', 'appointments', $where );
		$order_ids = $ntsdb->get_select( 'id', 'orders', $where );

		if( $app_ids OR $order_ids )
		{
			if( $app_ids && $order_ids )
			{
				$where = array(
					array(
						'obj_class'	=> array( '=', 'appointment' ),
						'obj_id'	=> array( 'IN', $app_ids ),
						),
					array(
						'obj_class'	=> array( '=', 'order' ),
						'obj_id'	=> array( 'IN', $order_ids ),
						),
					);
			}
			elseif( $app_ids )
			{
				$where = array(
					'obj_class'	=> array( '=', 'appointment' ),
					'obj_id'	=> array( 'IN', $app_ids ),
					);
			}
			elseif( $order_ids )
			{
				$where = array(
					'obj_class'	=> array( '=', 'order' ),
					'obj_id'	=> array( 'IN', $order_ids ),
					);
			}
			$return = $ntsdb->get_select( 
				'invoice_id',
				'invoice_items',
				$where
				);
		}

		/* also add direct invoices */
		$where = array(
			'customer_id'	=> array( '=', $customerId ),
			);

// remove, too slow
//		$where['exists'] = array( '', '(' . 'SELECT id FROM {PRFX}invoice_items WHERE {PRFX}invoice_items.invoice_id = {PRFX}invoices.id' . ')', TRUE );
		$direct_invoices = $ntsdb->get_select( 'id', 'invoices', $where );
		$new_ones = array_diff( $direct_invoices, $return );
		$return = array_merge( $return, $new_ones );

		return $return;
	}

	function makeInvoices( $items, $forceAmount = 0, $forceTime = 0 ){
		$return = array();
		$now = time();

		$makeInvoices = array(); // array( $item, $amount, array(taxrate, taxname) );
		$customer_id = 0;

		$invoiceAmount = 0;
		reset( $items );

		for( $ii = 0; $ii < count($items); $ii++ )
		{
			$item = $items[$ii];

			if( is_array($forceAmount) )
			{
				$thisForceAmount = isset($forceAmount[$ii]) ? $forceAmount[$ii] : 0;
			}
			else
			{
				$thisForceAmount = $forceAmount;
			}

			$className = $item->getClassName();
			switch( $className ){
				case 'appointment':
					$serviceId = $item->getProp( 'service_id' );
					$customer_id = $item->getProp( 'customer_id' );
					$service = ntsObjectFactory::get( 'service' );
					$service->setId( $serviceId );
					$startsAt = $item->getProp( 'starts_at' );

					if( $thisForceAmount )
					{
						$thisTime = $forceTime ? $forceTime : $startsAt;
						if( ! isset($makeInvoices[$thisTime]) )
							$makeInvoices[$thisTime] = array();
						$makeInvoices[$thisTime][] = array( $item, $thisForceAmount );
					}
					else
					{
						$couponCode = '';

						$totalPrice = $this->getPrice( $item->getByArray(), $couponCode );
						$totalPriceReal = $this->getPrice( $item->getByArray(), '' );
						$totalDiscount = ( $totalPriceReal - $totalPrice );
						$prepayAmount = $this->getPrepayAmount( $item->getByArray(), $couponCode );
						$prepayDiscount = ($totalPrice > 0) ? round( ($prepayAmount/$totalPrice)*$totalDiscount, 2 ) : 0;

						$thisPayments = array();
						if( $prepayAmount )
						{
							if( ! isset($makeInvoices[$now]) )
								$makeInvoices[$now] = array();
							$makeInvoices[$now][] = array( $item, $prepayAmount, $prepayDiscount );
						}
						if( $totalPrice > $prepayAmount )
						{
							$startsAt = $item->getProp( 'starts_at' );
							if( ! isset($makeInvoices[$startsAt]) )
								$makeInvoices[$startsAt] = array();
							$makeInvoices[$startsAt][] = array( $item, ($totalPrice - $prepayAmount), ($totalDiscount - $prepayDiscount) );
						}
					}
					break;

				case 'order':
					$customer_id = $item->getProp( 'customer_id' );
					$thisAmount = 0;
					if( $thisForceAmount )
					{
						$thisAmount = $thisForceAmount;
					}
					else
					{
						$packId = $item->getProp( 'pack_id' );
						$pack = ntsObjectFactory::get( 'pack' );
						$pack->setId( $packId );
						$thisAmount = $pack->getProp('price');
					}
					if( ! isset($makeInvoices[$now]) )
						$makeInvoices[$now] = array();
					$makeInvoices[$now][] = array( $item, $thisAmount, 0 );
					break;
				}
		}

		$cm =& ntsCommandManager::getInstance();

	/* check if we have specific Paypal emails for every resource */
		$finalMakeInvoices = array();

		$pgm =& ntsPaymentGatewaysManager::getInstance();
		$gateways = $pgm->getActiveGateways();
		$isPaypal = in_array('paypal', $gateways);
		if( $isPaypal )
		{
			$paymentGatewaySettings = $pgm->getGatewaySettings( 'paypal' );
			$mainPaypal = $paymentGatewaySettings['email'];
		}

		reset( $makeInvoices );
		foreach( $makeInvoices as $due => $items )
		{
			if( $isPaypal )
			{
				$byPaypal = array();

				reset( $items );
				foreach( $items as $ia )
				{
					$item = $ia[0];
					$itemAmount = $ia[1];
					$itemDiscount = isset($ia[2]) ? $ia[2] : 0;

					$resourceId = $item->getProp('resource_id');
					$resource = ntsObjectFactory::get( 'resource' );
					$resource->setId( $resourceId );
					$thisPaypal = $resource->getProp( '_paypal' );
					if( $thisPaypal )
					{
						if( ! isset($byPaypal[$thisPaypal]) )
							$byPaypal[$thisPaypal] = array();
						$byPaypal[$thisPaypal][] = array( $item, $itemAmount, $itemDiscount );
					}
					else
					{
						if( ! isset($byPaypal[$mainPaypal]) )
							$byPaypal[$mainPaypal] = array();
						$byPaypal[ $mainPaypal ][] = array( $item, $itemAmount, $itemDiscount );
					}
				}
				reset( $byPaypal );
				foreach( $byPaypal as $paypal => $items )
				{
					$finalMakeInvoices[] = array( $due, $items );
				}
			}
			else 
			{
				$finalMakeInvoices[] = array( $due, $items );
			}
		}

		for( $jj = 0; $jj < count($finalMakeInvoices); $jj++ )
		{
			list( $due, $items ) = $finalMakeInvoices[ $jj ];

			$invoiceAmount = 0;
			$invoice = ntsObjectFactory::get( 'invoice' );
			$invoice->setProp( 'amount', $invoiceAmount );
			$invoice->setProp( 'due_at', $due );
			$invoice->setProp( 'customer_id', $customer_id );

			$cm->runCommand( $invoice, 'create' );
			$invoiceId = $invoice->getId();

			reset( $items );
			foreach( $items as $ia )
			{
				$item = $ia[0];
				$itemAmount = $ia[1];
				$itemDiscount = isset($ia[2]) ? $ia[2] : 0;

				$tax = $this->getTaxRate( $item );
				$className = $item->getClassName();

				$item_array = array(
					'amount'	=> $itemAmount,
					'title'		=> '',
					'qty'		=> 1,
					'taxable'	=> 1,
					'obj_class'	=> $className,
					'obj_id'	=> $item->getId(),
					);

				$cm->runCommand( 
					$invoice, 
					'add_item',
					array(
						'item'	=> $item_array
						)
					);
			}
			$return[] = $invoice;
		}

	/* plugins */
		$plm =& ntsPluginManager::getInstance();
		$activePlugins = $plm->getActivePlugins();
		$transactionFiles = array();
		reset( $activePlugins );
		foreach( $activePlugins as $plg ){
			$trf = $plm->getPluginFolder( $plg ) . '/makeInvoices.php';
			if( file_exists($trf) )
				$transactionFiles[] = $trf;
			}
		reset( $transactionFiles );
		foreach( $transactionFiles as $trf ){
			require( $trf );
			}

		return $return;
		}

	function updateInvoice( $invoice )
	{
		$return = TRUE;
	/* plugins */
		$plm =& ntsPluginManager::getInstance();
		$activePlugins = $plm->getActivePlugins();
		$transactionFiles = array();
		reset( $activePlugins );
		foreach( $activePlugins as $plg )
		{
			$trf = $plm->getPluginFolder( $plg ) . '/updateInvoice.php';
			if( file_exists($trf) )
				$transactionFiles[] = $trf;
		}
		reset( $transactionFiles );
		foreach( $transactionFiles as $trf )
		{
			require( $trf );
		}
		return $return;
	}

	function deleteInvoice( $invoice )
	{
		$return = TRUE;
	/* plugins */
		$plm =& ntsPluginManager::getInstance();
		$activePlugins = $plm->getActivePlugins();
		$transactionFiles = array();
		reset( $activePlugins );
		foreach( $activePlugins as $plg )
		{
			$trf = $plm->getPluginFolder( $plg ) . '/deleteInvoice.php';
			if( file_exists($trf) )
				$transactionFiles[] = $trf;
		}
		reset( $transactionFiles );
		foreach( $transactionFiles as $trf )
		{
			require( $trf );
		}
		return $return;
	}

	function getTransactionsOfCustomer( $customer_id )
	{
		$return = array();
		$invoices_ids = $this->getInvoicesOfCustomer( $customer_id );
		reset( $invoices_ids );
		foreach( $invoices_ids as $iid )
		{
			$this_trans = $this->getTransactionsOfInvoice( $iid );
			$return = array_merge( $return, $this_trans );
		}
		return $return;
	}

	function getTransactionsOfInvoice( $invoiceId ){
		$invoice = ntsObjectFactory::get( 'invoice' );
		$invoice->setId( $invoiceId );

	/* plugins */
		$plm =& ntsPluginManager::getInstance();
		$activePlugins = $plm->getActivePlugins();
		$transactionFiles = array();
		reset( $activePlugins );
		foreach( $activePlugins as $plg ){
			$trf = $plm->getPluginFolder( $plg ) . '/getTransactionsOfInvoice.php';
			if( file_exists($trf) )
				$transactionFiles[] = $trf;
			}
		reset( $transactionFiles );
		foreach( $transactionFiles as $trf ){
			require( $trf );
			}

		if( $transactionFiles ){
			ntsObjectFactory::clearCache( 'invoice', $invoiceId );
			ntsObjectFactory::clearCache( 'transaction' );
			}

	/* now get them */
		$transactions = $invoice->getTransactions();

		return $transactions;
		}

	function deleteTransaction( $transId ){
		$tra = ntsObjectFactory::get('transaction');
		$tra->setId( $transId );
		$amount = $tra->getProp('amount');
		$invoiceId = $tra->getProp( 'invoice_id' );

		$cm =& ntsCommandManager::getInstance();
		$cm->runCommand( $tra, 'delete' );
		if( $cm->isOk() ){
			$return = true;
			}
		else {
			$return = false;
			}

		if( $return ){
		/* plugins */
			$plm =& ntsPluginManager::getInstance();
			$activePlugins = $plm->getActivePlugins();
			$transactionFiles = array();
			reset( $activePlugins );
			foreach( $activePlugins as $plg ){
				$trf = $plm->getPluginFolder( $plg ) . '/deleteTransaction.php';
				if( file_exists($trf) )
					$transactionFiles[] = $trf;
				}
			reset( $transactionFiles );
			foreach( $transactionFiles as $trf ){
				require( $trf );
				}
			}
		return $return;
		}

	function makeTransaction( $amount, $invoiceId = 0, $paymentInfo = array() ){
		$return = 0;
		$now = time();
		$ntsdb =& dbWrapper::getInstance();

		$what = array(
			'amount'		=> $amount,
			'created_at'	=> $now,
			'invoice_id'	=> $invoiceId,
			);
		$what = array_merge( $what, $paymentInfo );
		if( ! isset($what['amount_net']) )
			$what['amount_net'] = $what['amount'];

		$cm =& ntsCommandManager::getInstance();
		if( $amount )
		{
			$tra = ntsObjectFactory::get( 'transaction' );
			$tra->setByArray( $what );
			$cm->runCommand( $tra, 'create' );
			$return = $tra->getId();
		}

	/* send payments to interested objects */
		/* now it's managed in accountingManager */

		if( $amount ){
		/* plugins */
			$plm =& ntsPluginManager::getInstance();
			$activePlugins = $plm->getActivePlugins();
			$transactionFiles = array();
			reset( $activePlugins );
			foreach( $activePlugins as $plg ){
				$trf = $plm->getPluginFolder( $plg ) . '/makeTransaction.php';
				if( file_exists($trf) )
					$transactionFiles[] = $trf;
				}
			reset( $transactionFiles );
			foreach( $transactionFiles as $trf ){
				require( $trf );
				}
			}
		return $return;
		}

	// Singleton stuff
	static function &getInstance(){
		return ntsLib::singletonFunction( 'ntsPaymentManager' );
		}
	}

class ntsPaymentGatewaysManager {
	var $dir;
	var $gateways;

	function ntsPaymentGatewaysManager(){
		$this->dir = NTS_APP_DIR . '/payment';
		$this->gateways = array();
		$this->allCurrencies = array(
			array( 'usd', 'USD U.S. Dollar' ),
			array( 'eur', 'EUR Euro' ),
			array( 'gbp', 'GBP Pound Sterling' ),
			array( 'aud', 'AUD Australian Dollar' ),
			array( 'cad', 'CAD Canadian Dollar'),
			array( 'jpy', 'JPY Japanese Yen' ),
			array( 'nzd', 'NZD New Zealand Dollar' ),
			array( 'chf', 'CHF Swiss Franc' ),
			array( 'hkd', 'HKD Hong Kong Dollar'),
			array( 'sgd', 'SGD Singapore Dollar' ),
			array( 'sek', 'SEK Swedish Krona' ),
			array( 'dkk', 'DKK Danish Krone' ),
			array( 'pln', 'PLN Polish Zloty' ),
			array( 'nok', 'NOK Norwegian Krone' ),
			array( 'huf', 'HUF Hungarian Forint' ),
			array( 'czk', 'CZK Czech Koruna' ),
			array( 'ils', 'ILS Israeli Shekel' ),
			array( 'mxn', 'MXN Mexican Peso' ),
			array( 'brl', 'BRL Brazilian Real' ),
			array( 'myr', 'MYR Malaysian Ringgits' ),
			array( 'php', 'PHP Philippine Pesos' ),
			array( 'twd', 'TWD Taiwan New Dollars' ),
			array( 'thb', 'THB Thai Baht' ),
			array( 'vef', 'VEF Venezuelan Bolivar' ),
			array( 'xcd', 'XCD East Caribbean Dollar' ),
			array( 'ars', 'ARS Argentine Peso' ),
			array( 'inr', 'INR Indian Rupee' ),
			array( 'ltl', 'LTL Lithuanian Litas' ),
			array( 'ron', 'RON Romanian New Leu' ),
			array( 'rub', 'RUB Russian Ruble' ),
			array( 'zar', 'ZAR South African Rand' ),
			array( 'try', 'TRY Turkish Lira' ),
			array( 'aed', 'AED United Arab Emirates Dirham' ),
			);
		}

	function hasOnline()
	{
		$return = TRUE;
		$allGateways = $this->getActiveGateways();
		if( (count($allGateways) == 1) && ($allGateways[0] == 'offline') )
			$return = FALSE;
		return $return;
	}

	function hasOffline()
	{
		$return = FALSE;
		$allGateways = $this->getActiveGateways();
		if( in_array('offline', $allGateways) )
		{
			$settings = $this->getGatewaySettings( 'offline' );
			$return = ( isset($settings['label']) && strlen($settings['label']) ) ? $settings['label'] : M('Pay At Our Office');
		}
		return $return;
	}

	function getAllCurrencies(){
		return $this->allCurrencies;
		}

	function gatewayActivate( $newGateway ){
		$conf =& ntsConf::getInstance();
		$setting = $conf->get( 'paymentGateways' );

		$gatewayAdded = '';
		if( ! in_array($newGateway, $setting) ){
			$setting[] = $newGateway;
			$gatewayAdded = $newGateway;
			}
		return $setting;
		}

	function gatewayDisable( $disableGateway ){
		$conf =& ntsConf::getInstance();
		$setting = $conf->get( 'paymentGateways' );

		$newSetting = array();
		reset( $setting );
		foreach( $setting as $s ){
			if( $s == $disableGateway )
				continue;
			$newSetting[] = $s;
			}

		return $newSetting;
		}

	function getActiveGateways()
	{
		$active = array();
		$conf =& ntsConf::getInstance();
		$gateways = $this->getGateways();
		$activeGateways = $conf->get('paymentGateways');

		reset( $gateways );
		foreach( $gateways as $g )
		{
			if( in_array($g, $activeGateways) )
				$active[] = $g;
		}
		return $active;
	}

	function getGateways()
	{
		$gateways = array();

		$folders = ntsLib::listSubfolders( $this->dir );
		reset( $folders );
		foreach( $folders as $f )
		{
//			if( $f == 'offline' )
//			{
//				continue;
//			}
			$gateways[] = $f;
		}

		return $gateways;
	}

	function getGatewayName( $gtw ){
		$return = $gtw;
		return $return;
		}

	function getGatewayCurrencies( $gtw ){
		$return = array();

		$file = $this->getGatewayFolder( $gtw ) . '/currencies.php';
		if( file_exists($file) ){
			require( $file );
			if( isset($currencies) )
				$return = $currencies;
			}
		else {
			reset( $this->allCurrencies );
			foreach( $this->allCurrencies as $c )
				$return[] = $c[0];
			}
		return $return;
		}

	function getActiveCurrencies(){
		$allowedCurrencies = array();
		$paymentGateways = $this->getActiveGateways();
		reset( $paymentGateways );
		$count = 0;
		foreach( $paymentGateways as $pg ){
			$thisCurrencies = $this->getGatewayCurrencies( $pg );
			// first one, init
			if( ! $count ){
				$allowedCurrencies = $thisCurrencies;
				}
			else {
				$allowedCurrencies = array_intersect( $allowedCurrencies, $thisCurrencies );
				}
			$count++;
			}
		$return = array_unique( $allowedCurrencies );
		return $return;
		}

	function getGatewayFolder( $gtw ){
		$folderName = $gtw;
		$fullFolderName = $this->dir . '/' . $folderName;
		return $fullFolderName;
		}

	function getGatewaySettings( $gtw ){
		$return = array();
		$conf =& ntsConf::getInstance();

		$confPrefix = 'payment-gateway-' . $gtw . '-';
		$allSettingsNames = $conf->getLoadedNames();
		reset( $allSettingsNames );
		foreach( $allSettingsNames as $confName ){
			if( substr($confName, 0, strlen($confPrefix)) == $confPrefix ){
				$shortName = substr($confName, strlen($confPrefix));
				$confValue = $conf->get( $confName );
				$return[ $shortName ] = $confValue;
				}
			}
		return $return;
		}

	// Singleton stuff
	static function &getInstance(){
		return ntsLib::singletonFunction( 'ntsPaymentGatewaysManager' );
		}
	}
?>