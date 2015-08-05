<?php
/* @var $this RaffleController */
/* @var $data Raffle */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('RaffleId')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->RaffleId), array('view', 'id'=>$data->RaffleId)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Source')); ?>:</b>
	<?php echo CHtml::encode($data->Source); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('NoOfWinners')); ?>:</b>
	<?php echo CHtml::encode($data->NoOfWinners); ?>
	<br />
	
	<b><?php echo CHtml::encode($data->getAttributeLabel('BackUp')); ?>:</b>
	<?php echo CHtml::encode($data->BackUp); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Promo Permit No')); ?>:</b>
	<?php echo CHtml::encode($data->FdaNo); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('DrawDate')); ?>:</b>
	<?php echo CHtml::encode($data->DrawDate); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('DateCreated')); ?>:</b>
	<?php echo CHtml::encode($data->DateCreated); ?>
	<br />


	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('DateUpdated')); ?>:</b>
	<?php echo CHtml::encode($data->DateUpdated); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('UpdatedBy')); ?>:</b>
	<?php echo CHtml::encode($data->UpdatedBy); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('ClientId')); ?>:</b>
	<?php echo CHtml::encode($data->ClientId); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('BrandId')); ?>:</b>
	<?php echo CHtml::encode($data->BrandId); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('CampaignId')); ?>:</b>
	<?php echo CHtml::encode($data->CampaignId); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('ChannelId')); ?>:</b>
	<?php echo CHtml::encode($data->ChannelId); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Status')); ?>:</b>
	<?php echo CHtml::encode($data->Status); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('CouponId')); ?>:</b>
	<?php echo CHtml::encode($data->CouponId); ?>
	<br />

	*/ ?>

</div>