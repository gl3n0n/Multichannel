<fieldset>
	<legend>By Customer Name</legend>
	<input type='text' 
		id='byCustomerName' 
		name='byCustomerName' 
		placeholder='CustomerName' 
		title='Search ( Firstname/Lastname )' 
		style='width:200px;'
		maxlength='20'
		value="<?php echo Yii::app()->request->getParam('byCustomerName');?>"
	/>
</fieldset>