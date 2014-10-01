<?php
class ntsAppointment extends ntsObject {
	private $_invoices = NULL;

	function __construct(){
		parent::ntsObject( 'appointment' );
		}

	/* returns 1 if ok, 0 if no availability, array if conflicts */
	function get_availability_status()
	{
		$return = 1;
		$completed = $this->getProp('completed');
		if( $completed )
		{
			return $return;
		}

		if( ntsLib::hasVar( 'admin::tm2' ) )
		{
			$tm2 = ntsLib::getVar( 'admin::tm2' );
		}
		else
		{
			$tm2 = new haTimeManager2();
		}

		$saveLids = $tm2->locationIds;
		$saveRids = $tm2->resourceIds;
		$saveSids = $tm2->serviceIds;

		$tm2->setLocation( $this->getProp('location_id') );
		$tm2->setResource( $this->getProp('resource_id') );
		$tm2->setService( $this->getProp('service_id') );
		$tm2->setSkip( array($this->getId()) );

		$starts_at = $this->getProp('starts_at');
		$times = $tm2->getAllTime( $starts_at, $starts_at );

		if( ! isset($times[$starts_at]) )
		{
			$return = 0;
			$slot = $tm2->makeSlotFromAppointment( $this );
			$remain_seats = $tm2->checkSlot( $starts_at, $slot, TRUE );
			if( ! $remain_seats )
			{
				$errors = $tm2->getSlotErrors();
				if( $errors )
				{
					$return = $errors;
				}
			}
		}

		$tm2->setLocation( $saveLids );
		$tm2->setResource( $saveRids );
		$tm2->setService( $saveSids );

		return $return;
	}

	function get_conflicts( $skip_from_today = FALSE )
	{
		$return = array();
		$completed = $this->getProp('completed');
		if( $completed )
		{
			return $return;
		}

		$starts_at = $this->getProp('starts_at');
		$duration = $this->getProp('duration');
		$lead_out = $this->getProp('lead_out');
		if( $skip_from_today )
		{
			$now = time();
			$skip_from = $now - 24*60*60;
			if( ($starts_at + $duration + $lead_out) < $skip_from )
			{
				return $return;
			}
		}

		if( ntsLib::hasVar( 'admin::tm2' ) )
		{
			$tm2 = ntsLib::getVar( 'admin::tm2' );
		}
		else
		{
			$tm2 = new haTimeManager2();
		}

		$saveLids = $tm2->locationIds;
		$saveRids = $tm2->resourceIds;
		$saveSids = $tm2->serviceIds;

		$tm2->setLocation( $this->getProp('location_id') );
		$tm2->setResource( $this->getProp('resource_id') );
		$tm2->setService( $this->getProp('service_id') );
		$tm2->setSkip( array($this->getId()) );

		$starts_at = $this->getProp('starts_at');
		$times = $tm2->getAllTime( $starts_at, $starts_at );
		if( ! isset($times[$starts_at]) )
		{
			$slot = $tm2->makeSlotFromAppointment( $this );
			$remain_seats = $tm2->checkSlot( $starts_at, $slot, TRUE );
			if( ! $remain_seats )
			{
				$return = $tm2->getSlotErrors();
			}
		}

		$tm2->setLocation( $saveLids );
		$tm2->setResource( $saveRids );
		$tm2->setService( $saveSids );

		return $return;
	}

	function setId( $id, $load = true )
	{
		if( preg_match("/[^\-\d]/", $id) )
		{
			return;
		}
		$this->id = $id;
		if( ($id != 0) && $load )
		{
			$this->load();
		}
	}

	function load()
	{
		$id = $this->getId();
		if( $id >= 0 )
		{
			return parent::load();
		}

		global $NTS_VIRTUAL_APPOINTMENTS;
		if( ! $NTS_VIRTUAL_APPOINTMENTS )
			$NTS_VIRTUAL_APPOINTMENTS = array();
		if( isset($NTS_VIRTUAL_APPOINTMENTS[$id]) )
		{
			$this->setByArray($NTS_VIRTUAL_APPOINTMENTS[$id]);
		}
	}

	function getBasePrice()
	{
		$pm =& ntsPaymentManager::getInstance();
		$return = $pm->getBasePrice( $this->getByArray() );
		return $return;
	}

	public function getStatusActions()
	{
		$actions = array();

		$rid = $this->getProp( 'resource_id' );
		$completedStatus = $this->getProp('completed');
		$approvedStatus = $this->getProp('approved');

		if( ! $completedStatus )
		{
			if( $approvedStatus )
			{
				$actions[] = array( 'complete',	ntsAppointment::_statusLabel(0, HA_STATUS_COMPLETED, '', 'i') . ' ' . M('Completed') );
				$actions[] = array( 'pending',	ntsAppointment::_statusLabel(0, HA_STATUS_PENDING, '', 'i') . ' ' . M('Pending') );
				$actions[] = array( 'reject',	ntsAppointment::_statusLabel(0, HA_STATUS_CANCELLED, '', 'i') . ' ' . M('Reject') );
				$actions[] = array( 'noshow',	ntsAppointment::_statusLabel(0, HA_STATUS_NOSHOW, '', 'i') . ' ' . M('No Show') );
			}
			else
			{
				$actions[] = array( 'approve',	ntsAppointment::_statusLabel(HA_STATUS_APPROVED, 0, '', 'i') . ' ' . M('Approve') );
				$actions[] = array( 'reject',	ntsAppointment::_statusLabel(0, HA_STATUS_CANCELLED, '', 'i') . ' ' . M('Reject') );
			}
		}
		else
		{
			if( $completedStatus == HA_STATUS_NOSHOW )
			{
				$actions[] = array( 'showup',	ntsAppointment::_statusLabel($approvedStatus, 0, '', 'i') . ' ' . M('Unmark No Show') );
			}
			if( $completedStatus == HA_STATUS_COMPLETED )
			{
				$actions[] = array( 'incomplete',	ntsAppointment::_statusLabel($approvedStatus, 0, '', 'i') . ' ' . M('Unmark Completed') );
			}
		}
		return $actions;
	}

	static function dump_labels()
	{
		$return = array(
			'date'		=> M('Date'),
			'time'		=> M('Starts At'),
			'time_end'	=> M('Ends At'),
			'duration'	=> M('Duration'),
			'location'	=> M('Location'),
			'resource'	=> M('Bookable Resource'),
			'service'	=> M('Service'),
			'customer'	=> M('Customer'),
			'notes'		=> M('Notes'),
			'status'	=> M('Status'),
			'total_amount'	=> M('Total Amount'),
			'paid_amount'	=> M('Paid Amount'),
			'paid_amount'	=> M('Payment Balance'),
			'invoice_ref'	=> M('Invoice'),
			'paid_through'	=> M('Paid Through'),
			'payment_notes'	=> M('Payment Notes'),
			'payment_balance'	=> M('Payment Balance'),
			'created'		=> M('Created'),
			);

		/* custom fields */
		$om =& objectMapper::getInstance();
		$customFields = $om->getFields( 'appointment', 'internal', array('service_id' => -1) );
		reset( $customFields );
		$customFieldTypes = array();
		foreach( $customFields as $cf )
		{
			$return[ $cf[0] ] = $cf[1];
		}

		$customerFields = $om->getFields( 'customer', 'internal' );
		$skipCustomerFields = array('first_name', 'last_name');
		reset( $customerFields );
		foreach( $customerFields as $cf )
		{
			if( in_array($cf[0], $skipCustomerFields) )
				continue;
			$return[ 'customer:' . $cf[0] ] = M('Customer') . ': ' . $cf[1];
		}
		$return[ 'customer:id' ] = M('Customer') . ': ID';

		return $return;
	}

	public function propView( $pname, $force_value = FALSE, $html = FALSE )
	{
		$return = '';
		$value = ( $force_value === FALSE ) ? $this->getProp( $pname ) : $force_value;
		switch( $pname )
		{
			case 'location_id':
				$object = ntsObjectFactory::get('location');
				$object->setId( $value );
				$return = ntsView::objectTitle($object, $html);
				break;

			case 'resource_id':
				$object = ntsObjectFactory::get('resource');
				$object->setId( $value );
				$return = ntsView::objectTitle($object, $html);
				break;

			case 'service_id':
				$object = ntsObjectFactory::get('service');
				$object->setId( $value );
				$return = ntsView::objectTitle($object, $html);
				break;

			case 'customer_id':
				$object = new ntsUser;
				$object->setId( $value );
				$return = ntsView::objectTitle($object, $html);
				break;

			case 'id':
				if( $value > 0 )
				{
					$return = M('Created');
				}
				else
				{
					$return = '';
				}
				break;

			case 'approved':
				if( $html )
				{
					$return = ntsAppointment::_statusLabel( $value, 0 );
				}
				else
				{
					$return = ntsAppointment::_statusText( $value, 0 );
				}
				break;

			case 'completed':
				$approved = $this->getProp('approved');
				if( $html )
				{
					$return = ntsAppointment::_statusLabel( $approved, $value );
				}
				else
				{
					$return = ntsAppointment::_statusText( $approved, $value );
				}
				break;

			case 'starts_at':
				$t = isset($NTS_VIEW['t']) ? $NTS_VIEW['t'] : new ntsTime;
				$t->setTimestamp( $value );
				if( $html )
				{
					$return = '<i class="fa fa-calendar fa-fw"></i>' . $t->formatDateFull() . ' ' .  '<i class="fa fa-clock-o fa-fw"></i>' . $t->formatTime();
				}
				else
				{
					$return = $t->formatDateFull() . ', ' . $t->formatTime();
				}
				break;

			default:
				$return = $value;
				break;
		}
		return $return;
	}

	public function dump( $html = FALSE, $show_fields = array() )
	{
		global $NTS_VIEW;
		$return = array();

		$t = isset($NTS_VIEW['t']) ? $NTS_VIEW['t'] : new ntsTime;
		$t->setTimestamp( $this->getProp('starts_at') );

		$return['id'] = $this->getId();
		$return['date'] = $t->formatDate();
		$return['time'] = $t->formatTime();
		$duration = $this->getProp('duration');
		$lead_out = $this->getProp('lead_out');
		$t->modify( '+' . $duration . ' seconds' );
		$return['time_end'] = $t->formatTime();
		$return['duration'] = $t->formatPeriod( $duration );
		$return['duration_short'] = $t->formatPeriodShort( $duration );
		$return['clean_up'] = $t->formatPeriod( $lead_out );
		$return['clean_up_short'] = $t->formatPeriodShort( $lead_out );

		$t->setTimestamp( $this->getProp('created_at') );
		$return['created'] = $t->formatDate() . ' ' . $t->formatTime();

		if( (! $show_fields) OR in_array('location', $show_fields) )
		{
			$return['location'] = $this->propView( 'location_id', FALSE, $html );
		}

		$return['service'] = $this->propView( 'service_id', FALSE, $html );

		if( (! $show_fields) OR in_array('resource', $show_fields) )
		{
			$return['resource'] = $this->propView( 'resource_id', FALSE, $html );
		}

		$customer = new ntsUser;
		$customer_id = $this->getProp('customer_id');
		$customer->setId( $this->getProp('customer_id') );
		$return['customer'] = $this->propView( 'customer_id', FALSE, $html );
		$return['customer:id'] = $customer_id;

		if( $html )
		{
			$return['status'] = $this->statusLabel( '&nbsp;' );
		}
		else
		{
			$return['status'] = $this->statusText();
		}

		$return['lrst'] = join( '-', 
			array(
				$this->getProp('location_id'),
				$this->getProp('resource_id'),
				$this->getProp('service_id'),
				$this->getProp('starts_at')
				)
			);

		$access = 'internal';
		$om =& objectMapper::getInstance();

	/* custom fields */
		$otherDetails = array(
			'service_id'	=> $this->getProp('service_id'),
			);

		$fields = $om->getFields( 'appointment', $access, $otherDetails );
		reset( $fields );
		foreach( $fields as $f )
		{
			$k = $f[0];
			$v = $this->getProp($k);
			if( $f[2] == 'checkbox' )
			{
				$v = $v ? M('Yes') : M('No');
			}
			$return[$k] = $v;
		}

	/* customer */
		$fields = $om->getFields( 'customer', $access );
		foreach( $fields as $f )
		{
			$k = $f[0];
			$v = $customer->getProp($k);
			if( $f[2] == 'checkbox' )
			{
				$v = $v ? M('Yes') : M('No');
			}
			$return['customer:' . $k] = $v;
		}

	/* price */
		$price = $this->getProp('price');
		if( strlen($price) > 0 )
		{
			if( (! $show_fields) OR in_array('total_amount', $show_fields) )
			{
				$amount = $this->getCost();
				$return['total_amount'] = ntsCurrency::formatPrice( $amount );
			}
			if( (! $show_fields) OR in_array('payment_balance', $show_fields) )
			{
				$due_amount = $this->getDue();
				$this_view = ntsCurrency::formatPrice( -$due_amount );
				if( $html )
				{
					if( $due_amount > 0 )
					{
						$this_view = '<span class="label label-danger">' . $this_view . '</span>';
					}
					elseif( $due_amount == 0 )
					{
						$this_view = '<span class="label label-success">' . M('Paid') . '</span>';
					}
					else
					{
						$this_view = '<span class="label label-success">' . $this_view . '</span>';
					}
				}
				$return['payment_balance'] = $this_view;
			}
		}
		else
		{
			$return['total_amount'] = '';
			$return['payment_balance'] = '';
		}

	/* transactions */
		if( in_array('invoice_ref', $show_fields) )
		{
			$invoices = array();
			$invoices_transactions = array();
			$invoices_info = $this->getInvoices();
			reset( $invoices_info );
			foreach( $invoices_info as $ia )
			{
				list( $invoiceId, $myNeededAmount, $due ) = $ia;
				$invoice = ntsObjectFactory::get( 'invoice' );
				$invoice->setId( $invoiceId );
				$invoices[] = $invoice;

				$transactions = $invoice->getTransactions();
				foreach( $transactions as $tra )
				{
					$invoices_transactions[] = $tra;
				}
			}

			$thisView = array();
			reset( $invoices );
			foreach( $invoices as $invoice )
			{
				$thisView[] = $invoice->getProp('refno');
			}
			$thisView = join( ', ', $thisView );
			$return['invoice_ref'] = $thisView;

			$calc = new ntsMoneyCalc;
			$thisView1 = array();
			$thisView2 = array();
			reset( $invoices_transactions );
			foreach( $invoices_transactions as $tr )
			{
				$thisView1[] = $tr->getProp('pgateway');
				$ref = $tr->getProp('pgateway_ref');
				$response = $tr->getProp('pgateway_response');
				$thisView2[] = $ref ? $ref . ':' . $response : $response;

				$calc->add( $tr->getProp('amount_net') );
			}
			$amount_received = $calc->result();
			$return['amount_received'] = $amount_received;

			$thisView1 = join( ', ', $thisView1 );
			$return['paid_through'] = $thisView1;
			$thisView2 = join( ', ', $thisView2 );
			$return['payment_notes'] = $thisView2;
		}

		return $return;
	}

	function getInvoice(){
		return null;
		}

	function getOrder(){
		$return = NULL;
		$orders = $this->getChildren( 'order' );
		if( $orders && isset($orders[0]) )
			$return = $orders[0];
		return $return;
		}

	function doRefund()
	{
		$ntsdb =& dbWrapper::getInstance();
		$objId = $this->getId();

		$where = array(
			'obj_class'		=> array('=', 'invoice'),
			'meta_name'		=> array('=', '_appointment'),
			'meta_value'	=> array('=', $objId),
			);
		$what = array(
			'meta_data'	=> 0
			);
		$ntsdb->update( 'objectmeta', $what, $where );

		$pm =& ntsPaymentManager::getInstance();
		$invoices = $this->getInvoices();
		reset( $invoices );
		foreach( $invoices as $ia )
		{
			list( $invoiceId, $myNeededAmount, $due ) = $ia;
			$invoice = ntsObjectFactory::get( 'invoice' );
			$invoice->setId( $invoiceId );
			$pm->updateInvoice( $invoice );
		}
	}

	function redoRefund(){
		$ntsdb =& dbWrapper::getInstance();
		$objId = $this->getId();
		$cost = $this->getCost();

		$where = array(
			'obj_class'		=> array('=', 'invoice'),
			'meta_name'		=> array('=', '_appointment'),
			'meta_value'	=> array('=', $objId),
			);
		$what = array(
			'meta_data'	=> $cost
			);
		$ntsdb->update( 'objectmeta', $what, $where );

		$pm =& ntsPaymentManager::getInstance();
		$invoices = $this->getInvoices();
		reset( $invoices );
		foreach( $invoices as $ia ){
			list( $invoiceId, $myNeededAmount, $due ) = $ia;
			$invoice = ntsObjectFactory::get( 'invoice' );
			$invoice->setId( $invoiceId );
			$pm->updateInvoice( $invoice );
			}
		}

	function getCost()
	{
		$return = '';
		$price = $this->getProp('price');

		if( ! strlen($price) )
			return $return;

		$completed = $this->getProp('completed');
		if( in_array($completed, array(HA_STATUS_CANCELLED)) )
		{
			return $return;
		}

		$return = $price;
		if( $price )
		{
			$postings = $this->get_accounting_postings();
			if( $postings )
			{
				$calc = new ntsMoneyCalc;
				$calc->add( $price );

				reset( $postings );
				foreach( $postings as $p )
				{
					if( $p['asset_id'] != 0 )
						continue;

					$action = $p['obj_class'] . '::' . $p['action'];
					if( ! in_array($action, $this->cost_actions) )
					{
						continue;
					}
					$calc->add( -$p['asset_value'] );
				}
				$return = $calc->result();
			}
		}
		return $return;
	}

	function getDue()
	{
		$cost = $this->getCost();
		$paid = $this->getPaidAmount();

		$calc = new ntsMoneyCalc;
		$calc->add( $cost );
		$calc->add( -$paid );
		$return = $calc->result();
		return $return;
	}

	function getInvoices()
	{
		$ntsconf =& ntsConf::getInstance();
		$taxRate = $ntsconf->get('taxRate');

		if( ! is_array($this->_invoices) )
		{
			$this->_invoices = array();

			$ntsdb =& dbWrapper::getInstance();
			$objId = $this->getId();

			$invoices = array();
			$where = array(
				'obj_class'	=> array('=', 'appointment'),
				'obj_id'	=> array('=', $objId),
				);
			$join = array(
				array( 'invoices', array('invoice_items.invoice_id' => array('=', '{PRFX}invoices.id', 1)) )
				);
			$addon = 'ORDER BY {PRFX}invoices.due_at ASC';
			$result = $ntsdb->select( 
				array(
					'invoice_id',
					'{PRFX}invoice_items.amount',
					'taxable',
					'{PRFX}invoices.due_at AS due_at'
					),
				'invoice_items',
				$where,
				$addon,
				$join
				);

			while( $i = $result->fetch() )
			{
				$calc = new ntsMoneyCalc;
				$calc->add( $i['amount'] );
				if( $i['taxable'] && $taxRate )
				{
					$tax = ntsLib::calcTax( $i['amount'], $taxRate );
					$calc->add( $tax );
				}
				$total = $calc->result();
				$this->_invoices[] = array( $i['invoice_id'], $total, $i['due_at'] );
			}
		}
		return $this->_invoices;
	}

	function statusLabel( $force_text = NULL, $html_element = 'span' )
	{
		$approved = $this->getProp('approved');
		$completed = $this->getProp('completed');
		return ntsAppointment::_statusLabel( $approved, $completed, $force_text, $html_element );
	}

	function paymentStatus( $short = FALSE )
	{
		$return = '';
		$price = $this->getProp('price');
		if( ! strlen($price) )
			return $return;

		$due_amount = $this->getDue();

		if( $due_amount <= 0 )
		{
			if( $short )
			{
				$return = '<i class="fa fa-fw btn-success-o fa-border fa-usd text-small" title="' . M('Paid') . '"></i>';
			}
			else
			{
				$return = '<span class="btn btn-xs btn-success-o">' . M('Paid') . '</span>';
			}
		}
		else
		{
			$cost = $this->getCost();
			if( $due_amount < $cost )
			{
				if( $short )
					$return = '<i class="fa fa-fw btn-warning-o fa-border fa-usd text-small" title="' . M('Partially Paid') . '"></i>';
				else
					$return = '<span class="btn btn-xs btn-warning-o">' . M('Partially Paid') . '</span>';
			}
			else
			{
				if( $short )
					$return = '<i class="fa fa-fw btn-danger fa-inverse fa-border fa-usd text-small" title="' . M('Not Paid') . '"></i>';
				else
					$return = '<span class="btn btn-xs btn-danger-o">' . M('Not Paid') . '</span>';
			}
		}
		return $return;
	}

	function statusText()
	{
		$approved = $this->getProp('approved');
		$completed = $this->getProp('completed');
		list( $message, $class ) = ntsAppointment::_statusText( $approved, $completed );
		return $message;
	}

	static function _statusClass( $approved, $completed )
	{
		list( $message, $class ) = ntsAppointment::_statusText( $approved, $completed );
		return $class;
	}

	function statusClass()
	{
		$approved = $this->getProp('approved');
		$completed = $this->getProp('completed');
		$return = ntsAppointment::_statusClass( $approved, $completed );
		return $return;
	}

	static function _statusText( $approved, $completed )
	{
		$message = '';
		$class = '';

		$completed_conf = array(
			HA_STATUS_COMPLETED	=> array( 'info',		M('Completed') ),
			HA_STATUS_CANCELLED	=> array( 'archive',	M('Cancelled') ),
			HA_STATUS_NOSHOW	=> array( 'danger',		M('No Show') ),
			);

		$approved_conf = array(
			HA_STATUS_APPROVED	=> array( 'success',	M('Approved') ),
			HA_STATUS_PENDING	=> array( 'warning',	M('Pending') ),
			);

		if( $completed )
		{
			list( $class, $message ) = $completed_conf[ $completed ];
		}
		else 
		{
			list( $class, $message ) = $approved_conf[ $approved ];
		}

//		$class = 'label-' . $class;
		$return = array( $message, $class );
		return $return;
	}

	static function _statusLabel( $approved, $completed, $force_text = NULL, $html_element = 'span' )
	{
		$class = array();

		$message = '';
		list( $message, $status_class ) = ntsAppointment::_statusText( $approved, $completed );

		if( $html_element == 'i' )
		{
			$class[] = 'icon-' . $status_class;
			$class = join( ' ', $class );
			$out = '<i class="fa fa-fw fa-square ' . $class . '" title="' . $message . '"';
			$out .= '>';
			$out .= '</i>';
		}
		else
		{
			$class[] = 'btn';
			$class[] = 'btn-xs';
			$class[] = 'btn-' . $status_class;

			$class = join( ' ', $class );

			$out = '<' . $html_element . ' class="' . $class . '" title="' . $message . '"';
			$out .= '>';
			if( $force_text === NULL )
				$out .= $message;
			else
			{
				if( ! strlen($force_text) )
					$force_text = '&nbsp;';
				$out .= $force_text;
			}
			$out .= '</' . $html_element . '>';
		}
		return $out;
	}

	function getStatus(){
		$alert = 0;
		$cssClass = '';
		$message = '';
		$return = array( $alert, $cssClass, $message );

		$completed = $this->getProp('completed');
		if( $completed ){
			switch( $completed ){
				case HA_STATUS_COMPLETED:
					$alert = 0;
					$cssClass = 'ntsCompleted';
					$message = M('Completed');
					break;
				case HA_STATUS_CANCELLED:
					$alert = 1;
					$cssClass = 'ntsCancelled';
					$message = M('Cancelled');
					break;
				case HA_STATUS_NOSHOW:
					$alert = 1;
					$cssClass = 'ntsNoshow';
					$message = M('No Show');
					break;
				}
			}
		else {
			if( $this->getProp('approved') ){
				$alert = 0;
				$cssClass = 'ntsApproved';
				$message = M('Approved');
				}
			else {
				$alert = 1;
				$cssClass = 'ntsPending';
				$message = M('Pending');
				}
			}

		$return = array( $alert, $cssClass, $message );
		return $return;
		}
	}
?>