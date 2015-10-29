<fieldset>
	<legend>Coupon Name</legend>
	<input type='text' 
		id='byCouponName' 
		name='byCouponName' 
		placeholder='Coupon Name' 
		title='Search Coupon Name' 
		style='width:200px;'
		maxlength='20'
		value="<?php echo Yii::app()->request->getParam('byCouponName');?>"
	/>
</fieldset>
