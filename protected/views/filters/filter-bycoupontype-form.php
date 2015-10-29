<fieldset class='filterSrch'>
<legend>Coupon Type</legend>
<?php
	echo ZHtml::enumDropDownList(CouponSystem::model(), 'CouponType', array(
	'id'   =>'byCouponType',
	'name' =>'byCouponType',
	'style'=>'width:203px;',
	'prompt'=>'-- --',
	'options' => array(Yii::app()->request->getParam('byCouponType') => array('selected'=>true)),
));
?>
<br/>
</fieldset>	