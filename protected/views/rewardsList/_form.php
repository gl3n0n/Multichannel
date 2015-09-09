<?php
/* @var $this RewardsListController */
/* @var $model RewardsList */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'rewards-list-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
	'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'Title'); ?>
		<?php echo $form->textField($model,'Title',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'Title'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Description'); ?>
		<?php echo $form->textField($model,'Description',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'Description'); ?>
	</div>

	<?php if($model->scenario === 'insert'): // These are displayed when user is creating a new coupon. ?>
	<div class="row">
		<?php echo $form->labelEx($model,'Image'); ?>		
		<?php echo $form->fileField($model,'Image', array('class'=>'input-file')); ?>
		<?php echo $form->error($model,'Image'); ?>

	</div>
	<?php endif; // End Create scenario ?>

		<div class="row">
		<?php echo $form->labelEx($model,'Availability'); ?>
		<?php
			$this->widget('zii.widgets.jui.CJuiDatePicker', array(
		   		'model'=>$model,
				'attribute'=>'Availability',
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
           	));
       	?>
		<?php echo $form->error($model,'Availability'); ?>
	</div>

<div class="row">
    <?php echo $form->labelEx($model,'Status'); ?>
    <?php echo CHtml::dropDownList('RewardsList[Status]', 
        $model->scenario === 'update' ? $model->Status : 'ACTIVE', 
        ZHtml::enumItem($model, 'Status')); 
    ?>
    <?php echo $form->error($model,'Status'); ?>
</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
