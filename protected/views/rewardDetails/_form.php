<?php
/* @var $this RewardDetailsController */
/* @var $model RewardDetails */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'reward-details-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>
    <?php if($model->scenario === 'insert'): // These are displayed when user is creating a new reward details. ?>
	<div class="row">
		<?php echo $form->labelEx($model,'RewardId'); ?>
		<?php echo $form->dropDownList($model,'RewardId',$rewards_list); ?>
		<?php echo $form->error($model,'RewardId'); ?>
	</div>	
	
	<div class="row">
		<?php echo $form->labelEx($model,'PointsId'); ?>
		<?php echo $form->dropDownList($model,'PointsId',$points_list); ?>
		<?php echo $form->error($model,'PointsId'); ?>
	</div>
	<?php else: // End Create scenario ?>	
	<div class="row">
		<?php echo $form->labelEx($model,'RewardId'); ?>
        <?php echo $form->textField($model,'CodeLength',array('size'=>20,'maxlength'=>20,'disabled'=>true, 'value'=>$model->byRewards->Title)); ?>
        <?php echo $form->error($model,'RewardId'); ?>
	  </div>
	  
	  <div class="row">
		<?php echo $form->labelEx($model,'PointsId'); ?>
        <?php echo $form->textField($model,'CodeLength',array('size'=>20,'maxlength'=>20,'disabled'=>true, 'value'=>$model->byPointsSystem->Name)); ?>
        <?php echo $form->error($model,'PointsId'); ?>
	  </div>
	<?php endif; // End Create scenario ?>

	<div class="row">
		<?php echo $form->labelEx($model,'Name'); ?>
		<?php echo $form->textField($model,'Name',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'Name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Inventory'); ?>
		<?php echo $form->textField($model,'Inventory'); ?>
		<?php echo $form->error($model,'Inventory'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Limitations'); ?>
		<?php echo $form->textField($model,'Limitations',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'Limitations'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Value'); ?>
		<?php echo $form->textField($model,'Value',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'Value'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'StartDate'); ?>
		<?php
			$this->widget('zii.widgets.jui.CJuiDatePicker', array(
           'name' => 'StartDate',
		   'model'=>$model,
			'attribute'=>'StartDate',
           // additional javascript options for the date picker plugin
           'options' => array(
               'showAnim' => "slideDown",
               'changeMonth' => true,
               'numberOfMonths' => 1,
               'showOn' => "button",
               'buttonImageOnly' => false,
               'dateFormat' => "yy-mm-dd",
               'showButtonPanel' => true,
               'onClose' => 'js:function(selectedDate) { $("#EndDate").datepicker("option", "minDate", selectedDate); }',            
           )
       ));	?>
		<?php echo $form->error($model,'StartDate'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'EndDate'); ?>
		<?php
			 $this->widget('zii.widgets.jui.CJuiDatePicker', array(
           'name' => 'EndDate',
		   'model'=>$model,
			'attribute'=>'EndDate',
           // additional javascript options for the date picker plugin
           'options' => array(
               'showAnim' => "slideDown",
               'changeMonth' => true,
               'numberOfMonths' => 1,
               'showOn' => "button",
               'buttonImageOnly' => false,
               'dateFormat' => "yy-mm-dd",
               'showButtonPanel' => true,
               'onClose' => 'js:function(selectedDate) { $("#StartDate").datepicker("option", "maxDate", selectedDate); }',
           )
       ));	?>
		<?php echo $form->error($model,'EndDate'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Status'); ?>
		<?php echo CHtml::dropDownList('RewardDetails[Status]', 
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