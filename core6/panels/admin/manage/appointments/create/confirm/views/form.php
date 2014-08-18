<?php
/* COUPON */
$session = new ntsSession;
$coupon = $session->userdata('coupon');

$show_coupon = FALSE;
$coupon_valid = FALSE;
$coupon_promotions = array();

$ntspm =& ntsPaymentManager::getInstance();
$show_coupon = $coupon ? TRUE : FALSE;
$coupon_valid = FALSE;
$coupon_promotions = array();

if( $coupon )
{
	$coupon_valid = TRUE;
	/* check if valid */
	foreach( $apps as $r )
	{
		$coupon_promotions = $ntspm->getPromotions( $r, $coupon, TRUE );
		if( $coupon_promotions )
		{
			break;
		}
	}
	if( ! $coupon_promotions )
	{
		$coupon_valid = FALSE;
		$this->errors['coupon'] = M('Not Valid');
	}
}
else
{
	foreach( $apps as $r )
	{
		$coupon_promotions = $ntspm->getPromotions( $r, '', TRUE );
		if( $coupon_promotions )
		{
			$show_coupon = TRUE;
			break;
		}
	}
}

/* TOTAL PRICE */
$pm =& ntsPaymentManager::getInstance();

$grand_total_amount = 0;
$grand_base_amount = 0;
for( $ii = 1; $ii <= count($apps); $ii++ )
{
	$a = $apps[$ii-1];
	$base_amount = $pm->getBasePrice( $a );
	$total_amount = $pm->getPrice( $a, $coupon );

	$grand_base_amount += $base_amount;
	$grand_total_amount += $total_amount;
}

/* CUSTOM FIELDS */
$om =& objectMapper::getInstance();
$custom_forms = array();

foreach( $apps as $a )
{
	$form_id = $om->isFormForService( $a['service_id'] );
	if( $form_id )
	{
		$custom_forms[ $form_id ] = $a['service_id'];
	}
}

$custom_fields = array();
if( $custom_forms )
{
	$class = 'appointment';
	reset( $custom_forms );
	foreach( $custom_forms as $form_id => $sid )
	{
		$other_details = array(
			'service_id'	=> $sid,
			);
		$this_fields = $om->getFields( $class, 'internal', $other_details );
		$custom_fields[ $form_id ] = $this_fields;
	}
}

/* BUTTON LABEL */
$btn_label = '';
if( count($apps) > 1 )
{
	$btn_text = M('Create Appointments') . ' [' . count($apps) . ']';
}
else
{
	$btn_text = M('Create Appointment');
}

$btn_class = 'btn-success';
foreach( $status as $ii => $errors )
{
	if( is_array($errors) && $errors )
	{
		$btn_class = 'btn-danger';
		break;
	}
	elseif( ! $errors )
	{
		$btn_class = 'btn-archive';
	}
}
if( ! $btn_class )
	$btn_class = 'btn-success';

$btn = '<INPUT class="btn btn-lg ' . $btn_class . '" title="' . $btn_label . '" TYPE="submit" VALUE="' . $btn_text . '">';
?>

<?php if( $custom_fields ) : ?>
	<?php foreach( $custom_fields as $form_id => $farray ) : ?>
		<?php if( count($custom_fields) > 1 ) : ?>
			<?php
			$form_obj = ntsObjectFactory::get( 'form' );
			$form_obj->setId( $form_id );
			$form_title = $form_obj->getProp('title');
			?>
			<?php
			echo ntsForm::wrapInput(
				'',
				'<em>' . $form_title . '</em>'
				);
			?>
		<?php endif; ?>

		<?php foreach( $farray as $f ) : ?>
			<?php $c = $om->getControl( $class, $f[0], FALSE ); ?>
			<?php
			echo ntsForm::wrapInput(
				$c[0],
				$this->buildInput(
					$c[1],
					$c[2],
					$c[3]
					)
				);
			?>
		<?php endforeach; ?>
	<?php endforeach; ?>
<?php endif; ?>
<?php
echo $this->makePostParams('-current-', 'create' );
?>
<?php
$price_view = '';
if( $grand_total_amount )
{
	if( $grand_base_amount != $grand_total_amount )
	{
		$price_view .= '<span class="text-muted" style="text-decoration: line-through;">' . ntsCurrency::formatPrice($grand_base_amount) . '</span> ' . ntsCurrency::formatPrice($grand_total_amount);
	}
	else
	{
		$price_view .= ntsCurrency::formatPrice($grand_total_amount);
	}
}
?>

<?php
$dl_class = $custom_fields ? 'dl-horizontal' : '';
?>

<?php if( $price_view ) : ?>
	<dl class="<?php echo $dl_class; ?>">
		<dt>
			<?php echo M('Total'); ?>
		</dt>
		<dd>
			<ul class="list-inline list-separated">
				<li>
					<span class="btn btn-default btn-lg"><?php echo $price_view; ?></span>
				</li>
				<?php if( $show_coupon ) : ?>
					<?php if( $coupon ) : ?>
						<li>
							<?php echo M('Coupon Code'); ?>
						</li>
					<?php endif; ?>
					<li>
						<?php require( dirname(__FILE__) . '/_form_coupon_options.php' ); ?>
					</li>
				<?php endif; ?>
			</ul>
		</dd>
	</dl>
<?php endif; ?>



<?php if( $custom_fields ) : ?>

	<?php
	echo ntsForm::wrapInput(
		M('Customer') . ': ' . M('Notification'),
		$this->buildInput(
			'checkbox',
			array(
				'id'		=> 'notify_customer',
				'default'	=> 1,
				)
			)
		);
	?>

	<?php
	echo ntsForm::wrapInput(
		'',
		$btn
		);
	?>
<?php else : ?>
	<p>
		<div class="checkbox">
		<label>
			<?php
			echo $this->makeInput(
				'checkbox',
				array(
					'id'	=> 'notify_customer',
					'default'	=> 1,
					)
				);
			?> <?php echo M('Customer'); ?>: <?php echo M('Notification'); ?>
		</label>
		</div>
	</p>

	<p>
		<?php echo $btn; ?>
	</p>
<?php endif; ?>