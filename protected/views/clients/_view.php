<?php
/* @var $this ClientsController */
/* @var $data Clients */
?>
<div class="view">
	<b><?php echo CHtml::encode($data->getAttributeLabel('ClientId')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->ClientId), array('view', 'id'=>$data->ClientId)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('CompanyName')); ?>:</b>
	<?php echo CHtml::encode($data->CompanyName); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Address')); ?>:</b>
	<?php echo CHtml::encode($data->Address); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Email')); ?>:</b>
	<?php echo CHtml::encode($data->Email); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Landline')); ?>:</b>
	<?php echo CHtml::encode($data->Landline); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Status')); ?>:</b>
	<?php echo CHtml::encode($data->Status); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('DateCreated')); ?>:</b>
	<?php echo CHtml::encode($data->DateCreated); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('CreatedBy')); ?>:</b>
	<?php echo CHtml::encode($data->CreatedBy); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('DateUpdated')); ?>:</b>
	<?php echo CHtml::encode($data->DateUpdated); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('UpdatedBy')); ?>:</b>
	<?php echo CHtml::encode($data->UpdatedBy); ?>
	<br />
	
	*/ ?>

</div>