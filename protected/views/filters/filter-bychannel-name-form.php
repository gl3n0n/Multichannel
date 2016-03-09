<fieldset>
	<legend>Channel Name</legend>
	<input type='text' 
		id='byChannelName' 
		name='byChannelName' 
		placeholder='Channel Name' 
		title='Search Channel Name' 
		style='width:200px;'
		maxlength='20'
		value="<?php echo Yii::app()->request->getParam('byChannelName');?>"
	/>
</fieldset>
