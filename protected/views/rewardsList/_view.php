<?php
/* @var $this RewardsListController */
/* @var $data RewardsList */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('RewardId')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->RewardId), array('view', 'id'=>$data->RewardId)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Title')); ?>:</b>
	<?php echo CHtml::encode($data->Title); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Description')); ?>:</b>
	<?php echo CHtml::encode($data->Description); ?>
	<br />
	
	<b><?php echo CHtml::encode($data->getAttributeLabel('Image')); ?>:</b>
	<img src="<?php echo CHtml::encode($data->Image); ?>" border="0">
	<br />


	<b><?php echo CHtml::encode($data->getAttributeLabel('Availability')); ?>:</b>
	<?php echo CHtml::encode($data->Availability); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Status')); ?>:</b>
	<?php echo CHtml::encode($data->Status); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('DateCreated')); ?>:</b>
	<?php echo CHtml::encode($data->DateCreated); ?>
	<br />

	
</div>