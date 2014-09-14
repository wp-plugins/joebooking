<?php
if( $balance_cover )
{
	$payment_options[] = M('Use Balance');
	foreach( $balance_cover as $asset_id => $asset_value )
	{
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

		$balance_link = ntsLink::makeLink(
			'-current-/../edit/paybalance',
			'',
			array(
				'_id'			=> $a->getId(),
				'asset_id'		=> $asset_id,
				'asset_value'	=> $asset_value
				)
			);

		$payment_options[] = array(
			$this_view,
			$balance_link
			);
	}
}
if( $has_online )
{
	if( $balance_cover )
	{
		$payment_options[] = M('Pay Online');
		$payment_options[] = array(
			ntsCurrency::formatPrice( $due ),
			'#'
			);
	}
	else
	{
		$link = ntsLink::makeLink(
			'-current-',
			'payone',
			array(
				'_id'		=> $a->getId(),
				)
			);

		$payment_options[] = array(
			M('Pay Online') . ' ' . ntsCurrency::formatPrice( $due ),
			$link
			);
	}
}
?>