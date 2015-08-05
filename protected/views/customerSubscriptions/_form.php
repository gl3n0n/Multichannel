<?php
/* @var $this CustomerSubscriptionsController */
/* @var $model CustomerSubscriptions */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'customer-subscriptions-form',
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
		<?php echo $form->labelEx($model,'ClientId'); ?>
		<?php echo $form->textField($model,'ClientId'); ?>
		<?php echo $form->error($model,'ClientId'); ?>
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
		<?php echo $form->labelEx($model,'ChannelId'); ?>
		<?php echo $form->textField($model,'ChannelId',array('size'=>11,'maxlength'=>11)); ?>
		<?php echo $form->error($model,'ChannelId'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Status'); ?>
		<?php echo $form->textField($model,'Status',array('size'=>8,'maxlength'=>8)); ?>
		<?php echo $form->error($model,'Status'); ?>
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

	<div class="row">
		<?php echo $form->labelEx($model,'DateUpdated'); ?>
		<?php echo $form->textField($model,'DateUpdated'); ?>
		<?php echo $form->error($model,'DateUpdated'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'UpdatedBy'); ?>
		<?php echo $form->textField($model,'UpdatedBy'); ?>
		<?php echo $form->error($model,'UpdatedBy'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->