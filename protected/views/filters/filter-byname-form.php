<fieldset>
	<legend>By Name</legend>
	<input type='text' 
		id='byName' 
		name='byName' 
		placeholder='Name' 
		title='Search Name' 
		style='width:200px;'
		maxlength='20'
		value="<?php echo Yii::app()->request->getParam('byName');?>"
	/>
</fieldset>
