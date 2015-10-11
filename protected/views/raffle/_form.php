<?php
/* @var $this RaffleController */
/* @var $model Raffle */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'raffle-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>


	<div class="row">
		<?php echo $form->labelEx($model,'CouponId'); ?>
		<?php echo $form->dropDownList($model,'CouponId',$coupon_list); ?>
		<?php echo $form->error($model,'CouponId'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'RaffleName'); ?>
		<?php echo $form->textField($model,'RaffleName',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'RaffleName'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'Source'); ?>
		<?php echo $form->textField($model,'Source',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'Source'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'NoOfWinners'); ?>
		<?php echo $form->textField($model,'NoOfWinners'); ?>
		<?php echo $form->error($model,'NoOfWinners'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'BackUp'); ?>
		<?php echo $form->textField($model,'BackUp'); ?>
		<?php echo $form->error($model,'BackUp'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Promo Permit No'); ?>
		<?php echo $form->textField($model,'FdaNo',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'FdaNo'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'DrawDate'); ?>
		<?php
			$this->widget('zii.widgets.jui.CJuiDatePicker', array(
           'name' => 'DrawDate',
		   'model'=>$model,
			'attribute'=>'DrawDate',
           // additional javascript options for the date picker plugin
           'options' => array(
               'showAnim' => "slideDown",
               'changeMonth' => true,
		'changeYear' => true,
               'numberOfMonths' => 1,
               'showOn' => "button",
               'buttonImageOnly' => false,
               'dateFormat' => "yy-mm-dd",
               'showButtonPanel' => true      
           )
       ));	?>
		<?php echo $form->error($model,'DrawDate'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'Status'); ?>
		<?php echo CHtml::dropDownList('Raffle[Status]', 
			$model->scenario !== 'update' ? $model->Status : 'PENDING', 
			ZHtml::enumItem($model, 'Status'),
			array('disabled' => ($model->scenario==='update') ? true: false)); 
		?>
		<?php echo $form->error($model,'Status'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
