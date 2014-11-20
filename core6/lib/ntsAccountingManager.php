<?php
/*
CREATE APPOINTMENT
//	APP_1			-25 $
	CUSTOMER_APPS_{CID}	-25 $
	RECEIVABLE			+25 $

REJECT/CANCEL APPOINTMENT
//	APP_1			+25 $
	CUSTOMER_APPS_{CID}	+25 $
	RECEIVABLE			-25 $
	if funded then refund app

DISCOUNT APPOINTMENT
	APP_1		+5 $
	RECEIVABLE	-5 $

APPLY PROMOTION
	APP_1		+5 $
	RECEIVABLE			-5 $

RECEIVE TRANSACTION
	CUSTOMER_CASH_1	-50 $
	CUSTOMER_APPS_1	+40 $
	TAX				+10 $
	RECEIVABLE		-40 $
	CASH			+40 $

	PAY APP
		CUSTOMER_APPS_{CID}	-25 $
		APP_{AID}			+25 $

	PAY APP
		CUSTOMER_APPS_{CID}	-15 $
		APP_{AID2}			+15 $

BUY PACKAGE
	CUSTOMER_CASH_1	-50 $
	CUSTOMER_1		+5 SLOTS // balance
	TAX				+10 $
	COMPANY			-5 SLOTS
	CASH			+40 $

FUND APPOINTMENT FROM BALANCE
	CUSTOMER_1		-1 SLOTS
	COMPANY			+1 SLOTS
	APP_1			+25 $
	RECEIVABLE		-25 $

REFUND APPOUNTMENT
find journal of fund appointment and revert it

*/

class ntsAccountingManager 
{
//	var $_debug = TRUE;
	var $_debug = FALSE;
	var $_cache = array();
	var $track_payment = array();

	// Singleton stuff
	static function &getInstance()
	{
		return ntsLib::singletonFunction( 'ntsAccountingManager' );
	}

	function observe( $action_name, $object, $main_action_name, $params )
	{
		$created_at = time();
		if( isset($params['created_at']) )
			$created_at = $params['created_at'];
		$this->add( $action_name, $object, $params, $created_at );
	}

	function add( 
		$action_name,
		$object,
		$params = array(),
		$created_at = 0,
		$parent_id = 0
		)
	{
		$this->track_payment = array();

		$aam =& ntsAccountingAssetManager::getInstance();
		if( ! $created_at )
		{
			if( isset($params['created_at']) )
				$created_at = $params['created_at'];
			else
				$created_at = $object->getProp('created_at');
		}

		list( $object_class, $short_action_name ) = explode( '::', $action_name );

		switch( $action_name )
		{
		/* CREATE APPOINTMENT */
		/*
		//	APP_1				-25 $
			CUSTOMER_APPS_{CID}	-25 $
			RECEIVABLE			+25	$
		*/
			case 'appointment::create':
				$appointment_price = $object->getProp('price');
				if( ! $appointment_price )
					break;

				$customer_id = $object->getProp('customer_id');
				$journal_id = $parent_id ? $parent_id : $this->add_journal(
					array(
						'obj_class'		=> $object->getClassName(),
						'obj_id'		=> $object->getId(),
						'action'		=> 'create',
						'created_at'	=> $created_at,
						)
					);
				$this->add_posting(
					array(
						'journal_id'	=> $journal_id,
//						'account_type'	=> 'appointment',
//						'account_id'	=> $object->getId(),
						'account_type'	=> 'customer_apps',
						'account_id'	=> $customer_id,
						'asset_id'		=> 0,
						'asset_value'	=> -$appointment_price,
						)
					);
				$this->add_posting(
					array(
						'journal_id'	=> $journal_id,
						'account_type'	=> 'receivable',
						'account_id'	=> 0,
						'asset_id'		=> 0,
						'asset_value'	=> $appointment_price,
						)
					);
				break;

		/* ADD DISCOUNT */
		/*
			APP_1		+5 $
			RECEIVABLE	-5 $
		*/
			case 'appointment::discount':
				$discount = $params['discount'];

				$journal_id = $parent_id ? $parent_id : $this->add_journal(
					array(
						'obj_class'		=> $object->getClassName(),
						'obj_id'		=> $object->getId(),
						'action'		=> 'discount',
						'created_at'	=> $created_at
						)
					);
				$this->add_posting(
					array(
						'journal_id'	=> $journal_id,
						'account_type'	=> 'appointment',
						'account_id'	=> $object->getId(),
						'asset_id'		=> 0,
						'asset_value'	=> $discount,
						)
					);
				$this->add_posting(
					array(
						'journal_id'	=> $journal_id,
						'account_type'	=> 'receivable',
						'account_id'	=> 0,
						'asset_id'		=> 0,
						'asset_value'	=> -$discount,
						)
					);
				break;

		/* APPLY PROMOTION */
		/*
			APP_1		+5 $
			RECEIVABLE	-5 $
		*/
			case 'promotion::apply':
				$app = $params['appointment'];
				$coupon = isset($params['coupon']) ? $params['coupon'] : '';

				$ntspm =& ntsPaymentManager::getInstance();
				$amount = $ntspm->applyPromotion( $app, $object );

				$obj_class = $object->getClassName();
				$obj_id = $object->getId();
				if( $coupon )
				{
					// find this coupon
					$where = array(
						'code'	=> array('=', $coupon),
						);
					$coupon_obj = ntsObjectFactory::find_one( 'coupon', $where );
					if( $coupon_obj )
					{
						$obj_class = $coupon_obj->getClassName();
						$obj_id = $coupon_obj->getId();
					}
				}

				$journal_id = $parent_id ? $parent_id : $this->add_journal(
					array(
						'obj_class'		=> $obj_class,
						'obj_id'		=> $obj_id,
						'action'		=> 'apply',
						'created_at'	=> $created_at
						)
					);
				$this->add_posting(
					array(
						'journal_id'	=> $journal_id,
						'account_type'	=> 'appointment',
						'account_id'	=> $app->getId(),
						'asset_id'		=> 0,
						'asset_value'	=> -$amount,
						)
					);
				$this->add_posting(
					array(
						'journal_id'	=> $journal_id,
						'account_type'	=> 'receivable',
						'account_id'	=> 0,
						'asset_id'		=> 0,
						'asset_value'	=> $amount,
						)
					);
				break;

		/* REJECT OR CANCEL - REVERT CREATE */
		/*
			APP_1		+25	$
			RECEIVABLE	-25	$
			if funded then also refund app
		*/
			case 'appointment::reject':
			case 'appointment::cancel':
				$obj_id = $object->getId();

				$do_postings = array();
				$revert_postings = $this->get_journal_postings(
					array(
						'obj_class'	=> array( '=', $object->getClassName() ),
						'obj_id'	=> array( '=', $obj_id ),
						)
					);

				$customer_id = $object->getProp('customer_id');
				$revert_transactions = $this->get_journal_postings(
					array(
						'obj_class'		=> array('=', 'transaction'),
						'account_type'	=> array( '=', $object->getClassName() ),
						'account_id'	=> array( '=', $obj_id ),
						)
					);
				foreach( $revert_transactions as $p )
				{
					$revert_postings[] = array(
						'account_type'	=> $object->getClassName(),
						'account_id'	=> $obj_id,
						'asset_id'		=> $p['asset_id'],
						'asset_value'	=> $p['asset_value'],
						'expires_at'	=> $p['expires_at'],
						);
					$revert_postings[] = array(
						'account_type'	=> 'customer',
						'account_id'	=> $customer_id,
						'asset_id'		=> $p['asset_id'],
						'asset_value'	=> -$p['asset_value'],
						'expires_at'	=> $p['expires_at'],
						);
				}

				$revert_balance = $this->parse_balance( $revert_postings );
				reset( $revert_balance );
				foreach( $revert_balance as $account_key => $balance )
				{
					list( $account_type, $account_id ) = explode( ':', $account_key );
					reset( $balance );
					foreach( $balance as $asset_id => $asset_value )
					{
						if( $asset_value == 0 )
							continue;

						if( strpos($asset_id, '-') !== FALSE )
						{
							list( $asset_id, $expires_at ) = explode( '-', $asset_id );
						}
						else
						{
							$expires_at = 0;
							if( isset($params['expires_at']) )
							{
								$expires_at = $params['expires_at'];
							}
						}

						if( is_array($asset_value) )
							$asset_values = $asset_value;
						else
							$asset_values = array( $asset_value );
						foreach( $asset_values as $asset_value )
						{
							$do_postings[] = array(
								'account_type'	=> $account_type,
								'account_id'	=> $account_id,
								'asset_id'		=> $asset_id,
								'asset_value'	=> -$asset_value,
								'expires_at'	=> $expires_at,
								);
						}
					}
				}

				if( $do_postings )
				{
					$journal_id = $parent_id ? $parent_id : $this->add_journal(
						array(
							'obj_class'		=> $object->getClassName(),
							'obj_id'		=> $object->getId(),
							'action'		=> $short_action_name,
							'created_at'	=> $created_at
							)
						);
					foreach( $do_postings as $p )
					{
						$p['journal_id'] = $journal_id;
						$this->add_posting( $p );
					}
				}
				break;

			/*
			REFUND APPOUNTMENT
			find journal of fund appointment and revert it
			*/
			case 'appointment::refund':
				if( isset($params['back']) )
				{
					$asset_value = $params['back'];
					$asset_id = 0;
				}

				$postings = $this->get_journal_postings( 
					array(
						'obj_class'	=> array( '=', $object->getClassName() ),
						'obj_id'	=> array( '=', $object->getId() ),
						'action'	=> array( '=', 'fund' ),
						)
					);
				if( ! $postings )
				{
					return;
				}

				$journal_id = $parent_id ? $parent_id : $this->add_journal(
					array(
						'obj_class'		=> $object->getClassName(),
						'obj_id'		=> $object->getId(),
						'action'		=> $short_action_name,
						'created_at'	=> $created_at
						)
					);
				foreach( $postings as $p )
				{
					$this->add_posting(
						array(
							'journal_id'	=> $journal_id,
							'account_type'	=> $p['account_type'],
							'account_id'	=> $p['account_id'],
							'asset_id'		=> $p['asset_id'],
							'asset_value'	=> -$p['asset_value'],
							)
						);
				}
				break;

			case 'transaction::delete':
				/* find journal for this transaction */
				break;

		/* ADD INVOICE ITEM */
		/*
			CUSTOMER_APPS_1	-10 $
			RECEIVABLE		+10 $
		*/
			case 'invoice::add_item':
				$customer_id = $object->getCustomerId();
				if( ! $customer_id )
					return;
				$item_amount = $params['item']['amount'] * $params['item']['qty'];
				if( ! $item_amount )
					return;

				$journal_id = $parent_id ? $parent_id : $this->add_journal(
					array(
						'obj_class'		=> $object->getClassName(),
						'obj_id'		=> $object->getId(),
						'action'		=> 'add_item',
						'created_at'	=> $created_at
						)
					);
				$this->add_posting(
					array(
						'journal_id'	=> $journal_id,
						'account_type'	=> 'customer_apps',
						'account_id'	=> $customer_id,
						'asset_id'		=> 0,
						'asset_value'	=> -$item_amount,
						)
					);
				$this->add_posting(
					array(
						'journal_id'	=> $journal_id,
						'account_type'	=> 'receivable',
						'account_id'	=> 0,
						'asset_id'		=> 0,
						'asset_value'	=> $item_amount,
						)
					);
				break;

		/* DELETE INVOICE ITEM */
		/*
			CUSTOMER_APPS_1	+10 $
			RECEIVABLE		-10 $
		*/
			case 'invoice::delete_item':
				$customer_id = $object->getCustomerId();
				if( ! $customer_id )
					return;

				$item_amount = $params['item']['amount'] * $params['item']['qty'];
				if( ! $item_amount )
					return;

				$journal_id = $parent_id ? $parent_id : $this->add_journal(
					array(
						'obj_class'		=> $object->getClassName(),
						'obj_id'		=> $object->getId(),
						'action'		=> 'add_item',
						'created_at'	=> $created_at
						)
					);
				$this->add_posting(
					array(
						'journal_id'	=> $journal_id,
						'account_type'	=> 'customer_apps',
						'account_id'	=> $customer_id,
						'asset_id'		=> 0,
						'asset_value'	=> $item_amount,
						)
					);
				$this->add_posting(
					array(
						'journal_id'	=> $journal_id,
						'account_type'	=> 'receivable',
						'account_id'	=> 0,
						'asset_id'		=> 0,
						'asset_value'	=> +$item_amount,
						)
					);
				break;

		/* RECEIVE PAYMENT */
		/*
			CUSTOMER_CASH_1	-50 $
			CUSTOMER_APPS_1	+40 $
			TAX				+10 $
			RECEIVABLE		-40 $
			CASH			+40 $

			FUND APP
				CUSTOMER_APPS_1	-25 $
				APP_1			+25 $

			FUND APP
				CUSTOMER_APPS_1	-15 $
				APP_2			+15 $
		*/
			case 'transaction::create':
				$amount = $object->getProp('amount');
				if( ! $amount )
				{
					return;
				}

				$journal_id = $parent_id ? $parent_id : $this->add_journal(
					array(
						'obj_class'	=> $object->getClassName(),
						'obj_id'	=> $object->getId(),
						'action'	=> 'create',
						'created_at'	=> $created_at
						)
					);

				$invoice_found = FALSE;
				$invoice_id = $object->getProp( 'invoice_id' );
				if( $invoice_id )
				{
					$invoice = ntsObjectFactory::get('invoice');
					$invoice->setId( $invoice_id );
					if( ! $invoice->notFound() )
					{
						$invoice_found = TRUE;
					}
				}

				$gross_amount = $amount;

				if( $invoice_found )
				{
					$invoice_amount = $invoice->getTotalAmount();
					$invoice_items = $invoice->getItems();

				/* preload funds of this invoice items */
					$needed_amounts = array();
					reset( $invoice_items );
					foreach( $invoice_items as $item_array )
					{
						$this_cost = 0;
						if( isset($item_array['object']) )
						{
							$item_key = $item_array['object']->getClassName() . ':' . $item_array['object']->getId();
							if( method_exists($item_array['object'], 'getCost') )
								$this_cost = $item_array['object']->getBasePrice();
						}
						else
						{
							$item_key = 'invoiceitem' . ':' . $item_array['id'];
						}

						$needed_amounts[ $item_key ] = new ntsMoneyCalc;
						if( $this_cost )
							$needed_amounts[ $item_key ]->add( $this_cost );
					}

					$items_options = array_keys($needed_amounts);

					$items_funds = $this->get_postings_where( 
						array(
							'CONCAT(account_type, ":", account_id)'	=> array( 'IN', $items_options, FALSE, TRUE ),
							)
						);

					foreach( $items_funds as $if )
					{
						if( $if['asset_id'] != 0 )
							continue;

						$item_key = $if['account_type'] . ':' . $if['account_id'];
						if( ! isset($needed_amounts[$item_key]) )
						{
							$needed_amounts[$item_key] = new ntsMoneyCalc;
						}
						$needed_amounts[$item_key]->add( -$if['asset_value'] );
					}

					foreach( array_keys($needed_amounts) as $item_key )
					{
						$needed_amounts[ $item_key ] = $needed_amounts[ $item_key ]->result();
					}
				}

				if( $invoice_found && ($invoice_amount > 0) && $invoice_items )
				{
					$customer_id = $invoice->getCustomerId();

				/*
					CUSTOMER_CASH_1	-50$
				*/
					$this->add_posting(
						array(
							'journal_id'	=> $journal_id,
							'account_type'	=> 'customer_cash',
							'account_id'	=> $customer_id,
							'asset_id'		=> 0,
							'asset_value'	=> -$gross_amount,
							)
						);

					$tax_amount = $invoice->getTaxAmount();

					$remain_amount = $gross_amount;
					$used_amount = 0;

					reset( $invoice_items );
					foreach( $invoice_items as $item_array )
					{
					/*
						CUSTOMER_APPS_1	-25$
						APP_1			+25$
					*/
						if( isset($item_array['object']) )
						{
							$item = $item_array['object'];
						}
						else
						{
							$item = ntsObjectFactory::get( 'invoiceitem' );
							$item->setId( $item_array['id'], FALSE );
						}

						$action_name = $item->getClassName() . '::' . 'pay';
						$item_key = $item->getClassName() . ':' . $item->getId();

						$needed_amount = $item_array['unitCost'];
						if( isset($needed_amounts[$item_key]) && ($needed_amounts[$item_key] < $needed_amount) )
						{
							$needed_amount = $needed_amounts[$item_key];
						}

						if( $needed_amount <= 0 )
							continue;

						$use_amount = ($needed_amount < $remain_amount) ? $needed_amount : $remain_amount;

						$this->add(
							$action_name,
							$item,
							array(
								'asset_id' 		=> 0,
								'asset_value'	=> $use_amount
								),
							$created_at,
							$journal_id
							);

						$remain_amount = $remain_amount - $use_amount;
						$used_amount = $used_amount + $use_amount;
						if( $remain_amount <= 0 )
						{
							break;
						}
					}

				/*
					TAX: +5$
				*/
					$use_tax_amount = 0;
					if( $remain_amount && $tax_amount )
					{
						$use_tax_amount = ($tax_amount < $remain_amount) ? $tax_amount : $remain_amount;
						$this->add_posting(
							array(
								'journal_id'	=> $journal_id,
								'account_type'	=> 'tax',
								'account_id'	=> 0,
								'asset_id'		=> 0,
								'asset_value'	=> $use_tax_amount,
								)
							);
					}

				/*
					CUSTOMER_APPS_1: +40$
					RECEIVABLE:		-40$
					CASH: 			+40$
				*/
					if( $used_amount )
					{
						$this->add_posting(
							array(
								'journal_id'	=> $journal_id,
								'account_type'	=> 'customer_apps',
								'account_id'	=> $customer_id,
								'asset_id'		=> 0,
								'asset_value'	=> $used_amount,
								)
							);
						$this->add_posting(
							array(
								'journal_id'	=> $journal_id,
								'account_type'	=> 'receivable',
								'account_id'	=> 0,
								'asset_id'		=> 0,
								'asset_value'	=> -$used_amount,
								)
							);
						$this->add_posting(
							array(
								'journal_id'	=> $journal_id,
								'account_type'	=> 'cash',
								'account_id'	=> 0,
								'asset_id'		=> 0,
								'asset_value'	=> $used_amount,
								)
							);
					}

					$remain_amount = $remain_amount - $use_tax_amount;
				/*
					OVERPAID: CUSTOMER_APPS_1: +5$
				*/
					if( $remain_amount > 0 )
					{
						$this->add_posting(
							array(
								'journal_id'	=> $journal_id,
								'account_type'	=> 'customer_apps',
								'account_id'	=> $customer_id,
								'asset_id'		=> 0,
								'asset_value'	=> $remain_amount,
								)
							);
					}
				}
				else
				{
				/*
					RECEIVABLE	-40 $
					CASH		+40 $
				*/
					$this->add_posting(
						array(
							'journal_id'	=> $journal_id,
							'account_type'	=> 'receivable',
							'account_id'	=> 0,
							'asset_id'		=> 0,
							'asset_value'	=> -$gross_amount,
							)
						);
					$this->add_posting(
						array(
							'journal_id'	=> $journal_id,
							'account_type'	=> 'cash',
							'account_id'	=> 0,
							'asset_id'		=> 0,
							'asset_value'	=> $gross_amount,
							)
						);
				}
				break;

		/* ACTIVATE ORDER */
		/*
				COMPANY		-300 minutes
				CUSTOMER_1	+300 minutes
		*/
			case 'order::request':
				$pack_id = $object->getProp('pack_id');
				$pack = ntsObjectFactory::get( 'pack', $pack_id );

				$asset_id = $object->getProp( 'asset_id' );
				$asset_value = $object->getProp( 'asset_value' );
				if( ! ($asset_id && $asset_value) )
				{
					$asset_id = $pack->getProp( 'asset_id' );
					$asset_value = $pack->getProp( 'asset_value' );
				}

				$expires_at = 0;
				if( isset($params['expires_at']) )
				{
					$expires_at = $params['expires_at'];
				}
				else
				{
					$expires_in = $pack->getProp( 'expires_in' );
					if( $expires_in )
					{
						$t = new ntsTime();
						$t->setTimestamp( $created_at );
						$t->modify( '+' . $expires_in );
						$expires_at = $t->getTimestamp();
					}
				}

				$journal_id = $parent_id ? $parent_id : $this->add_journal(
					array(
						'obj_class'		=> $object->getClassName(),
						'obj_id'		=> $object->getId(),
						'action'		=> 'request',
						'created_at'	=> $created_at,
						)
					);

				$do_postings = array();

				$asset_values = array( $asset_value );
				$asset = $aam->get_asset_by_id( $asset_id );
				$service_type = $aam->get_service_type( $asset );
				if( $service_type == 'fixed' )
				{
					$asset_values = explode( '-', $asset['service'] );
				}

				$customer_id = $object->getProp( 'customer_id' );
				foreach( $asset_values as $asset_value )
				{
				/* COMPANY -300 minutes */
					$do_postings[] = array(
						'account_type'	=> 'company',
						'account_id'	=> 0,
						'asset_id'		=> $asset_id,
						'asset_value'	=> -$asset_value,
						);
				/* CUSTOMER +300 minutes */
					$do_postings[] = array(
						'account_type'	=> 'customer',
						'account_id'	=> $customer_id,
						'asset_id'		=> $asset_id,
						'asset_value'	=> $asset_value,
						);
				}

				foreach( $do_postings as $p )
				{
					$p['journal_id']	= $journal_id;
					$p['expires_at']	= $expires_at;
					$this->add_posting( $p );
				}
				break;

		/* FUND APPOINTMENT FROM BALANCE */
		/*
			CUSTOMER_1		-1 SLOTS
			APP_1			+25 $
			COMPANY			+1 SLOTS
			RECEIVABLE		-25 $

			if cash then
			CUSTOMER_1		-25 $
			APP_1			+25 $
		*/
			case 'appointment::fund':
			case 'invoiceitem::fund':
				$asset_id = $params['asset_id'];
				$asset_value = $params['asset_value'];
				if( strpos($asset_id, '-') !== FALSE )
				{
					list( $asset_id, $expires_at ) = explode( '-', $asset_id );
				}
				else
				{
					$expires_at = 0;
					if( isset($params['expires_at']) )
					{
						$expires_at = $params['expires_at'];
					}
				}

			// calculate asset money value
				$asset_money_value = $asset_value;
				if( $asset_id != 0 )
				{
					$asset_money_value = 0;
					$asset = $aam->get_asset_by_id( $asset_id );
					switch( $asset['type'] )
					{
						case 'amount':
							$asset_money_value = $asset_value;
							break;
					}
				}

				if( ! $asset_money_value )
				{
				// if no money payment, move money already paid to customer overpaid account
					$overpaid = 0;
					switch( $object->getClassName() )
					{
						case 'appointment':
							$asset_money_value = $object->getCost();
							break;
						default:
							$asset_money_value = 0;
							break;
					}

					$postings = $this->get_postings( $object->getClassName(), $object->getId() );

					foreach( $postings as $p )
					{
						if( $p['asset_id'] == 0 )
						{
							switch( $p['obj_class'] )
							{
								case 'transaction':
									if( $p['action'] == 'create' )
									{
										$overpaid += $p['asset_value'];
									}
									break;
								default:
									if( in_array($p['action'], array('create', 'discount', 'fund')) )
									{
										$asset_money_value += - $p['asset_value'];
									}
									break;
							}
						}
					}
				}

				if( $asset_money_value <= 0 )
				{
					return;
				}

				$journal_id = $parent_id ? $parent_id : $this->add_journal(
					array(
						'obj_class'		=> $object->getClassName(),
						'obj_id'		=> $object->getId(),
						'action'		=> 'fund',
						'created_at'	=> $created_at,
						)
					);

			/* CUSTOMER_1 -30 minutes */
				$customer_id = $object->getProp( 'customer_id' );
				$this->add_posting(
					array(
						'journal_id'	=> $journal_id,
						'account_type'	=> 'customer',
						'account_id'	=> $customer_id,
						'asset_id'		=> $asset_id,
						'asset_value'	=> -$asset_value,
						'expires_at'	=> $expires_at,
						)
					);

			/* APP +25$ */
				$this->add_posting(
					array(
						'journal_id'	=> $journal_id,
						'account_type'	=> $object->getClassName(),
						'account_id'	=> $object->getId(),
						'asset_id'		=> 0,
						'asset_value'	=> $asset_money_value,
						'expires_at'	=> 0,
						)
					);

				if( $asset_id != 0 )
				{
				/* COMPANY +30 minutes */
					$customer_id = $object->getProp( 'customer_id' );
					$this->add_posting(
						array(
							'journal_id'	=> $journal_id,
							'account_type'	=> 'company',
							'account_id'	=> 0,
							'asset_id'		=> $asset_id,
							'asset_value'	=> $asset_value,
							'expires_at'	=> $expires_at,
							)
						);

				/* RECEIVABLE -25$ */
					$this->add_posting(
						array(
							'journal_id'	=> $journal_id,
							'account_type'	=> 'receivable',
							'account_id'	=> 0,
							'asset_id'		=> 0,
							'asset_value'	=> -$asset_money_value,
							'expires_at'	=> 0,
							)
						);
				}
				break;

			case 'order::pay':
				$this->add(
					'order::request',
					$object,
					$params
					);
				break;

		/* PAY APPOINTMENT */
		/*
			CUSTOMER_APPS_1		-25 $
			APP_1				+25 $
		*/
			case 'appointment::pay':
			case 'invoiceitem::pay':
				$asset_id = $params['asset_id'];
				$asset_value = $params['asset_value'];
				if( strpos($asset_id, '-') !== FALSE )
				{
					list( $asset_id, $expires_at ) = explode( '-', $asset_id );
				}
				else
				{
					$expires_at = 0;
					if( isset($params['expires_at']) )
					{
						$expires_at = $params['expires_at'];
					}
				}

				$journal_id = $parent_id ? $parent_id : $this->add_journal(
					array(
						'obj_class'		=> $object->getClassName(),
						'obj_id'		=> $object->getId(),
						'action'		=> 'pay',
						'created_at'	=> $created_at,
						)
					);

			/* CUSTOMER_APPS -25 $ */
				$customer_id = $object->getProp( 'customer_id' );
				$this->add_posting(
					array(
						'journal_id'	=> $journal_id,
						'account_type'	=> 'customer_apps',
						'account_id'	=> $customer_id,
						'asset_id'		=> $asset_id,
						'asset_value'	=> -$asset_value,
						'expires_at'	=> $expires_at,
						)
					);

			/* APP +25 $ */
				$this->add_posting(
					array(
						'journal_id'	=> $journal_id,
						'account_type'	=> $object->getClassName(),
						'account_id'	=> $object->getId(),
						'asset_id'		=> $asset_id,
						'asset_value'	=> $asset_value,
						'expires_at'	=> $expires_at,
						)
					);
				break;

			default :
				$msg = "SKIP $action_name";
//				ntsView::addAnnounce( $msg, 'error' );
				break;
		}

		if( $this->track_payment )
		{
			$cm =& ntsCommandManager::getInstance();
			foreach( $this->track_payment as $tpi => $ttt )
			{
				list( $obj_class, $obj_id ) = explode( '::', $tpi );
				$obj = ntsObjectFactory::get( $obj_class );
				$obj->setId( $obj_id );
				$cm->runCommand( $obj, 'track_payment' );
			}
		}
	}

	function add_journal( $journal = array() )
	{
/*
		$journal = array(
			'obj_class'		=> 'appointment' | 'order' | 'resource' | 'customer'
			'obj_id'		=> $app_id | $order_id | $customer_id
			'action'		=> 'create' | 'pay'
			);
*/
		if( ! isset($journal['created_at']) )
			$journal['created_at'] = time();

		if( $this->_debug )
		{
			echo '<p>ACCOUNTING JOURNAL:<br>';
			_print_r2( $journal );
			$return = 1;
		}
		else
		{
			$ntsdb =& dbWrapper::getInstance();
			$return = $ntsdb->insert( 'accounting_journal', $journal );
		}
		return $return;
	}

	function add_posting( $posting = array() )
	{
/*
		$journal = array(
			'journal_id'	=> $journal_id,
			'account_type'	=> 'appointment' | 'order' | 'resource' | 'customer',
			'account_id'	=> $resource_id,
			'asset_id'		=> 'cash',
			'asset_value'	=> $appointment_price
			);
*/
		if( ! isset($posting['expires_at']) )
			$posting['expires_at'] = 0;

		// amount
		$mc = new ntsMoneyCalc($posting['asset_value']);
		$amount = $mc->result();
		$posting['asset_value'] = $amount;

		if( $this->_debug )
		{
			echo '<p>ACCOUNTING POSTING:<br>';
			_print_r2( $posting );
			$return = 1;
		}
		else
		{
			$ntsdb =& dbWrapper::getInstance();
			$return = $ntsdb->insert( 'accounting_posting', $posting );
		}

		$track = array(
			'appointment', 'order'
			);
		if( in_array($posting['account_type'], $track) )
		{
			$key = $posting['account_type'] . '::' . $posting['account_id'];
			$this->track_payment[ $key ] = 1;
		}
		return $return;
	}

	/* returns 
		array(
			array(
				[asset_id] => 0
				[asset_value] => -45
				[obj_class] => appointment
				[obj_id] => 234
				[action] => create
				[created_at] => 1385676028
				)
			)
	*/
	function get_postings( $account_type, $account_id )
	{
		$key = $account_type . '_' . $account_id;
		if( ! isset($this->_cache[$key]) )
		{
			$this->_cache[$key] = array();
			$this->load_postings( $account_type, $account_id );
		}
		return $this->_cache[$key];
	}

	function reset_accounting_postings( $account_type, $account_id )
	{
		$key = $account_type . '_' . $account_id;
		if( isset($this->_cache[$key]) )
		{
			unset( $this->_cache[$key] );
		}
	}

	function load_postings( $account_type, $account_id )
	{
		$return = array();
		$ntsdb =& dbWrapper::getInstance();

		if( ! $account_id )
			return;
		$account_ids = is_array($account_id) ? $account_id : array( $account_id );

	/* init cache */
		reset( $account_ids );
		foreach( $account_ids as $id )
		{
			$key = $account_type . '_' . $id;
			$this->_cache[$key] = array();
		}

		$where = array(
			'account_type'	=> array( '=', $account_type ),
			'account_id'	=> array( 'IN', $account_ids ),
			);

		$join = array(
			array(
				'accounting_journal',
				array(
					'accounting_journal.id'	=> array( '=', 'accounting_posting.journal_id', TRUE )
					)
				)
			);

		$return = $ntsdb->get_select( 
			array(
				'account_id',
				'asset_id',
				'asset_value',
				'obj_class',
				'obj_id',
				'action',
				'journal_id',
				'{PRFX}accounting_journal.created_at',
				'expires_at'
				),
			'accounting_posting',
			$where,
			'ORDER BY {PRFX}accounting_journal.created_at DESC, {PRFX}accounting_posting.id DESC',
			$join
			);

		for( $ii = 0; $ii < count($return); $ii++ )
		{
			$key = $account_type . '_' . $return[$ii]['account_id'];
			if( ! isset($this->_cache[$key]) )
			{
				$this->_cache[$key] = array();
			}
			$this->_cache[$key][] = $return[$ii];
		}
	}

	function get_postings_where( $where )
	{
		$return = array();
		$ntsdb =& dbWrapper::getInstance();

		$return = $ntsdb->get_select( 
			array(
				'account_type',
				'account_id',
				'asset_id',
				'asset_value',
				),
			'accounting_posting',
			$where,
			'ORDER BY {PRFX}accounting_posting.id DESC'
			);

		return $return;
	}

	/* returns 
		array(
			array(
				[asset_id] => 0
				[asset_value] => -45
				[account_type] => appointment
				[account_id] => 234
				)
			)

		$where = array(
			'obj_class'	=> array( '=', $obj_class ),
			'obj_id'	=> array( '=', $obj_id ),
			'action'	=> array( '=', $action ),
			);

	*/
	function get_journal_postings( $where = array() )
	{
		$return = array();
		$ntsdb =& dbWrapper::getInstance();

		$join = array(
			array(
				'accounting_journal',
				array(
					'accounting_journal.id'	=> array( '=', 'accounting_posting.journal_id', TRUE )
					)
				),
			);

		$return = $ntsdb->get_select( 
			array(
				'asset_id',
				'asset_value',
				'account_type',
				'account_id',
				'expires_at'
				),
			'accounting_posting',
			$where,
			'ORDER BY {PRFX}accounting_journal.created_at DESC, {PRFX}accounting_posting.id DESC',
			$join
			);
		return $return;
	}

	private function _sort_balance( $a, $b )
	{
	/* sort by time left before expiration */
		list($a1,$a2) = explode('-',$a);
		list($b1,$b2) = explode('-',$b);
		if( (! $a2) OR (! $b2) )
		{
			$return = ntsLib::numberCompare($b2, $a2);
		}
		else
		{
			$return = ntsLib::numberCompare($a2, $b2);
		}
		return $return;
	}


	/* returns 
		array(
			asset_id	=> asset_value,
			)
		for example
		array(
			0	=> -45,
			1	=> 300
			)
	*/
	function get_balance( $account_type, $account_id )
	{
		$where = array(
			'account_type'	=> array( '=', $account_type ),
			'account_id'	=> array( '=', $account_id ),
			);
		$postings = $this->get_journal_postings( $where );
		$return = $this->parse_balance( $postings );
		return $return;
	}

	function parse_balance( $postings )
	{
		$return = array();
		$aam =& ntsAccountingAssetManager::getInstance();

		$use_akey = FALSE;
		$akeys = array();
		reset( $postings );
		foreach( $postings as $p )
		{
			$akey = $p['account_type'] . ':' . $p['account_id'];
			$akeys[ $akey ] = 1;
		}
		if( count($akeys) > 1 )
			$use_akey = TRUE;

		reset( $postings );
		foreach( $postings as $p )
		{
			$asset = $aam->get_asset_by_id( $p['asset_id'] );

			$pkey = $p['asset_id'] . '-' . $p['expires_at'];
			if( $use_akey )
			{
				$akey = $p['account_type'] . ':' . $p['account_id'];
				if( ! isset($return[$akey]) )
					$return[$akey] = array();
			}

			$service_type = $aam->get_service_type( $asset );
			if( $service_type == 'fixed' )
			{
				if( $use_akey )
				{
					if( ! isset($return[$akey][$pkey]) )
						$return[$akey][$pkey] = array();

					if( in_array(-$p['asset_value'], $return[$akey][$pkey]) )
					{
						$return[$akey][$pkey] = Hc_lib::remove_from_array( $return[$akey][$pkey], -$p['asset_value'], FALSE );
					}
					else
					{
						$return[$akey][$pkey][] = $p['asset_value'];
					}
				}
				else
				{
					if( ! isset($return[$pkey]) )
						$return[$pkey] = array();

					if( in_array(-$p['asset_value'], $return[$pkey]) )
					{
						$return[$pkey] = Hc_lib::remove_from_array( $return[$pkey], -$p['asset_value'], FALSE );
					}
					else
					{
						$return[$pkey][] = $p['asset_value'];
					}
				}
			}
			else
			{
				if( isset($asset['type']) )
				{
					if( ($asset['type'] == 'unlimited') && ($p['asset_value'] < 0) )
					{
						continue;
					}
				}

				if( $use_akey )
				{
					if( ! isset($return[$akey][$pkey]) )
						$return[$akey][$pkey] = new ntsMoneyCalc;

					$return[$akey][$pkey]->add( $p['asset_value'] );
				}
				else
				{
					if( ! isset($return[$pkey]) )
						$return[$pkey] = new ntsMoneyCalc;

					$return[$pkey]->add( $p['asset_value'] );
				}
			}
		}

		if( $use_akey )
		{
			$akeys = array_keys($return);
			for( $jj = 0; $jj < count($akeys); $jj++ )
			{
				$keys = array_keys($return[ $akeys[$jj] ]);
				for( $ii = 0; $ii < count($keys); $ii++ )
				{
					if( ! is_array($return[ $akeys[$jj] ][ $keys[$ii] ]) )
					{
						$return[ $akeys[$jj] ][ $keys[$ii] ] = $return[ $akeys[$jj] ][ $keys[$ii] ]->result();
					}
				}
				uksort( $return[ $akeys[$jj] ], array($this, '_sort_balance') );
			}
		}
		else
		{
			$keys = array_keys($return);
			for( $ii = 0; $ii < count($keys); $ii++ )
			{
				if( ! is_array($return[ $keys[$ii] ]) )
				{
					$return[ $keys[$ii] ] = $return[ $keys[$ii] ]->result();
				}
			}
			uksort( $return, array($this, '_sort_balance') );
		}
		return $return;
	}

	function minus_balance( $customer_balance, $asset_id, $asset_value )
	{
		$aam =& ntsAccountingAssetManager::getInstance();
		$type = '';
		if( $asset_id )
		{
			$short_asset_id = $asset_id;
			if( strpos($asset_id, '-') !== FALSE )
			{
				list( $short_asset_id, $asset_expires ) = explode( '-', $asset_id );
			}
			$asset = $aam->get_asset_by_id( $short_asset_id );
			$type = $asset['type'];
		}

		$service_type = $aam->get_service_type( $asset );
		switch( $service_type )
		{
			case 'fixed':
				$customer_balance[$asset_id] = Hc_lib::remove_from_array( $customer_balance[$asset_id], $asset_value, FALSE );
				break;

			default:
				if( $type != 'unlimited' )
				{
					$customer_balance[$asset_id] = $customer_balance[$asset_id] - $asset_value;
				}
				break;
		}
		return $customer_balance;
	}

	function balance_cover( $customer_balance, $app )
	{
		$aam =& ntsAccountingAssetManager::getInstance();
		$return = array();
		$now = time();

		reset( $customer_balance );
		foreach( $customer_balance as $asset_key => $asset_value )
		{
			list( $asset_id, $asset_expires ) = explode( '-', $asset_key );
			if( $asset_expires && ($asset_expires < $now) )
			{
				continue;
			}

			if( $asset_id )
			{
				$return_value = 0;
				$asset = $aam->get_asset_by_id( $asset_id );

				$asset_fits = $this->asset_fits( $asset, $app );
				if( ! $asset_fits )
				{
					continue;
				}

				$service_type = $aam->get_service_type( $asset );
				switch( $service_type )
				{
					case 'fixed':
						if( ! is_array($asset_value) )
							$asset_value = array($asset_value);

						$service_id = $app->getProp('service_id');
						if( in_array($service_id, $asset_value) )
						{
							$return_value = $service_id;
						}
						else
						{
							$return_value = 0;
						}
						break;

					default:
						switch( $asset['type'] )
						{
							case 'unlimited':
							case 'qty':
								if( $asset_value >= 1 )
								{
									$return_value = 1;
								}
								break;
							case 'amount':
								$due_amount = $app->getDue();
								if( ($due_amount > 0) && ($asset_value > $due_amount) )
								{
									$return_value = $due_amount;
								}
								break;
							case 'duration':
								$app_duration = $app->getProp('duration');
								if( $asset_value >= $app_duration )
								{
									$return_value = $app_duration;
								}
								break;
						}
						break;
				}
				if( $return_value )
					$return[ $asset_key ] = $return_value;
			}
		/* cash */
			else
			{
				$due_amount = $app->getDue();
				if( ($due_amount > 0) && ($asset_value > $due_amount) )
				{
					$return[ $asset_key ] = $due_amount;
				}
			}
		}

		uksort( $return, array($this, '_sort_balance') );
		return $return;
	}

	function journal_label( $e = array() )
	{
		$key = $e['obj_class'] . '::' . $e['action'];
		$labels = array(
			'appointment::create'		=> M('New Appointment'),
			'appointment::cancel'		=> M('Appointment') . ': ' . M('Cancel'),
			'appointment::reject'		=> M('Appointment') . ': ' . M('Reject'),
			'appointment::discount'		=> M('Discount'),
			'appointment::refund'		=> M('Appointment') . ': ' . M('Refund'),
			'appointment::fund'			=> M('Pay By Balance'),
			'transaction::create'		=> join( ': ', array(M('Payment'), M('Add')) ),
			'order::request'			=> join( ': ', array(M('Package'), M('Add')) ),
			);

		$return = $key;
		if( isset($labels[$key]) )
		{
			$return = $labels[$key];
		}
		else
		{
			switch( $key )
			{
				case 'promotion::apply':
					$obj = ntsObjectFactory::get( 'promotion' );
					$obj->setId( $e['obj_id'] );
					$return = M('Promotion') . ': ' . ntsView::objectTitle( $obj );
					break;
				case 'coupon::apply':
					$obj = ntsObjectFactory::get( 'coupon' );
					$obj->setId( $e['obj_id'] );
					$return = M('Coupon') . ': ' . ntsView::objectTitle( $obj );
					break;
			}
		}
		return $return;
	}

	function asset_fits( $asset, $app )
	{
		$return = TRUE;

		$t = new ntsTime;

	/* location */
		if( isset($asset['location']) )
		{
			if( ! is_array($asset['location']) )
				$asset['location'] = array( $asset['location'] );
			if( ! in_array($app->getProp('location_id'), $asset['location']) )
				$return = FALSE;
		}
		if( ! $return )
			return $return;

	/* resource */
		if( isset($asset['resource']) )
		{
			if( ! is_array($asset['resource']) )
				$asset['resource'] = array( $asset['resource'] );
			if( ! in_array($app->getProp('resource_id'), $asset['resource']) )
				$return = FALSE;
		}
		if( ! $return )
			return $return;

	/* service */
		if( isset($asset['service']) )
		{
			if( ! is_array($asset['service']) )
			{
				if( strpos($asset['service'], '-') !== FALSE )
				{
					$asset['service'] = explode( '-', $asset['service'] );
				}
			}
			if( ! is_array($asset['service']) )
				$asset['service'] = array( $asset['service'] );
			if( ! in_array($app->getProp('service_id'), $asset['service']) )
				$return = FALSE;
		}
		if( ! $return )
			return $return;

	/* time */
		if( isset($asset['time']) )
		{
			$duration = $app->getProp('duration');
			$t->setTimestamp( $app->getProp('starts_at') );
			$time_of_day = $t->getTimeOfDay();

			if( 
				($time_of_day < $asset['time'][0]) OR
				( ($time_of_day + $duration) > $asset['time'][1] )
				)
			{
				$return = FALSE;
			}
		}
		if( ! $return )
			return $return;

	/* date */
		if( isset($asset['date']) )
		{
			if( ! is_array($asset['date']) )
				$asset['date'] = array( $asset['date'] );

			$t->setTimestamp( $app->getProp('starts_at') );
			$this_date = $t->formatDate_Db();

			if( isset($asset['date']['from']) )
			{
				if( 
					( $this_date < $asset['date']['from'] ) OR
					( $this_date > $asset['date']['to'] )
					)
				{
					$return = FALSE;
				}
			}
			else
			{
				if( ! in_array($this_date, $asset['date']) )
				{
					$return = FALSE;
				}
			}
		}
		if( ! $return )
			return $return;

	/* weekday */
		if( isset($asset['weekday']) )
		{
			if( ! is_array($asset['weekday']) )
				$asset['weekday'] = array( $asset['weekday'] );

			$t->setTimestamp( $app->getProp('starts_at') );
			$this_weekday = $t->getWeekday();
			if( ! in_array($this_weekday, $asset['weekday']) )
			{
				$return = FALSE;
			}
		}
		if( ! $return )
			return $return;

		return $return;
	}
}

class ntsAccountingAssetManager
{
	var $_assets = array();

	function __construct()
	{
		$ntsdb =& dbWrapper::getInstance();
		$assets = $ntsdb->get_select( '*', 'accounting_assets' );
		$this->_assets = array();
		foreach( $assets as $a )
		{
			$asset = unserialize( $a['asset'] );
			$asset = $this->_prepare_asset( $asset );
			$a['asset'] = serialize( $asset );
			$this->_assets[ $a['asset'] ] = $a['id'];
		}
	}

	public function format_asset(
		$asset_id = 0,
		$asset_value = 0,
		$html = FALSE,
		$show_sign = TRUE,
		$spending = FALSE
		)
	{
		$return = '';
		$asset_expires = 0;
		if( strpos($asset_id, '-') !== FALSE )
		{
			list( $asset_id, $asset_expires ) = explode( '-', $asset_id );
		}

		if( $asset_value > 0 )
		{
			$plus = TRUE;
		}
		else
		{
			$plus = FALSE;
			$asset_value = - $asset_value;
		}

		if( $asset_id == 0 )
		{
			$return = ntsCurrency::formatPrice( $asset_value );
		}
		else
		{
			$asset = $this->get_asset_by_id( $asset_id );
			$service_type = $this->get_service_type( $asset );

			switch( $service_type )
			{
				case 'fixed':
					if( is_array($asset_value) )
						$format_asset_value = count($asset_value);
					else
						$format_asset_value = 1;

					$return = $format_asset_value . ' ';
					$return .= ($format_asset_value > 1) ? M('Appointments') : M('Appointment');
					break;
				default:
					switch( $asset['type'] )
					{
						case 'unlimited':
							if( $plus && (! $spending) )
							{
								$return = M('Unlimited');
//								$show_sign = FALSE;
							}
							else
							{
								$asset_value = (int) $asset_value;
								$return = '';
								$return .= $asset_value . ' ';
								$return .= ($asset_value > 1) ? M('Appointments') : M('Appointment');
								$return .= ' [' . M('Unlimited') . ']';
							}
							break;
						case 'qty':
							$asset_value = (int) $asset_value;
							$return = $asset_value . ' ';
							$return .= ($asset_value > 1) ? M('Appointments') : M('Appointment');
							break;
						case 'amount':
							$return = ntsCurrency::formatPriceNumber( $asset_value );
							$return = $return . ' ' . M('Credits');
							break;
						case 'duration':
							$return = ntsTime::formatPeriod( $asset_value );
							break;
					}
					break;
			}
		}

		if( $show_sign )
		{
			if( $plus )
				$return = '+' . $return;
			else
				$return = '-' . $return;
		}

		if( $html )
		{
			if( $plus )
				$return = '<span class="text-success">' . $return . '</span>';
			else
				$return = '<span class="text-danger">' . $return . '</span>';
		}
		return $return;
	}

	private function _prepare_asset( $asset )
	{
	/* final keys - these are allowed keys */
		$final_keys = array(
			'location',
			'resource',
			'service',
			'date',
			'weekday',
			'time',
			'type'
			);

	/* filter keys */
		$filter = array( 'location', 'resource', 'service' );
		reset( $filter );
		foreach( $filter as $f )
		{
			$f_id = $f . '_id';
			if( isset($asset[$f_id]) && (! isset($asset[$f])) )
				$asset[$f] = $asset[$f_id];

			if( isset($asset[$f]) )
			{
				if( ! is_array($asset[$f]) )
				{
					$v = explode(',', $asset[$f]);
					if( count($v) == 1 )
						$v = $v[0];
					elseif( count($v) == 0 )
						$v = $v;
					$asset[$f] = $v;
				}
			}
		}

	/* define type if not yet defined */
		if( ! isset($asset['type']) )
		{
			$asset['type'] = 'unlimited'; 
			if( 
				(isset($asset['service_id']) && (! is_array($asset['service_id'])) && (strpos($asset['service_id'], '-') !== FALSE)) OR
				(isset($asset['service']) && (! is_array($asset['service'])) && (strpos($asset['service'], '-') !== FALSE))
				)
			{
				$asset['type'] = 'fixed'; 
			}
			else
			{
				$types = array('qty', 'duration', 'amount');
				reset( $types );
				foreach( $types as $t )
				{
					if( isset($asset[$t]) && $asset[$t] )
					{
						$asset['type'] = $t; 
					}
				}
			}
		}

	/* unset those that are not needed */
		$keys = array_keys($asset);
		foreach( $keys as $k )
		{
			if( ! in_array($k, $final_keys) )
			{
				unset( $asset[$k] );
			}
		}

	/* for every asset sort values if array */
		$keys = array_keys( $asset );
		foreach( $keys as $k )
		{
			if( ! $asset[$k] )
			{
				unset($asset[$k]);
			}
			elseif( is_array($asset[$k]) )
			{
				if( isset($asset[$k][0]) )
					sort($asset[$k]);
				else
					ksort( $asset[$k] );
			}
		}

	/* sort keys */
		ksort( $asset );

		return $asset;
	}

	public function get_asset_by_id( $asset_id )
	{
		$return = array();
		if( $asset_id == 0 )
			return $return;
		$asset = '';
		reset( $this->_assets );
		foreach( $this->_assets as $asset_string => $id )
		{
			if( $id == $asset_id )
			{
				$asset = $asset_string;
				break;
			}
		}
		if( ! $asset )
		{
//			echo "asset_id = $asset_id is not registered<br>";
			return $return;
		}
		$return = unserialize( $asset );
		return $return;
	}

	public function get_asset_id( $asset )
	{
		$return = 0;
		$asset = $this->_prepare_asset( $asset );

		if( $asset )
		{
			$asset_string = serialize( $asset );
			if( ! isset($this->_assets[$asset_string]) )
			{
				$ntsdb =& dbWrapper::getInstance();
				$new_asset_id = $ntsdb->insert(
					'accounting_assets',
					array(
						'asset'	=> $asset_string
						)
					);
				if( ! $new_asset_id )
				{
					echo 'database error: ' . $ntsdb->getError();
					exit;
				}
				$this->_assets[$asset_string] = $new_asset_id;
			}
			$return = $this->_assets[$asset_string];
		}
		return $return;
	}

	function get_service_type( $asset )
	{
		$return = 'one';
		if( ! is_array($asset) )
			$asset = $this->get_asset_by_id( $asset );

		$service = '';
		if( isset($asset['service']) )
		{
			$service = $asset['service'];
		}
		elseif( isset($asset['service_id']) )
		{
			$service = $asset['service_id'];
		}

		if( (! is_array($service)) && ( strpos($service, '-') !== FALSE ) )
		{
			$return = 'fixed';
		}
		return $return;
	}

	function asset_view(
		$asset_id,
		$html = FALSE,
		$just = array(),
		$skip = array(),
		$force_value = NULL
		)
	{
		$return = array();
		$asset = $this->get_asset_by_id( $asset_id );
		if( $just )
		{
			$keys = array_keys($asset);
			foreach( $keys as $k )
			{
				if( ! in_array($k, $just) )
				{
					unset( $asset[$k] );
				}
			}
		}

		if( $skip )
		{
			foreach( $skip as $k )
			{
				unset( $asset[$k] );
			}
		}

		if( isset($asset['location']) )
		{
			$value = $asset['location'];
			$ids = is_array($value) ? $value : array($value);

			$view = array();
			$view[] = $html ? '<i class="fa fa-home"></i>' : M('Location');

			$view2 = array();
			foreach( $ids as $oid )
			{
				$obj = ntsObjectFactory::get('location', $oid);
				$view2[] = ntsView::objectTitle( $obj );
			}
			$view[] = $view2;
			$return['location'] = $view;
		}

		if( isset($asset['resource']) )
		{
			$value = $asset['resource'];
			$ids = is_array($value) ? $value : array($value);

			$view = array();
			$view[] = $html ? '<i class="fa fa-hand-o-up"></i>' : M('Bookable Resource');

			$view2 = array();
			foreach( $ids as $oid )
			{
				$obj = ntsObjectFactory::get('resource', $oid);
				$view2[] = ntsView::objectTitle( $obj );
			}
			$view[] = $view2;
			$return['resource'] = $view;
		}

		if( isset($asset['service']) )
		{
			$view = array();
			$view[] = $html ? '<i class="fa fa-tags"></i>' : M('Service');

			$view2 = array();
			$service_type = $this->get_service_type( $asset );
			if( $service_type == 'fixed' )
			{
				if( $force_value )
					$service = $force_value;
				else
					$service = isset($asset['service']) ? $asset['service'] : '';

				if( $service )
				{
					$services = array();
					if( is_array($service) )
						$sids = $service;
					else
						$sids = explode( '-', $service );
					foreach( $sids as $sid )
					{
						if( ! isset($services[$sid]) )
							$services[$sid] = 0;
						$services[$sid]++;
					}
					reset( $services );
					foreach( $services as $sid => $count )
					{
						$service = ntsObjectFactory::get( 'service' );
						$service->setId( $sid );
						$view2[] = $count . ' x ' . ntsView::objectTitle( $service );
					}
				}
				else
				{
					$view2[] = ' - ' . M('Fixed Services') . ' - ';
				}
			}
			else
			{
				$value = $asset['service'];
				$ids = is_array($value) ? $value : array($value);

				if( in_array(0, $ids) )
				{
					$view2[] = ' - ' . M('Any') . ' - ';
				}
				else
				{
					foreach( $ids as $oid )
					{
						$obj = ntsObjectFactory::get('service', $oid);
						$view2[] = ntsView::objectTitle( $obj );
					}
				}
			}
			$view[] = $view2;
			$return['service'] = $view;
		}

		if( isset($asset['date']) )
		{
			$view = array();
			$view[] = $html ? '<i class="fa fa-calendar"></i>' : M('Dates');

			$t = new ntsTime();
			if( isset($asset['date']['from']) )
			{
				$t->setDateDb( $asset['date']['from'] );
				$fromView = $t->formatDate();
				$t->setDateDb( $asset['date']['to'] );
				$toView = $t->formatDate();
				$view2 = array( join( ' - ', array($fromView, $toView) ) );
			}
			else
			{
				$view2 = array();
				foreach( $asset['date'] as $date )
				{
					$t->setDateDb( $date );
					$dateView = $t->formatDate();
					$view2[] = $dateView;
				}
			}

			$view[] = $view2;
			$return['date'] = $view;
		}

		if( isset($asset['weekday']) )
		{
			$view = array();
			$view[] = $html ? '<i class="fa fa-calendar"></i>' : M('Weekday');

			$view2 = array();
			reset( $asset['weekday'] );
			foreach( $asset['weekday'] as $wdi )
			{
				$view2[] = ntsTime::weekdayLabelShort($wdi);
			}
			$view2 = join(', ', $view2);

			if( isset($return['date']) )
				$return['date'][1][] = $view2;
			else
			{
				$view[] = array( $view2 );
				$return['weekday'] = $view;
			}
		}

		if( isset($asset['time']) )
		{
			$view = array();
			$view[] = $html ? '<i class="fa fa-clock-o"></i>' : M('Time');

			$t = new ntsTime();
			$view2 = array( join( ' - ', array($t->formatTimeOfDay($asset['time'][0]), $t->formatTimeOfDay($asset['time'][1])) ) );

			$view[] = $view2;
			$return['time'] = $view;
		}

		return $return;
	}

/* form helpers */
	public function asset_form_remove_validation( $req )
	{
		$removeValidation = array();

		if( $req->getParam( 'time_all' ) )
		{
			$removeValidation[] = 'from_time';
			$removeValidation[] = 'to_time';
		}
		if( $req->getParam( 'date_all' ) )
		{
			$removeValidation[] = 'from_date';
			$removeValidation[] = 'to_date';
			$removeValidation[] = 'fixed_date';
		}
		else
		{
			$dateType = $req->getParam( 'date_type' );
			switch( $dateType )
			{
				case 'fixed':
					$removeValidation[] = 'from_date';
					$removeValidation[] = 'to_date';
					break;
				case 'range':
					$removeValidation[] = 'fixed_date';
					break;
			}
		}

		$serviceType = $req->getParam( 'service_type' );
		$packType = $req->getParam( 'pack_type' );
		switch( $serviceType ){
			case 'fixed':
				$removeValidation[] = 'qty';
				$removeValidation[] = 'duration';
				$removeValidation[] = 'amount';
				$removeValidation[] = 'service_id';
				break;
			case 'one':
				$removeValidation[] = 'fixed_service_id';
				switch( $packType ){
					case 'unlimited':
						$removeValidation[] = 'qty';
						$removeValidation[] = 'duration';
						$removeValidation[] = 'amount';
						break;
					case 'qty':
						$removeValidation[] = 'duration';
						$removeValidation[] = 'amount';
						break;
					case 'duration':
						$removeValidation[] = 'qty';
						$removeValidation[] = 'amount';
						break;
					case 'amount':
						$removeValidation[] = 'qty';
						$removeValidation[] = 'duration';
						break;
					}

				$serviceAll = $req->getParam( 'service_id_all' );
				if( $serviceAll ){
					$removeValidation[] = 'service_id';
					}
				break;
			}
		$notForSale = $req->getParam( 'notForSale' );
		if( $notForSale )
		{
			$removeValidation[] = 'price';
		}

		return $removeValidation;
	}

	public function asset_form_grab( $formValues )
	{
		$asset = array();

		$serviceType = $formValues['service_type'];
		$packType = $formValues['pack_type'];
		$serviceAll = $formValues['service_id_all'];

	/* build asset */
		$asset = array(
			);

		$grab = array('weekday');
		foreach( $grab as $gr )
		{
			if( isset($formValues[$gr]) && (! in_array(-1, $formValues[$gr])) )
			{
				$asset[$gr] = $formValues[$gr];
			}
		}
		if( (! isset($formValues['time_all'])) OR (! $formValues['time_all']) )
		{
			$asset['time'] = array($formValues['from_time'], $formValues['to_time']);
		}

		if( (! isset($formValues['date_all'])) OR (! $formValues['date_all']) )
		{
			$dateType = $formValues['date_type'];
			switch( $dateType )
			{
				case 'fixed':
					$asset['date'] = $formValues['fixed_date'];
					$asset['weekday'] = '';
					break;
				case 'range':
					$asset['date'] = array(
						'from'	=> $formValues['from_date'],
						'to'	=> $formValues['to_date'],
						);
					break;
			}
		}

		switch( $serviceType ){
			case 'fixed':
				$formValues['service_id'] = $formValues['fixed_service_id'];
				break;

			case 'one':
				if( ! is_array($formValues['service_id']) )
					$formValues['service_id'] = array($formValues['service_id']);
				if( $serviceAll )
					$formValues['service_id'] = array(0);
				$formValues['service_id'] = join(',', $formValues['service_id'] );
				break;
			}

		if( isset($formValues['neverExpires']) && $formValues['neverExpires'] )
			$formValues['expires_in'] = 0;

		if( isset($formValues['service_type']) )
			unset($formValues['service_type']);
		if( isset($formValues['fixed_service_id']) )
			unset($formValues['fixed_service_id']);

		$add_fields = array( 'location_id', 'resource_id', 'service_id', 'expires_in' );
		foreach( $add_fields as $af )
		{
			if( isset($formValues[$af]) )
			{
				$asset[$af] = $formValues[$af];
			}
		}
		if( (! isset($asset['service'])) && isset($asset['service_id']) )
		{
			$asset['service'] = $asset['service_id'];
		}

		$asset['type'] = $packType;
		return $asset;
	}

	// Singleton stuff
	static function &getInstance()
	{
		return ntsLib::singletonFunction( 'ntsAccountingAssetManager' );
	}
}
?>