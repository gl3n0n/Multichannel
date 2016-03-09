<fieldset>
	<legend>Brand Name</legend>
	<input type='text' 
		id='byBrandName' 
		name='byBrandName' 
		placeholder='Brand Name' 
		title='Search Brand Name' 
		style='width:200px;'
		maxlength='20'
		value="<?php echo Yii::app()->request->getParam('byBrandName');?>"
	/>
</fieldset>
