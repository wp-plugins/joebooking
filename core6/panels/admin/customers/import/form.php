<?php
$customer_fields = $NTS_VIEW['customer_fields'];
$required_fields = $NTS_VIEW['required_fields'];
?>
<TABLE class="ntsForm">
<tr>
	<td class="ntsFormLabel"><?php echo M('CSV File'); ?></td>
	<td class="ntsFormValue">
	<?php
	echo $this->makeInput (
	/* type */
		'upload',
	/* attributes */
		array(
			'id'	=> 'file',
			)
		);
	?>
	</td>
</tr>

<tr>
<td>&nbsp;</td>
<td>
<?php echo $this->makePostParams('-current-', 'upload'); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Import'); ?>">
</td>
</tr>
</table>

<p>
<?php echo M('We can recognize the following fields in your CSV file'); ?>:
<ul>
<?php foreach( $customer_fields as $cf ) : ?>
<li>
<?php if( in_array($cf, $required_fields) ) : ?>
<strong><?php echo $cf; ?></strong>
<?php else :  ?>
<?php echo $cf; ?>
<?php endif; ?>

</li>
<?php endforeach; ?>
</ul>
