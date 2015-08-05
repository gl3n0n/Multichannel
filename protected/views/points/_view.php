<?php
/* @var $this PointsController */
/* @var $data Points */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('PointsId')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->PointsId), array('view', 'id'=>$data->PointsId)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('ClientId')); ?>:</b>
	<?php echo $data->pointClients->CompanyName; ?>
	<br />
	
	<b><?php echo CHtml::encode($data->getAttributeLabel('BrandId')); ?>:</b>
	<?php echo $data->pointBrands->BrandName; ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('CampaignId')); ?>:</b>
	<?php echo $data->pointCampaigns->CampaignName; ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('ChannelId')); ?>:</b>
	<?php echo $data->pointChannels->ChannelName; ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('From')); ?>:</b>
	<?php echo CHtml::encode($data->From); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('To')); ?>:</b>
	<?php echo CHtml::encode($data->To); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Value')); ?>:</b>
	<?php echo CHtml::encode($data->Value); ?>
	<br />
	
	<b><?php echo CHtml::encode($data->getAttributeLabel('PointAction')); ?>:</b>
	<?php echo CHtml::encode($data->PointAction); ?>
	<br />
	
	<b><?php echo CHtml::encode($data->getAttributeLabel('PointCapping')); ?>:</b>
	<?php echo CHtml::encode($data->PointCapping); ?>
	<br />
	
	<b><?php echo CHtml::encode($data->getAttributeLabel('Status')); ?>:</b>
	<?php echo CHtml::encode($data->Status); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('Status')); ?>:</b>
	<?php echo CHtml::encode($data->Status); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('DateCreated')); ?>:</b>
	<?php echo CHtml::encode($data->DateCreated); ?>
	<br />

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