<?php
/* @var $this RewardDetailsController */
/* @var $data RewardDetails */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('RewardConfigId')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->RewardConfigId), array('view', 'id'=>$data->RewardConfigId)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('RewardId')); ?>:</b>
	<?php echo CHtml::encode($data->RewardId); ?>
	<?php echo $data->rdetailRewardslists->Title; ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('ChannelId')); ?>:</b>
	<?php echo $data->rdetailChannels->ChannelName; ?>
	<br />
	
	<b><?php echo CHtml::encode($data->getAttributeLabel('Inventory')); ?>:</b>
	<?php echo CHtml::encode($data->Inventory); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Limitations')); ?>:</b>
	<?php echo CHtml::encode($data->Limitations); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Value')); ?>:</b>
	<?php echo CHtml::encode($data->Value); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Availability')); ?>:</b>
	<?php echo CHtml::encode($data->Availability); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Status')); ?>:</b>
	<?php echo CHtml::encode($data->Status); ?>
	<br />

</div>