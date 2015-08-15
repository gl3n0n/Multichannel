<?php
/* @var $this CustomersController */
/* @var $model Customers */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'customers-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'FirstName'); ?>
		<?php echo $form->textField($model,'FirstName',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'FirstName'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'MiddleName'); ?>
		<?php echo $form->textField($model,'MiddleName',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'MiddleName'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'LastName'); ?>
		<?php echo $form->textField($model,'LastName',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'LastName'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Gender'); ?>
		<?php echo $form->textField($model,'Gender',array('size'=>1,'maxlength'=>1)); ?>
		<?php echo $form->error($model,'Gender'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'ContactNumber'); ?>
		<?php echo $form->textField($model,'ContactNumber',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'ContactNumber'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Address'); ?>
		<?php echo $form->textField($model,'Address',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'Address'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Email'); ?>
		<?php echo $form->textField($model,'Email',array('size'=>30,'maxlength'=>30)); ?>
		<?php echo $form->error($model,'Email'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'BirthDate'); ?>
		<?php echo $form->textField($model,'BirthDate',array('size'=>30,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'BirthDate'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'FBId'); ?>
		<?php echo $form->textField($model,'FBId',array('size'=>30,'maxlength'=>30)); ?>
		<?php echo $form->error($model,'FBId'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'TwitterHandle'); ?>
		<?php echo $form->textField($model,'TwitterHandle',array('size'=>30,'maxlength'=>30)); ?>
		<?php echo $form->error($model,'TwitterHandle'); ?>
	</div>

	<div class="row">
		<?php echo $form->hiddenField($model,'Status',array('value'=>'ACTIVE')); ?>
	</div>
	<!--//
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
	//-->
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->