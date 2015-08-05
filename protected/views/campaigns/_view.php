<?php
/* @var $this CampaignsController */
/* @var $data Campaigns */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('CampaignId')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->CampaignId), array('view', 'id'=>$data->CampaignId)); ?>
	<br />
	
	<b><?php echo CHtml::encode($data->getAttributeLabel('ClientId')); ?>:</b>
	<?php echo $data->campaignClients->CompanyName; ?>
	<br />
	
	<b><?php echo CHtml::encode($data->getAttributeLabel('BrandId')); ?>:</b>
	<?php echo $data->campaignBrands->BrandName; ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('CampaignName')); ?>:</b>
	<?php echo CHtml::encode($data->CampaignName); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Description')); ?>:</b>
	<?php echo CHtml::encode($data->Description); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('DurationFrom')); ?>:</b>
	<?php echo CHtml::encode($data->DurationFrom); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('DurationTo')); ?>:</b>
	<?php echo CHtml::encode($data->DurationTo); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Type')); ?>:</b>
	<?php echo CHtml::encode($data->Type); ?>
	<br />

	
	<b><?php echo CHtml::encode($data->getAttributeLabel('Status')); ?>:</b>
	<?php echo CHtml::encode($data->Status); ?>
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