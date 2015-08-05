<?php
/* @var $this GeneratedCouponsController */
/* @var $data GeneratedCoupons */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('GeneratedCouponId')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->GeneratedCouponId), array('view', 'id'=>$data->GeneratedCouponId)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('CouponId')); ?>:</b>
	<?php echo CHtml::encode($data->CouponId); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('CustomerId')); ?>:</b>
	<?php echo CHtml::encode($data->CustomerId); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Status')); ?>:</b>
	<?php echo CHtml::encode($data->Status); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Code')); ?>:</b>
	<?php echo CHtml::encode($data->Code); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('DateCreated')); ?>:</b>
	<?php echo CHtml::encode($data->DateCreated); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('CreatedBy')); ?>:</b>
	<?php echo CHtml::encode($data->CreatedBy); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('DateUpdated')); ?>:</b>
	<?php echo CHtml::encode($data->DateUpdated); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('UpdatedBy')); ?>:</b>
	<?php echo CHtml::encode($data->UpdatedBy); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('DateRedeemed')); ?>:</b>
	<?php echo CHtml::encode($data->DateRedeemed); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('CouponMappingId')); ?>:</b>
	<?php echo CHtml::encode($data->CouponMappingId); ?>
	<br />

	*/ ?>

</div>