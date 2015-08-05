<?php
/* @var $this RewardDetailsController */
/* @var $model RewardDetails */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'RewardConfigId'); ?>
		<?php echo $form->textField($model,'RewardConfigId',array('size'=>11,'maxlength'=>11)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'RewardId'); ?>
		<?php echo $form->textField($model,'RewardId',array('size'=>11,'maxlength'=>11)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'ChannelId'); ?>
		<?php echo $form->textField($model,'ChannelId'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'Limitations'); ?>
		<?php echo $form->textField($model,'Limitations',array('size'=>60,'maxlength'=>255)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'Value'); ?>
		<?php echo $form->textField($model,'Value',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'Availability'); ?>
		<?php echo $form->textField($model,'Availability'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'Status'); ?>
		<?php echo $form->textField($model,'Status',array('size'=>8,'maxlength'=>8)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'DateCreated'); ?>
		<?php echo $form->textField($model,'DateCreated'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'CreatedBy'); ?>
		<?php echo $form->textField($model,'CreatedBy'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'DateUpdated'); ?>
		<?php echo $form->textField($model,'DateUpdated'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'UpdatedBy'); ?>
		<?php echo $form->textField($model,'UpdatedBy'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'ClientId'); ?>
		<?php echo $form->textField($model,'ClientId'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'BrandId'); ?>
		<?php echo $form->textField($model,'BrandId'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'CampaignId'); ?>
		<?php echo $form->textField($model,'CampaignId'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->