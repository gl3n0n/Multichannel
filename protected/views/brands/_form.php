<?php
/* @var $this BrandsController */
/* @var $model Brands */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'brands-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

    <?php if(Yii::app()->user->AccessType === 'SUPERADMIN' && $model->scenario === 'insert'): ?>
    <div class="row">
        <?php echo $form->labelEx($model,'ClientId'); ?>
        <?php echo $form->dropDownList($model,'ClientId',$client_list); ?>
        <?php echo $form->error($model,'ClientId'); ?>
    </div>
    <?php endif; ?>

	<div class="row">
		<?php echo $form->labelEx($model,'BrandName'); ?>
		<?php echo $form->textField($model,'BrandName',array('size'=>60,'maxlength'=>100)); ?>
		<?php echo $form->error($model,'BrandName'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Description'); ?>
		<?php echo $form->textField($model,'Description',array('size'=>60,'maxlength'=>100)); ?>
		<?php echo $form->error($model,'Description'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'DurationFrom'); ?>
		<?php
			$this->widget('zii.widgets.jui.CJuiDatePicker', array(
           'name' => 'DurationFrom',
		   'model'=>$model,
			'attribute'=>'DurationFrom',
           // additional javascript options for the date picker plugin
           'options' => array(
               'showAnim' => "slideDown",
               'changeMonth' => true,
               'numberOfMonths' => 1,
               'showOn' => "button",
               'buttonImageOnly' => false,
               'dateFormat' => "yy-mm-dd",
               'showButtonPanel' => true,
               'onClose' => 'js:function(selectedDate) { $("#DurationTo").datepicker("option", "minDate", selectedDate); }',            
           )
       ));	?>
		<?php echo $form->error($model,'DurationFrom'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'DurationTo'); ?>
		<?php
			 $this->widget('zii.widgets.jui.CJuiDatePicker', array(
           'name' => 'DurationTo',
		   'model'=>$model,
			'attribute'=>'DurationTo',
           // additional javascript options for the date picker plugin
           'options' => array(
               'showAnim' => "slideDown",
               'changeMonth' => true,
               'numberOfMonths' => 1,
               'showOn' => "button",
               'buttonImageOnly' => false,
               'dateFormat' => "yy-mm-dd",
               'showButtonPanel' => true,
               'onClose' => 'js:function(selectedDate) { $("#DurationFrom").datepicker("option", "maxDate", selectedDate); }',
           )
       ));	?>
		<?php echo $form->error($model,'DurationTo'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Status'); ?>
		<?php echo ZHtml::enumDropDownList(Brands::model(), 'Status', array(
    'id'=>'search-status',
    'name'=>'Brands[Status]',
    'value'=>'',
)); ?>
		<?php echo $form->error($model,'Status'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->