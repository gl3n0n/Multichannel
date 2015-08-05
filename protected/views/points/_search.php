<?php
/* @var $this PointsController */
/* @var $model Points */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'PointsId'); ?>
		<?php echo $form->textField($model,'PointsId',array('size'=>11,'maxlength'=>11)); ?>
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
		<?php echo $form->label($model,'From'); ?>
		<?php echo $form->textField($model,'From'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'To'); ?>
		<?php echo $form->textField($model,'To'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'Value'); ?>
		<?php echo $form->textField($model,'Value',array('size'=>11,'maxlength'=>11)); ?>
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

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->