<?php
/* @var $this CouponController */
/* @var $model Coupon */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'CouponId'); ?>
		<?php echo $form->textField($model,'CouponId',array('size'=>11,'maxlength'=>11)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'Code'); ?>
		<?php echo $form->textField($model,'Code',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'Type'); ?>
		<?php echo $form->textField($model,'Type',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'TypeId'); ?>
		<?php echo $form->textField($model,'TypeId',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'Source'); ?>
		<?php echo $form->textField($model,'Source',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'ExpiryDate'); ?>
		<?php echo $form->textField($model,'ExpiryDate'); ?>
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
		<?php echo $form->label($model,'Image'); ?>
		<?php echo $form->textField($model,'Image',array('size'=>60,'maxlength'=>200)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'Quantity'); ?>
		<?php echo $form->textField($model,'Quantity',array('size'=>11,'maxlength'=>11)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'LimitPerUser'); ?>
		<?php echo $form->textField($model,'LimitPerUser',array('size'=>11,'maxlength'=>11)); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->