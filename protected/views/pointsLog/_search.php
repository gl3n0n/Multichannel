<?php
/* @var $this PointsLogController */
/* @var $model PointsLog */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

<?php if($model->scenario === 'searchi'): ?>
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

<?php else: ?>
	<div class="row">
		<?php echo $form->label($model,'PointLogId'); ?>
		<?php echo $form->textField($model,'PointLogId',array('size'=>11,'maxlength'=>11)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'CustomerId'); ?>
		<?php echo $form->textField($model,'CustomerId',array('size'=>11,'maxlength'=>11)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'SubscriptionId'); ?>
		<?php echo $form->textField($model,'SubscriptionId'); ?>
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
		<?php echo $form->label($model,'PointsId'); ?>
		<?php echo $form->textField($model,'PointsId',array('size'=>11,'maxlength'=>11)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'DateCreated'); ?>
		<?php echo $form->textField($model,'DateCreated'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'CreatedBy'); ?>
		<?php echo $form->textField($model,'CreatedBy'); ?>
	</div>
<?php endif; ?>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->