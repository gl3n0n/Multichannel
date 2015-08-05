<?php
/* @var $this CouponController */
/* @var $data Coupon */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('CouponId')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->CouponId), array('view', 'id'=>$data->CouponId)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Code')); ?>:</b>
	<?php echo CHtml::encode($data->Code); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Type')); ?>:</b>
	<?php echo CHtml::encode($data->Type); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('TypeId')); ?>:</b>
	<?php echo CHtml::encode($data->TypeId); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Source')); ?>:</b>
	<?php echo CHtml::encode($data->Source); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('ExpiryDate')); ?>:</b>
	<?php echo CHtml::encode($data->ExpiryDate); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Status')); ?>:</b>
	<?php echo CHtml::encode($data->Status); ?>
	<br />
	
	<b><?php echo CHtml::encode($data->getAttributeLabel('File')); ?>:</b>
	<?php echo CHtml::encode($data->File); ?>
	<br />


</div>