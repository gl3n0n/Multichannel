<?php
/* @var $this PointsLogController */
/* @var $model PointsLog */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'points-log-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'CustomerId'); ?>
		<?php echo $form->textField($model,'CustomerId',array('size'=>11,'maxlength'=>11)); ?>
		<?php echo $form->error($model,'CustomerId'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'SubscriptionId'); ?>
		<?php echo $form->textField($model,'SubscriptionId'); ?>
		<?php echo $form->error($model,'SubscriptionId'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'BrandId'); ?>
		<?php echo $form->textField($model,'BrandId',array('size'=>11,'maxlength'=>11)); ?>
		<?php echo $form->error($model,'BrandId'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'CampaignId'); ?>
		<?php echo $form->textField($model,'CampaignId',array('size'=>11,'maxlength'=>11)); ?>
		<?php echo $form->error($model,'CampaignId'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'PointsId'); ?>
		<?php echo $form->textField($model,'PointsId',array('size'=>11,'maxlength'=>11)); ?>
		<?php echo $form->error($model,'PointsId'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'DateCreated'); ?>
		<?php echo $form->textField($model,'DateCreated'); ?>
		<?php echo $form->error($model,'DateCreated'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'CreatedBy'); ?>
		<?php echo $form->textField($model,'CreatedBy'); ?>
		<?php echo $form->error($model,'CreatedBy'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->