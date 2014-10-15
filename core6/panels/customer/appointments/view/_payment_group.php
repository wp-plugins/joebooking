<?php
$app = $a->getByArray();

$paid_amount = $a->getPaidAmount();
$default_due = $pm->getPrepayAmount( $app );

$due_amount = isset($prepay[$a->getId()]) ? $prepay[$a->getId()] : $default_due;

if( is_array($due_amount) )
{
}
else
{
	if( $due_amount > $cost )
		$due_amount = $cost;

	if( ! $has_online )
		$due_amount = 0;

	if( $paid_amount >= $due_amount )
	{
		$due_amount = 0;
	}
}

if( ($paid_amount OR $due_amount) OR ($cost && $has_online) )
{
	if( is_array($due_amount) )
	{
		$asset_id = $due_amount[0];
		$asset_value = $due_amount[1];
		if( strpos($asset_id, '-') !== FALSE )
		{
			list( $asset_short_id, $asset_expires ) = explode( '-', $asset_id );
		}
		$this_view = $aam->format_asset( $asset_id, $asset_value, FALSE, FALSE );

		$this_view .= ' [' . M('Remain') . ': ';
		$this_view .= $aam->format_asset( $asset_id, $customer_balance[$asset_id], FALSE, FALSE );
		if( $asset_expires )
		{
			$t->setTimestamp( $asset_expires );
			$this_view .= ', ' . M('Expires') . ': ' . $t->formatDateFull();
		}
		$this_view .= ']';

		$default_payment_option = M('Use Balance') . ': ' . '<strong>' . $this_view . '</strong>';
	}

	$default_due = trim($default_due);
	$cost = trim($cost);
	$money_due_amount = is_array($due_amount) ? $default_due : trim($due_amount);

	if( $has_offline && $money_due_amount )
	{
		$link = ntsLink::makeLink(
			'-current-',
			'prepay',
			array(
				'ai'		=> $a->getId(),
				'prepay'	=> 0
				)
			);

		$label = $has_offline;

		$payment_options[] = array(
			$label,
			$link
			);
	}

	$possible_prepay = array();

	if( is_array($due_amount) )
	{
		$possible_prepay[] = $money_due_amount;
	}
	if( ($default_due != $money_due_amount) && ($default_due > $paid_amount) )
	{
		$possible_prepay[] = $default_due;
	}
	if( ($cost != $money_due_amount) && ($cost > $paid_amount) )
	{
		$possible_prepay[] = $cost;
	}

	if( $paid_amount < $money_due_amount )
	{
		if( ! is_array($due_amount) )
		{
			$default_payment_option = M('Pay Now') . ' ' . '<strong>' . ntsCurrency::formatPrice($money_due_amount - $paid_amount) . '</strong>';
		}
		if( $paid_amount )
		{
			$possible_prepay[] = $paid_amount;
		}
	}

	if( $possible_prepay )
		$payment_options[] = M('Pay Online');
	foreach( $possible_prepay as $pp )
	{
		$link = ntsLink::makeLink(
			'-current-',
			'prepay',
			array(
				'ai'		=> $a->getId(),
				'prepay'	=> $pp
				)
			);

		if( ($pp - $paid_amount) > 0)
		{
			$label = ntsCurrency::formatPrice($pp - $paid_amount);
		}
		else
		{
			$label = M('Pay Later');
		}

		$payment_options[] = array(
			$label,
			$link
			);
	}

	/* now balance */
	if( $balance_cover )
	{
		if( ! is_array($due_amount) OR (count($balance_cover) > 1) )
		{
			$payment_options[] = M('Use Balance');
		}

		foreach( $balance_cover as $asset_id => $asset_value )
		{
			if( is_array($due_amount) && (trim($due_amount[0]) == trim($asset_id)) )
			{
				continue;
			}

			$asset_expires = 0;
			if( strpos($asset_id, '-') !== FALSE )
			{
				list( $asset_short_id, $asset_expires ) = explode( '-', $asset_id );
			}
			$this_view = $aam->format_asset( $asset_id, $asset_value, FALSE, FALSE );

			$this_view .= ' [' . M('Available') . ': ';
			$this_view .= $aam->format_asset( $asset_id, $customer_balance[$asset_id], FALSE, FALSE );
			if( $asset_expires )
			{
				$t->setTimestamp( $asset_expires );
				$this_view .= ', ' . M('Expires') . ': ' . $t->formatDateFull();
			}
			$this_view .= ']';

			$link = ntsLink::makeLink(
				'-current-',
				'prepay',
				array(
					'ai'		=> $a->getId(),
					'prepay'	=> $asset_value,
					'asset_id'	=> $asset_id,
					)
				);

			$payment_options[] = array(
				$this_view,
				$link
				);
		}
	}
}
?>