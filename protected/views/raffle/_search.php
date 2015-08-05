<?php
/* @var $this RaffleController */
/* @var $model Raffle */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'RaffleId'); ?>
		<?php echo $form->textField($model,'RaffleId',array('size'=>11,'maxlength'=>11)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'Source'); ?>
		<?php echo $form->textField($model,'Source',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'NoOfWinners'); ?>
		<?php echo $form->textField($model,'NoOfWinners'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'FdaNo'); ?>
		<?php echo $form->textField($model,'FdaNo',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'DrawDate'); ?>
		<?php echo $form->textField($model,'DrawDate'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'DateCreated'); ?>
		<?php echo $form->textField($model,'DateCreated'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'CreatedBy'); ?>
		<?php echo $form->textField($model,'CreatedBy',array('size'=>30,'maxlength'=>30)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'DateUpdated'); ?>
		<?php echo $form->textField($model,'DateUpdated'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'UpdatedBy'); ?>
		<?php echo $form->textField($model,'UpdatedBy',array('size'=>30,'maxlength'=>30)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'ClientId'); ?>
		<?php echo $form->textField($model,'ClientId',array('size'=>11,'maxlength'=>11)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'BrandId'); ?>
		<?php echo $form->textField($model,'BrandId',array('size'=>11,'maxlength'=>11)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'CampaignId'); ?>
		<?php echo $form->textField($model,'CampaignId',array('size'=>11,'maxlength'=>11)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'ChannelId'); ?>
		<?php echo $form->textField($model,'ChannelId',array('size'=>11,'maxlength'=>11)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'Status'); ?>
		<?php echo $form->textField($model,'Status',array('size'=>8,'maxlength'=>8)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'CouponId'); ?>
		<?php echo $form->textField($model,'CouponId',array('size'=>11,'maxlength'=>11)); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->