<fieldset>
	<legend>By Email</legend>
	<input type='text' 
		id='byEmail' 
		name='byEmail' 
		placeholder='Email' 
		title='Search Email' 
		style='width:200px;'
		maxlength='20'
		value="<?php echo Yii::app()->request->getParam('byEmail');?>"
	/>
</fieldset>
