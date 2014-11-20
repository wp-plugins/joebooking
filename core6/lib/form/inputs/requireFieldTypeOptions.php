<tbody ID="requireOptions-Textarea">
<TR>
	<td class="ntsFormLabel"><?php echo M('Textarea Columns'); ?> *</td>
	<td class="ntsFormValue">
	<?php
	echo $this->makeInput(
		'text',
		array(
			'id'		=> 'attr-cols',
			'attr'		=> array(
				'size'	=> 4,
				),
			'required'	=> 1,
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> 'Please enter the columns',
				),
			array(
				'code'		=> 'number.php', 
				'error'		=> 'Only numbers are allowed for this field',
				),
			)
		);
	?>
	</TD>
</TR>

<TR>
	<td class="ntsFormLabel"><?php echo M('Textarea Rows'); ?> *</td>
	<td class="ntsFormValue">
	<?php
	echo $this->makeInput(
		'text',
		array(
			'id'		=> 'attr-rows',
			'attr'		=> array(
				'size'	=> 4,
				),
			'required'	=> 1,
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> 'Please enter the rows',
				),
			array(
				'code'		=> 'number.php', 
				'error'		=> 'Only numbers are allowed for this field',
				),
			)
		);
	?>
	</TD>
</TR>

<TR>
	<td class="ntsFormLabel"><?php echo M('Default Value'); ?></td>
	<td class="ntsFormValue">
	<?php
	echo $this->makeInput(
		'textarea',
		array(
			'id'		=> 'default_value-textarea',
			'attr'		=> array(
				'cols'	=> 32,
				'rows'	=> 3,
				),
			)
		);
	?>
	</TD>
</TR>
</tbody>

<tbody ID="requireOptions-Text">
<TR>
	<td class="ntsFormLabel"><?php echo M('Text Field Size'); ?> *</td>
	<td class="ntsFormValue">
	<?php
	echo $this->makeInput(
		'text',
		array(
			'id'		=> 'attr-size',
			'attr'		=> array(
				'size'	=> 4,
				),
			'required'	=> 1,
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> M('Required'),
				),
			array(
				'code'		=> 'number.php', 
				'error'		=> M('Numbers only'),
				),
			)
		);
	?>
	</TD>
</TR>
<TR>
	<td class="ntsFormLabel"><?php echo M('Default Value'); ?></td>
	<td class="ntsFormValue">
	<?php
	echo $this->makeInput(
		'text',
		array(
			'id'		=> 'default_value-text',
			'attr'		=> array(
				'size'	=> 32,
				),
			)
		);
	?>
	</TD>
</TR>
</tbody>

<tbody ID="requireOptions-Select">
<TR>
	<td class="ntsFormLabel"><?php echo M('Select Options'); ?> *</td>
	<td class="ntsFormValue">
	<?php
	echo $this->makeInput(
		'textareaArray',
		array(
			'id'		=> 'attr-options',
			'help'		=> M('One option per line') . '. ' . M('Add a star sign (*) before the default value') . '.',
			'attr'		=> array(
				'cols'	=> 24,
				'rows'	=> 4,
				),
			'required'	=> 1,
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> 'Please enter the field options',
				),
			)
		);
	?>
	</TD>
</TR>
</tbody>

<tbody ID="requireOptions-Checkbox">
<TR>
	<td class="ntsFormLabel"><?php echo M('Default Value'); ?></td>
	<td class="ntsFormValue">
	<?php
	echo $this->makeInput(
		'checkbox',
		array(
			'id'		=> 'default_value-checkbox',
			)
		);
	?>
	</TD>
</TR>
</tbody>
