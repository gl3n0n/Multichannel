<?php
/* @var $this CustomerSubscriptionsController */
/* @var $data CustomerSubscriptions */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('SubscriptionId')); ?>:</b>
	<?php echo CHtml::encode($data->SubscriptionId); ?> <a href="<?php echo Yii::app()->createAbsoluteUrl('/pointsLog/pointid?points_id=' . $data->SubscriptionId ); ?>"> View Logs </a>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('CustomerId')); ?>:</b>
	<a href="<?php echo Yii::app()->createAbsoluteUrl('/customers/' .  $data->CustomerId); ?>"> <?php echo $data->subsCustomers->Email; ?> </a>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('ClientId')); ?>:</b>
	<?php echo $data->subsClients->CompanyName; ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('BrandId')); ?>:</b>
	<?php echo $data->subsBrands->BrandName; ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('CampaignId')); ?>:</b>
	<?php echo $data->subsCampaigns->CampaignName; ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('ChannelId')); ?>:</b>
	<?php echo $data->subsChannels->ChannelName; ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Status')); ?>:</b>
	<?php echo CHtml::encode($data->Status); ?>
	<br />
	
	<b>Total Current Points:</b>
	<?php echo $data->subsCustPoints->Balance; ?>
	<br />

	<?php /*
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