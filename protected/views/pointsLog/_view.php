<?php
/* @var $this PointsLogController */
/* @var $data PointsLog */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('PointLogId')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->PointLogId), array('view', 'id'=>$data->PointLogId)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('CustomerId')); ?>:</b>
	<a href="<?php echo Yii::app()->createAbsoluteUrl('/customers/' .  $data->CustomerId); ?>"> <?php echo $data->pointlogCustomers->Email; ?></a>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('SubscriptionId')); ?>:</b>
	<?php echo CHtml::encode($data->SubscriptionId); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('ClientId')); ?>:</b>
	<?php echo $data->pointlogClients->CompanyName; ?>
	<br />
	
	<b><?php echo CHtml::encode($data->getAttributeLabel('BrandId')); ?>:</b>
	<?php echo $data->pointlogBrands->BrandName; ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('CampaignId')); ?>:</b>
	<?php echo $data->pointlogCampaigns->CampaignName; ?>
	<br />
	
	<b><?php echo CHtml::encode($data->getAttributeLabel('ChannelId')); ?>:</b>
	<?php echo $data->pointlogChannels->ChannelName; ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('PointsId')); ?>:</b>
	<?php echo CHtml::encode($data->PointsId); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('DateCreated')); ?>:</b>
	<?php echo CHtml::encode($data->DateCreated); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('CreatedBy')); ?>:</b>
	<?php echo CHtml::encode($data->CreatedBy); ?>
	<br />

	*/ ?>

</div>